<?php

require_once 'functions.php';
require_login();

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header('Location: cart.php');
    exit;
}

$stmt = $mysqli->prepare("SELECT o.order_id, o.order_date, o.total_amount, c.customer_name FROM orders o JOIN customers_info c ON o.customer_id = c.customer_id WHERE o.order_id = ? AND o.customer_id = ?");
$stmt->bind_param('si', $order_id, $_SESSION['customer_id']);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
if (!$order) {
    die('Order not found or not yours.');
}

$stmt = $mysqli->prepare("SELECT od.quantity, od.price_each, p.product_name FROM order_details od JOIN products p ON od.product_id = p.product_id WHERE od.order_id = ?");
$stmt->bind_param('s', $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Order <?php echo htmlspecialchars($order_id); ?></title></head>
<body>
<h2>Order Summary: <?php echo htmlspecialchars($order['order_id']); ?></h2>
<p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
<p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?></p>
<ul>
<?php while ($it = $items->fetch_assoc()): ?>
    <li><?php echo htmlspecialchars($it['product_name']) . ' x' . $it['quantity'] . ' — ₱' . number_format($it['price_each'] * $it['quantity'],2); ?></li>
<?php endwhile; ?>
</ul>
<p><b>Total: ₱<?php echo number_format($order['total_amount'],2); ?></b></p>
<p><a href="Final_landpage.php">Back to Home</a></p>
</body>
</html>
