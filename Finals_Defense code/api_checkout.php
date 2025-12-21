<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    echo json_encode(['success'=>false, 'message'=>'Invalid JSON']);
    exit;
}

$customerId = intval($_SESSION['customer_id']);
$total = floatval($input['total']);
$address = $input['address'] ?? 'Pick Up'; 
$date = date('Y-m-d H:i:s');

// START TRANSACTION
$conn->begin_transaction();

try {
    // 1. Create Order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, order_date, total_amount, payment_method, shipping_address) VALUES (?, ?, ?, 'COD', ?)");
    $stmt->bind_param("isds", $customerId, $date, $total, $address);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $orderId = $conn->insert_id;
    $stmt->close();

    // 2. Process Items & Inventory
    $stmtDetails = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
    $stmtGetIng  = $conn->prepare("SELECT ingredient_id, qty_required FROM product_ingredients WHERE product_id = ?");
    $stmtStock   = $conn->prepare("UPDATE ingredients SET stock_qty = stock_qty - ? WHERE ingredient_id = ?");

    foreach ($input['items'] as $item) {
        $prodId = intval($item['dish']['id']);
        $qty = intval($item['qty']);
        $sub = floatval($item['totalPrice']);

        // A. Order Details
        $stmtDetails->bind_param("iiid", $orderId, $prodId, $qty, $sub);
        if (!$stmtDetails->execute()) throw new Exception("Failed to add items");

        // B. Inventory Deduction
        $stmtGetIng->bind_param("i", $prodId);
        $stmtGetIng->execute();
        $result = $stmtGetIng->get_result();

        while ($row = $result->fetch_assoc()) {
            $ingId = $row['ingredient_id'];
            $needed = $row['qty_required'] * $qty;
            
            // Check stock logic could go here, but for now we just deduct
            $stmtStock->bind_param("ii", $needed, $ingId);
            $stmtStock->execute();
        }
    }

    $stmtDetails->close();
    $stmtGetIng->close();
    $stmtStock->close();

    // 3. Create Delivery Record
    $stmtDelivery = $conn->prepare("INSERT INTO delivery (order_id, delivery_date, delivery_status_id) VALUES (?, NULL, 1)");
    $stmtDelivery->bind_param("i", $orderId);
    if (!$stmtDelivery->execute()) throw new Exception("Failed to init delivery");
    $stmtDelivery->close();

    // COMMIT
    $conn->commit();
    echo json_encode(['success' => true, 'orderId' => $orderId]);

} catch (Exception $e) {
    // ROLLBACK ON ERROR
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Order Failed: ' . $e->getMessage()]);
}
?>