<?php
// menu.php
require_once 'functions.php';
require_login();

$res = $mysqli->query("SELECT product_id, product_name, price, IFNULL(image_path,'') AS image_path FROM products ORDER BY product_name");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Menu</title></head>
<body>
<h2>Menu - Items to Sell</h2>
<p><a href="Final_landpage.php">Back to Landing</a> | <a href="cart.php">Cart</a></p>
<div style="display:flex; flex-wrap:wrap; gap:12px;">
<?php while ($row = $res->fetch_assoc()): ?>
    <div style="border:1px solid #ddd; padding:10px; width:240px;">
        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="" style="width:100%;height:140px;object-fit:cover;">
        <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
        <p>₱<?php echo number_format($row['price'],2); ?></p>
        <form method="post" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            Qty <input type="number" name="qty" value="1" min="1" style="width:60px;">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
<?php endwhile; ?>
</div>
</body>
</html>
