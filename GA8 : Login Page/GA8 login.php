<?php

require_once 'functions.php';
if (is_logged_in()) header('Location: Final_landpage.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT user_account_id, password, customer_id FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($uid, $hash, $customer_id);
    if ($stmt->fetch()) {
        if (password_verify($password, $hash)) {
            $_SESSION['user_account_id'] = $uid;
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['username'] = $username;
            $stmt->close();
            header('Location: Final_landpage.php');
            exit;
        }
    }
    $stmt->close();
    $msg = 'Invalid username or password.';
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>
<p style="color:red;"><?php echo htmlspecialchars($msg); ?></p>
<form method="post" action="login.php">
    Username: <input name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<p><a href="register.php">Create an account</a></p>
</body>
</html>
