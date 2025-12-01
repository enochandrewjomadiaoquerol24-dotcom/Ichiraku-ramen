<?php 

require_once "config.php"; 
require_once "functions.php"; 
session_start();


if (!isset($_GET['order_id'])) {
    die("No order ID provided.");
}
$order_id = $_GET['order_id'];


$stmt = $conn->prepare("
    SELECT o.order_id, o.customer_id, o.order_date, o.total_amount,
           c.customer_name, c.customer_address, c.contact_number
    FROM orders o
    JOIN customers_info c ON o.customer_id = c.customer_id
    WHERE o.order_id = ?
    LIMIT 1
");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) { die("Order not found."); }


$datetime = new DateTime($order['order_date']);
$formatted_date = $datetime->format("m/d/Y");
$formatted_time = $datetime->format("h:i A");



$stmt2 = $conn->prepare("
    SELECT od.quantity, od.price_each, 
           p.product_name
    FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    WHERE od.order_id = ?
");
$stmt2->bind_param("s", $order_id);
$stmt2->execute();
$items = $stmt2->get_result();



$subtotal = 0;
foreach ($items as $i) {
    $subtotal += ($i['price_each'] * $i['quantity']);
}
$tax = 25.00;
$delivery_fee = 50.00;
$grand_total = $subtotal + $tax + $delivery_fee;



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Invoice</title>

  
  <link href="https://fonts.googleapis.com/css2?family=Nanum+Brush+Script&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
<?php

?>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f6f8;
    }
    #invoice-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.8);
        z-index: 9999; 
        display: flex;
        justify-content: center; 
        align-items: center; 
        padding: 20px;
        box-sizing: border-box;
        overflow-y: auto;
    }
    .invoice-box {
        background: white;
        width: 100%;
        max-width: 700px;
        border-radius: 12px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        overflow: hidden;
        position: relative;
        animation: slideUp 0.4s ease-out;
        margin: auto; 
    }
    .invoice-header {
        background-color: #ef2a39; 
        color: white;
        padding: 30px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 5px solid #bd1e2a;
    }
    .inv-logo { font-family: "Nanum Brush Script", cursive; font-size: 38px; display: flex; align-items: center; gap: 10px; }
    .inv-title h2 { font-size: 24px; margin: 0; font-weight: 700; letter-spacing: 1px; text-align: right; }
    .inv-title p { margin: 0; opacity: 0.9; font-size: 13px; text-align: right; }

    .invoice-body { padding: 30px 40px; }
    .inv-info-row { display: flex; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
    .inv-info-box h4 { color: #ef2a39; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; }
    .inv-info-box p { margin: 0 0 3px 0; color: #555; font-size: 13px; }
    .inv-badge { background-color: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 11px; }

    .inv-table-wrapper { border-radius: 8px; border: 1px solid #eee; overflow: hidden; margin-bottom: 25px; }
    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table thead { background-color: #FFF0F3; }
    .inv-table th { text-align: left; padding: 12px 15px; color: #ef2a39; font-weight: 700; font-size: 13px; }
    .inv-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }

    .inv-footer { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; }
    .inv-msg { font-size: 13px; color: #777; font-style: italic; }
    .inv-totals { width: 220px; }
    .inv-total-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .inv-total-row.final { font-size: 18px; font-weight: 800; color: #ef2a39; border-top: 2px solid #eee; padding-top: 10px; }

    .inv-actions { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #ddd; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;}
    .btn-inv-print { background-color: #333; color: white; padding: 12px 25px; border-radius: 30px; font-weight: 600; cursor: pointer;}
    .btn-inv-close { background-color: #ef2a39; color: white; padding: 12px 25px; border-radius: 30px; font-weight: 600; cursor: pointer;}

    @media print {
        .inv-actions { display: none !important; }
        #invoice-overlay { background: white; position: static; }
    }
  </style>
</head>
<body>

<div id="invoice-overlay">
    <div class="invoice-box">

        
        <div class="invoice-header">
            <div class="inv-logo">
                <i class="fa-solid fa-bowl-food"></i>
                <span>Ichiraku Ramen</span>
            </div>
            <div class="inv-title">
                <h2>RECEIPT</h2>
                <p>#<?= htmlspecialchars($order_id) ?></p>
            </div>
        </div>

        <div class="invoice-body">

          
            <div class="inv-info-row">
                <div class="inv-info-box">
                    <h4>Billed To</h4>
                    <p style="font-weight:600; font-size:15px; color:#222;">
                        <?= htmlspecialchars($order['customer_name']) ?>
                    </p>
                    <p><?= htmlspecialchars($order['customer_address']) ?></p>
                    <p><?= htmlspecialchars($order['contact_number']) ?></p>
                </div>

                <div class="inv-info-box">
                    <h4>Order Details</h4>
                    <p><strong>Date:</strong> <?= $formatted_date ?></p>
                    <p><strong>Time:</strong> <?= $formatted_time ?></p>
                    <p><strong>Payment:</strong> GCash / COD / Debit (stored later)</p>
                    <div class="inv-badge">PAID</div>
                </div>
            </div>

           
            <div class="inv-table-wrapper">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th class="inv-text-right">Qty</th>
                            <th class="inv-text-right">Price</th>
                            <th class="inv-text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $items->data_seek(0);
                        while ($row = $items->fetch_assoc()): 
                            $total = $row['price_each'] * $row['quantity'];
                        ?>
                        <tr>
                            <td>
                                <span class="inv-item-name"><?= htmlspecialchars($row['product_name']) ?></span>
                            </td>
                            <td class="inv-text-right"><?= $row['quantity'] ?></td>
                            <td class="inv-text-right">₱ <?= number_format($row['price_each'], 2) ?></td>
                            <td class="inv-text-right">₱ <?= number_format($total, 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

          
            <div class="inv-footer">
                <div class="inv-msg">
                    Thank you for dining with Ichiraku Ramen!<br>
                    Support: support@ichiraku.com
                </div>
                <div class="inv-totals">
                    <div class="inv-total-row">
                        <span>Subtotal</span>
                        <span>₱ <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="inv-total-row">
                        <span>Delivery Fee</span>
                        <span>₱ <?= number_format($delivery_fee, 2) ?></span>
                    </div>
                    <div class="inv-total-row">
                        <span>Tax & Fees</span>
                        <span>₱ <?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="inv-total-row final">
                        <span>Total</span>
                        <span>₱ <?= number_format($grand_total, 2) ?></span>
                    </div>
                </div>
            </div>

         
            <div class="inv-actions">
                <button class="btn-inv-print" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Print Receipt
                </button>

                <button class="btn-inv-close"
                        onclick="window.location.href='index.php?track=<?= $order_id ?>'">
                    Proceed to Tracking <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

        </div>

    </div>
</div>

</body>
</html>
