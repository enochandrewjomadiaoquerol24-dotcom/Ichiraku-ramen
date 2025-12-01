<?php
// register.php
require_once 'functions.php';
if (is_logged_in()) header('Location: Final_landpage.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname === '' || $username === '' || $password === '') {
        $msg = 'Please fill required fields.';
    } else {
        // Insert customer info
        $stmt = $mysqli->prepare("INSERT INTO customers_info (customer_name, customer_address, contact_number) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $fullname, $address, $contact);
        if ($stmt->execute()) {
            $customer_id = $stmt->insert_id;
            $stmt->close();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, customer_id) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $username, $hash, $customer_id);
            if ($stmt->execute()) {
                $_SESSION['user_account_id'] = $stmt->insert_id;
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['username'] = $username;
                header('Location: Final_landpage.php');
                exit;
            } else {
                if ($mysqli->errno === 1062) $msg = 'Username already taken.';
                else $msg = 'Error creating account: ' . $mysqli->error;
            }
            $stmt->close();
        } else {
            $msg = 'Error saving customer: ' . $mysqli->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>
<p style="color:red;"><?php echo htmlspecialchars($msg); ?></p>
<form method="post" action="register.php">
    Full name: <input name="fullname" required><br>
    Address: <input name="address"><br>
    Contact: <input name="contact"><br>
    Username: <input name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
<p><a href="login.php">Have account? Login</a></p>
</body>
</html>
