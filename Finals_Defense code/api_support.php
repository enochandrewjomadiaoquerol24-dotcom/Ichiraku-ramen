<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['customer_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in first.']);
        exit;
    }

    $cid = intval($_SESSION['customer_id']);
    $subject = $_POST['subject'] ?? 'General Inquiry';
    $message = $_POST['message'] ?? '';

    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO support_tickets (customer_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $cid, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    exit;
}
?>