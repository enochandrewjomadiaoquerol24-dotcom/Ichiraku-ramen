<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$cid = intval($_SESSION['customer_id']);
$action = $_POST['action'] ?? 'fetch';

if ($action === 'read') {
    $nid = intval($_POST['id'] ?? 0);
    $conn->query("UPDATE notifications SET is_read = 1 WHERE notification_id = $nid AND customer_id = $cid");
    echo json_encode(['success' => true]);
    exit;
}

// Added order_id to selection
$sql = "SELECT notification_id, order_id, title, message, is_read, created_at FROM notifications WHERE customer_id = $cid ORDER BY created_at DESC LIMIT 20";
$res = $conn->query($sql);

$data = [];
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $data[] = [
            'id' => $r['notification_id'],
            'order_id' => $r['order_id'], // Need this to link to Invoice
            'title' => $r['title'] ? htmlspecialchars($r['title']) : 'Notification',
            'message' => $r['message'] ? htmlspecialchars($r['message']) : '',
            'is_read' => (bool)$r['is_read'],
            'date' => date('M d, h:i A', strtotime($r['created_at']))
        ];
    }
}
echo json_encode($data);
?>