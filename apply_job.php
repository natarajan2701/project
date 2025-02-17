<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html"); 
    exit();
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "placement_cell";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Job Application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_SESSION['username'];
    $job_id = $_POST['job_id'];
    $year = $_POST['year'];
    $department = $_POST['department'];
    $course = $_POST['course'];

    $sql = "INSERT INTO job_applications (student_name, job_id, year, department, course, status) 
            VALUES ('$student_name', '$job_id', '$year', '$department', '$course', 'Pending')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Job application submitted!'); window.location.href = 'student_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error applying for job.'); window.location.href = 'student_dashboard.php';</script>";
    }
}

$conn->close();
?>
