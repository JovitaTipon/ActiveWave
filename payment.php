<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51RNGZhPTWBp2CLnU8Wunlr1T4G1VoRZHxhN5cYJd7MyCXtcPZwI1A9rYvZxEmnf4TJewYcCy3XUjSBX7NMvTC3Rx00ie1VBH0Q');

// Read data from frontend
$order = json_decode(file_get_contents('php://input'), true);

// Start session
session_start();

$order_id = uniqid('ORDER_');
$total = array_reduce($order['cart'], fn($sum, $i) => $sum + $i['price'] * $i['quantity'], 0);

// Store order in session with status "created"
$_SESSION['order'] = [
    'order_id' => $order_id,
    'status' => 'created',
    'payment_reference' => null, // will be added below
    'email' => $order['email'],
    'items' => $order['cart'],
    'total' => $total,
    'dispatch_slip_status' => null,
    'email_sent' => false
];

// Build Stripe line items
$line_items = [];
foreach ($order['cart'] as $item) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => ['name' => $item['name']],
            'unit_amount' => $item['price'] * 100,
        ],
        'quantity' => $item['quantity'],
    ];
}

// Create Stripe Checkout session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://localhost:8000/success.html',
    'cancel_url' => 'http://localhost:8000/cart.html',
]);

//Save Stripe session ID (reference)
$_SESSION['order']['payment_reference'] = $session->id;

//Rebuild log entry with Stripe ref
$orderCreatedLog = $_SESSION['order'];
$orderCreatedLog['timestamp'] = date('Y-m-d H:i:s');

//Save to log
file_put_contents('orders.log', json_encode($orderCreatedLog) . PHP_EOL, FILE_APPEND);

//Return Stripe URL
echo json_encode(['url' => $session->url]);





