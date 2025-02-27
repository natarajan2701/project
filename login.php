<?php
session_start();
$servername = "localhost";
$username = "root"; // Change if needed
$password = "";
$dbname = "placementdb"; // Ensure database exists

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["name"]); // 'name' from the form
    $password = trim($_POST["pswd"]); // 'pswd' from the form

    if (empty($username) || empty($password)) {
        echo "<script>alert('Both Username and Password are required!'); window.history.back();</script>";
        exit();
    }

    // Check if user exists in students table
    $sql = "SELECT * FROM students WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'student';
        echo "<script>alert('Login successful! You are logged into the Student site'); window.location.href='student_dashboard.php';</script>";
        exit();
    }

    // Check if user exists in company table (Changed from companies)
    $sql = "SELECT * FROM company WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'company';
        echo "<script>alert('Login successful! You are logged into the Company site'); window.location.href='company_dashboard.php';</script>";
        exit();
    }

    // Check if user exists in admin table
    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        echo "<script>alert('Login successful! You are logged into the Admin site'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // If no match found
    echo "<script>alert('Invalid username or password!'); window.history.back();</script>";
    exit();
}

$conn->close();
?>
