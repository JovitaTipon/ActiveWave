<?php
$orders = [];

$logFile = 'orders.log';

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $orders[] = json_decode($line, true);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order History</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #e8f4f3;
      padding: 40px;
    }
    .order-card {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    h2 {
      color: #1377b2;
    }
    .status {
      font-weight: bold;
      color: #ee6927;
    }
  </style>
</head>

<body>
<nav class="navbar">
  <div class="nav-left">
    <h1>Active Gear</h1>
  </div>
  <div class="nav-right">
    <a href="orders.php" class="btn btn-secondary">📦 Orders</a>
    <a href="cart.html" class="btn btn-cart">🛒 Cart</a>
  </div>
</nav>

  <h2>Order History</h2>
  <a href="index.html" class="btn btn-primary" style="margin-bottom: 20px;">← Back to Store</a>

  <?php if (count($orders) === 0): ?>
    <p>No orders placed yet.</p>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <div class="order-card">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($order['status']) ?></span></p>
        <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
        <p><strong>Payment Ref:</strong> <?= htmlspecialchars($order['payment_reference']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['timestamp']) ?></p>
        <p><strong>Items:</strong></p>
        <ul>
          <?php foreach ($order['items'] as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?> ($<?= $item['price'] ?>)</li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
