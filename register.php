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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = trim($_POST["role"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]); // No hashing applied
    $studentId = isset($_POST["studentId"]) ? trim($_POST["studentId"]) : null;
    $companyName = isset($_POST["companyName"]) ? trim($_POST["companyName"]) : null;
    $companyId = isset($_POST["companyId"]) ? trim($_POST["companyId"]) : null;

    // Validate required fields
    if (empty($role) || empty($username) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit();
    }

    // Insert based on role
    if ($role === "student") {
        if (empty($studentId)) {
            echo "<script>alert('Student ID is required!'); window.history.back();</script>";
            exit();
        }
        $sql = "INSERT INTO students (student_id, username, password, role) VALUES ('$studentId', '$username', '$password', '$role')";
    
    } elseif ($role === "company") {
        if (empty($companyId) || empty($companyName)) {
            echo "<script>alert('Company Name and ID are required!'); window.history.back();</script>";
            exit();
        }
        $sql = "INSERT INTO company (company_id, company_name, username, password, role) VALUES ('$companyId', '$companyName', '$username', '$password', '$role')";
    
    } elseif ($role === "admin") {
        $sql = "INSERT INTO admin (username, password, role) VALUES ('$username', '$password', '$role')";
    
    } else {
        echo "<script>alert('Invalid role selected!'); window.history.back();</script>";
        exit();
    }

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
    }
}

$conn->close();
?>
