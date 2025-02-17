<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "placement_cell";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];
$sql = "SELECT * FROM register WHERE reset_token=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid or expired reset token.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $update = "UPDATE register SET password=?, reset_token=NULL WHERE reset_token=?";
    $stmt2 = $conn->prepare($update);
    $stmt2->bind_param("ss", $new_password, $token);
    
    if ($stmt2->execute()) {
        echo "<script>alert('Password reset successfully. Please login with your new password.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error updating password. Try again.'); window.location.href='reset-password.php?token=$token';</script>";
    }
    
    $stmt2->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | MKU</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Reset Password</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password" required>
            </div>
            <button type="submit" class="login-btn">Update Password</button>
        </form>
    </div>
</body>
</html>
