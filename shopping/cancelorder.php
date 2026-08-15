<?php
session_start();
include_once('includes/config.php');

if (empty($_SESSION['login']) || empty($_SESSION['id'])) {
    header('location:login.php');
    exit();
}

$userId = intval($_SESSION['id']);
$orderid = intval($_GET['oid'] ?? $_GET['id'] ?? 0);

// Verify order exists and belongs to logged in user
$order = db_fetch_one("SELECT id, orderStatus FROM orders WHERE id=? AND userId=?", [$orderid, $userId], "ii");
if (!$order) {
    die("Order not found or unauthorized access.");
}

$currentStatus = $order['orderStatus'] ?? '';

if (isset($_POST['submit'])) {
    // Only allow cancellation if order is not delivered or already cancelled
    if ($currentStatus !== 'Delivered' && $currentStatus !== 'Cancelled' && $currentStatus !== 'Out For Delivery') {
        $ressta = "Cancelled";
        $remark = trim($_POST['restremark'] ?? '');
        
        db_query("INSERT INTO ordertrackhistory(orderId, status, remark) VALUES(?, ?, ?)", [$orderid, $ressta, $remark], "iss");
        db_query("UPDATE orders SET orderStatus=? WHERE id=? AND userId=?", [$ressta, $orderid, $userId], "sii");
        
        echo '<script>alert("Your order has been cancelled."); window.opener.location.reload(); window.close();</script>';
        exit();
    } else {
        echo '<script>alert("This order cannot be cancelled in its current state.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order Cancellation</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body style="padding: 20px;">

<div>
  <h3>Cancel Order #<?php echo e($order['id']); ?></h3>
  <table class="table table-bordered text-center" style="margin-top: 15px;">
    <tr>
      <th>Order ID</th>
      <th>Current Status</th>
    </tr>
    <tr> 
      <td>#<?php echo e($order['id']); ?></td> 
      <td><?php echo e(empty($currentStatus) ? "Waiting for confirmation" : $currentStatus); ?></td> 
    </tr>
  </table>

  <?php if ($currentStatus == "" || $currentStatus == "Packed" || $currentStatus == "Dispatched" || $currentStatus == "In Transit" || $currentStatus == "in Process") { ?>
    <form method="post" style="margin-top: 20px;">
      <div class="form-group">
        <label><strong>Reason for Cancellation:</strong></label>
        <textarea name="restremark" rows="5" class="form-control" required="required" placeholder="Please provide cancellation reason..."></textarea>
      </div>
      <button type="submit" name="submit" class="btn btn-danger">Confirm Order Cancellation</button>
    </form>
  <?php } else { ?>
    <?php if ($currentStatus == 'Cancelled') { ?>
      <div class="alert alert-info">This order is already cancelled.</div>
    <?php } else { ?>
      <div class="alert alert-warning">This order cannot be cancelled because it is already out for delivery or delivered.</div>
    <?php } ?>
  <?php } ?>
</div>

</body>
</html>