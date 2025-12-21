<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) { echo json_encode([]); exit; }
$cid = intval($_SESSION['customer_id']);
$action = $_POST['action'] ?? 'fetch';

// --- NEW: Fetch Single Order Details for Invoice ---
if ($action === 'get_details') {
    $oid = intval($_POST['order_id']);
    
    // Security: Ensure this order belongs to the logged-in customer
    $check = $conn->query("SELECT order_id FROM orders WHERE order_id = $oid AND customer_id = $cid");
    if($check->num_rows === 0) { echo json_encode(['success'=>false, 'message'=>'Unauthorized']); exit; }

    // 1. Fetch Order & Customer Data
    $sqlOrder = "SELECT o.order_id, o.order_date, o.total_amount, o.shipping_address, 
                        c.customer_name, c.contact_number 
                 FROM orders o 
                 JOIN customers_info c ON o.customer_id = c.customer_id 
                 WHERE o.order_id = $oid";
    $order = $conn->query($sqlOrder)->fetch_assoc();

    // 2. Fetch Line Items
    $sqlItems = "SELECT od.quantity, od.subtotal, p.product_name, p.price 
                 FROM order_details od 
                 JOIN products p ON od.product_id = p.product_id 
                 WHERE od.order_id = $oid";
    $resItems = $conn->query($sqlItems);
    $items = [];
    while($row = $resItems->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
    exit;
}

// --- HANDLE RATING ---
if ($action === 'rate') {
    $oid = intval($_POST['order_id']);
    $stars = intval($_POST['rating']);
    if($oid > 0 && $stars > 0) {
        $conn->query("UPDATE orders SET rating = $stars WHERE order_id = $oid AND customer_id = $cid");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// --- FETCH LIST ---
$sql = "SELECT o.order_id as id, 
               o.order_date as date, 
               o.total_amount as total, 
               IFNULL(ds.status_name, 'Pending') as status,
               o.rating
        FROM orders o 
        LEFT JOIN delivery d ON o.order_id = d.order_id 
        LEFT JOIN delivery_status ds ON d.delivery_status_id = ds.delivery_status_id
        WHERE o.customer_id = $cid 
        ORDER BY o.order_id DESC";

$res = $conn->query($sql);
$data = [];
if ($res) {
    while($r = $res->fetch_assoc()) {
        $r['total'] = floatval($r['total']);
        $r['date'] = date('M d, Y h:i A', strtotime($r['date']));
        $r['rating'] = intval($r['rating']); 
        $data[] = $r;
    }
}
echo json_encode($data);
?>