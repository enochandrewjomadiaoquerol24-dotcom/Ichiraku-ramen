<?php

require_once 'functions.php';
require_login();

$stmt = $mysqli->prepare("SELECT order_id, order_date, total_amount FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $_SESSION['customer_id']);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>My Orders</title></head>
<body>
<h2>My Orders</h2>
<p><a href="Final_landpage.php">Back</a></p>
<?php while ($row = $res->fetch_assoc()): ?>
    <div style="border:1px solid #ddd; padding:8px; margin:8px 0;">
        <strong><?php echo htmlspecialchars($row['order_id']); ?></strong><br>
        Date: <?php echo htmlspecialchars($row['order_date']); ?><br>
        Total: ₱<?php echo number_format($row['total_amount'],2); ?><br>
        <a href="order_summary.php?order_id=<?php echo urlencode($row['order_id']); ?>">View</a>
    </div>
<?php endwhile; ?>
</body>
</html>
