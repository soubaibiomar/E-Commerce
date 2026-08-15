<?php
/**
 * ZeyTech — 3-State High-Concurrency Inventory Reservation API (Phase 3)
 * Exact contract for manageInventoryReservation tool node.
 * Actions: check | reserve | release
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$action = trim($input['action'] ?? 'check');
$productId = intval($input['productId'] ?? 0);
$quantity = max(1, intval($input['quantity'] ?? 1));

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Valid productId is required']);
    exit();
}

try {
    // 1. Action: check (No mutation)
    if ($action === 'check') {
        $inv = db_fetch_one("SELECT * FROM inventory WHERE product_id = ?", [$productId], "i");
        if (!$inv) {
            // Check if product exists
            $prod = db_fetch_one("SELECT id FROM products WHERE id = ?", [$productId], "i");
            if (!$prod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Product not found']);
                exit();
            }
            // Seed inventory if missing
            db_execute("INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty) VALUES (?, 100, 0, 0)", [$productId], "i");
            $inv = ['available_qty' => 100, 'reserved_qty' => 0, 'sold_qty' => 0];
        }

        echo json_encode([
            'success' => true,
            'action' => 'check',
            'productId' => $productId,
            'stockAvailable' => intval($inv['available_qty']),
            'stockReserved' => intval($inv['reserved_qty']),
            'stockSold' => intval($inv['sold_qty']),
            'canReserve' => (intval($inv['available_qty']) >= $quantity)
        ]);
        exit();
    }

    // 2. Action: reserve (Atomic reservation with row-level safety)
    if ($action === 'reserve') {
        $con = get_db_connection();
        mysqli_begin_transaction($con);

        // Fetch with FOR UPDATE row lock
        $stmt = mysqli_prepare($con, "SELECT available_qty, reserved_qty FROM inventory WHERE product_id = ? FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$row) {
            mysqli_rollback($con);
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Product inventory not found']);
            exit();
        }

        $available = intval($row['available_qty']);
        if ($available < $quantity) {
            mysqli_rollback($con);
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'INSUFFICIENT_STOCK',
                'message' => "Insufficient stock: requested {$quantity}, only {$available} available.",
                'stockAvailable' => $available
            ]);
            exit();
        }

        // Perform atomic update
        $upd = mysqli_prepare($con, "UPDATE inventory SET available_qty = available_qty - ?, reserved_qty = reserved_qty + ? WHERE product_id = ? AND available_qty >= ?");
        mysqli_stmt_bind_param($upd, "iiii", $quantity, $quantity, $productId, $quantity);
        $exec = mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);

        if (!$exec) {
            mysqli_rollback($con);
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'CONCURRENCY_CONFLICT', 'message' => 'Could not lock inventory']);
            exit();
        }

        // Also update products mirror table for backward compatibility
        @mysqli_query($con, "UPDATE products SET stockAvailable = stockAvailable - {$quantity}, stockReserved = stockReserved + {$quantity} WHERE id = {$productId}");

        mysqli_commit($con);

        $resId = 'res_' . date('YmdHis') . '_' . bin2hex(random_bytes(4));
        echo json_encode([
            'success' => true,
            'action' => 'reserve',
            'reservationId' => $resId,
            'productId' => $productId,
            'reservedQuantity' => $quantity,
            'stockAvailable' => $available - $quantity,
            'stockReserved' => intval($row['reserved_qty']) + $quantity,
            'expiresInMinutes' => 30
        ]);
        exit();
    }

    // 3. Action: release (Move reserved stock back to available)
    if ($action === 'release') {
        $con = get_db_connection();
        mysqli_begin_transaction($con);

        $stmt = mysqli_prepare($con, "SELECT reserved_qty FROM inventory WHERE product_id = ? FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$row) {
            mysqli_rollback($con);
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Product inventory not found']);
            exit();
        }

        $currentReserved = intval($row['reserved_qty']);
        $actualRelease = min($quantity, $currentReserved);

        $upd = mysqli_prepare($con, "UPDATE inventory SET available_qty = available_qty + ?, reserved_qty = GREATEST(0, reserved_qty - ?) WHERE product_id = ?");
        mysqli_stmt_bind_param($upd, "iii", $actualRelease, $actualRelease, $productId);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);

        @mysqli_query($con, "UPDATE products SET stockAvailable = stockAvailable + {$actualRelease}, stockReserved = GREATEST(0, stockReserved - {$actualRelease}) WHERE id = {$productId}");

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'action' => 'release',
            'productId' => $productId,
            'releasedQuantity' => $actualRelease
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action: must be check, reserve, or release']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
