<?php
require_once 'config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) { echo json_encode(['success'=>false, 'message'=>'Missing credentials']); exit; }

    // Join with customers_info to get the Real Name
    $stmt = $conn->prepare("SELECT u.user_account_id, u.password, u.customer_id, u.username, c.customer_name 
                            FROM users u 
                            JOIN customers_info c ON u.customer_id = c.customer_id 
                            WHERE u.username = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($uid, $hash, $cid, $uname, $realName);

    if ($stmt->fetch()) {
        if (password_verify($password, $hash)) {
            $_SESSION['user_account_id'] = $uid;
            $_SESSION['customer_id'] = $cid;
            $_SESSION['username'] = $uname;
            $_SESSION['real_name'] = $realName; // Store real name

            $admins = ['admin', 'admin@ichiraku.com', 'Teuchi', 'Reiikuzink'];
            $_SESSION['role'] = in_array($uname, $admins) ? 'admin' : 'customer';

            echo json_encode(['success' => true, 'role' => $_SESSION['role'], 'name' => $realName]);
            exit;
        }
    }
    echo json_encode(['success'=>false, 'message'=>'Invalid credentials']);
    exit;
}

if ($action === 'check_session') {
    if (isset($_SESSION['user_account_id'])) {
        echo json_encode([
            'success' => true,
            'role' => $_SESSION['role'] ?? 'customer',
            'name' => $_SESSION['real_name'] ?? $_SESSION['username'] // Return real name
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ... (register and logout remain same, kept minimal here for brevity)
if ($action === 'logout') { session_unset(); session_destroy(); echo json_encode(['success'=>true]); exit; }
if ($action === 'register') {
    // Standard registration logic (same as before)
    $name = $_POST['name'] ?? ''; $email = $_POST['email'] ?? ''; $mobile = $_POST['mobile'] ?? ''; $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("INSERT INTO customers_info (customer_name, contact_number, customer_address) VALUES (?, ?, 'Not Set')");
    $stmt->bind_param('ss', $name, $mobile);
    if ($stmt->execute()) {
        $cid = $stmt->insert_id;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("INSERT INTO users (username, password, customer_id) VALUES (?, ?, ?)");
        $stmt2->bind_param('ssi', $email, $hash, $cid);
        if ($stmt2->execute()) {
            $_SESSION['user_account_id'] = $stmt2->insert_id; $_SESSION['customer_id'] = $cid; $_SESSION['username'] = $email; $_SESSION['real_name'] = $name; $_SESSION['role'] = 'customer';
            echo json_encode(['success'=>true, 'role'=>'customer', 'name'=>$name]); exit;
        }
    }
    echo json_encode(['success'=>false,'message'=>'Error creating account']); exit;
}
?>