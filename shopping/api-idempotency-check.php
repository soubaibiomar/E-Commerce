<?php
/**
 * ZeyTech — Atomic Idempotency Check API (Phase 4)
 * Exact contract for idempotency-check node.
 * Accepts: { eventId, eventType }
 * Returns: { alreadyProcessed: boolean }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$eventId = trim($input['eventId'] ?? '');
$eventType = trim($input['eventType'] ?? 'UNKNOWN_EVENT');

if (empty($eventId)) {
    http_response_code(400);
    echo json_encode(['alreadyProcessed' => false, 'error' => 'eventId is required']);
    exit();
}

try {
    // Attempt direct atomic insert
    db_execute(
        "INSERT INTO idempotency_keys (event_id, event_type, processed_at) VALUES (?, ?, NOW())",
        [$eventId, $eventType],
        "ss"
    );

    echo json_encode([
        'alreadyProcessed' => false,
        'eventId' => $eventId,
        'eventType' => $eventType,
        'status' => 'NEW_EVENT_REGISTERED'
    ]);

} catch (Throwable $e) {
    // Check if error is Duplicate Entry (MySQL Error Code 1062)
    if ($e->getCode() == 1062 || stripos($e->getMessage(), 'Duplicate entry') !== false) {
        echo json_encode([
            'alreadyProcessed' => true,
            'eventId' => $eventId,
            'eventType' => $eventType,
            'status' => 'DUPLICATE_EVENT_DETECTED'
        ]);
        exit();
    }

    // Other errors
    echo json_encode(['alreadyProcessed' => false, 'error' => $e->getMessage()]);
}
