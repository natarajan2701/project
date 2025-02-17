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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $sql = "SELECT * FROM register WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Store the token in the database
        $update = "UPDATE register SET reset_token=? WHERE email=?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("ss", $token, $email);
        $stmt2->execute();

        // Send reset email
        $reset_link = "http://yourwebsite.com/reset-password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n$reset_link";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script>alert('A password reset link has been sent to your email.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error sending email. Try again.'); window.location.href='forgot-password.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found. Please check and try again.'); window.location.href='forgot-password.html';</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
