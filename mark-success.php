<?php
session_start();

//Replaces/updates a single order entry in orders.log
function updateOrderLog($updatedOrder) {
    $logFile = 'orders.log';
    $orderId = $updatedOrder['order_id'];

    if (!file_exists($logFile)) return;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newLines = [];
    $found = false;

    foreach ($lines as $line) {
        $order = json_decode($line, true);
        if ($order['order_id'] === $orderId) {
            $newLines[] = json_encode($updatedOrder); //Replace existing order
            $found = true;
        } else {
            $newLines[] = $line;
        }
    }

    if (!$found) {
        $newLines[] = json_encode($updatedOrder); // (optional) Add if not found
    }

    file_put_contents($logFile, implode(PHP_EOL, $newLines) . PHP_EOL);
}

//Update session and log
if (isset($_SESSION['order'])) {
    // Step 1: Mark as accepted after payment
    $_SESSION['order']['status'] = 'accepted';

    // Step 2: Simulate creation of dispatch slip
    $_SESSION['order']['dispatch_slip_status'] = 'pending';

    // Step 3: Email not sent yet
    $_SESSION['order']['email_sent'] = false;

    // Save updated state to log (overwriting the previous entry)
    $log = $_SESSION['order'];
    $log['timestamp'] = date('Y-m-d H:i:s');

    updateOrderLog($log); //Replaces previous log entry
}


