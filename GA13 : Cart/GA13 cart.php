<?php

require_once 'functions.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmt = $mysqli->prepare("SELECT product_id, product_name, price, IFNULL(image_path,'') AS image_path FROM products WHERE product_id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $rqty = $cart[$r['product_id']];
        $r['qty'] = $rqty;
        $r['subtotal'] = $r['price'] * $rqty;
        $items[] = $r;
        $total += $r['subtotal'];
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Cart</title></head>
<body>
<h2>Your Cart</h2>
<p><a href="menu.php">Continue Shopping</a> | <a href="Final_landpage.php">Landing</a></p>

<?php if (empty($items)): ?>
    <p>Cart empty.</p>
<?php else: ?>
    <table border="1" cellpadding="6">
        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach($items as $it): ?>
            <tr>
                <td><?php echo htmlspecialchars($it['product_name']); ?></td>
                <td><?php echo $it['qty']; ?></td>
                <td>₱<?php echo number_format($it['price'],2); ?></td>
                <td>₱<?php echo number_format($it['subtotal'],2); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" align="right"><strong>Total</strong></td><td>₱<?php echo number_format($total,2); ?></td></tr>
    </table>

    <form method="post" action="order_summary.php" style="margin-top:10px;">
        <button type="submit">Proceed to Checkout</button>
    </form>
<?php endif; ?>
</body>
</html>
