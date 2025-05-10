<?php
session_start();

//Function to update an existing order in the log
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
            $newLines[] = json_encode($updatedOrder); //Update the entry
            $found = true;
        } else {
            $newLines[] = $line;
        }
    }

    if (!$found) {
        $newLines[] = json_encode($updatedOrder); // Fallback if order not in log
    }

    file_put_contents($logFile, implode(PHP_EOL, $newLines) . PHP_EOL);
}

if (isset($_SESSION['order'])) {
    //Update order status and flags
    $_SESSION['order']['status'] = 'shipped';
    $_SESSION['order']['email_sent'] = true;

    //Format the item list for email (Zapier)
    $itemsList = "";
    foreach ($_SESSION['order']['items'] as $item) {
        $itemsList .= "- {$item['name']} ×{$item['quantity']} (${$item['price']})\n";
    }
    $_SESSION['order']['items_list'] = $itemsList;

    //Update log record
    $log = $_SESSION['order'];
    $log['timestamp'] = date('Y-m-d H:i:s');

    updateOrderLog($log);

    //Send data to Zapier webhook
    $zapierWebhookUrl = 'zapier url placeholder';
    file_get_contents($zapierWebhookUrl, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($log)
        ]
    ]));

    //Redirect to order status page
    header("Location: orders.php");
    exit;
}



