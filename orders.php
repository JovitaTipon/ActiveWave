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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/x-icon" href="/images/activewavelogo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f9fafb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .order-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 2rem;
    }
    .order-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .order-card h5 {
      color: #1377b2;
    }
    .status {
      font-weight: 600;
      color: #ee6927;
    }
    .navbar-brand img {
      height: 60px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  < <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.html">Active Wave</a>
      <div class="ms-auto">
        <a href="orders.php" class="btn btn-outline-secondary me-2">📦 Orders</a>
        <a href="cart.html" class="btn btn-outline-primary">🛒 Cart</a>
      </div>
    </div>
  </nav>

  <div class="order-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Order History</h2>
      <a href="index.html" class="btn btn-outline-primary">&larr; Back to Store</a>
    </div>

    <?php if (count($orders) === 0): ?>
      <div class="alert alert-info">No orders placed yet.</div>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <h5>Order #<?= htmlspecialchars($order['order_id']) ?></h5>
          <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($order['status']) ?></span></p>
          <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
          <p><strong>Payment Ref:</strong> <?= htmlspecialchars($order['payment_reference']) ?></p>
          <p><strong>Date:</strong> <?= htmlspecialchars($order['timestamp']) ?></p>
          <p><strong>Items:</strong></p>
          <ul>
            <?php foreach ($order['items'] as $item): ?>
              <li><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?> ($<?= number_format($item['price'], 2) ?>)</li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
