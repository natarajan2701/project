<?php
// Database Configuration
$servername = "localhost"; // Change if necessary
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP MySQL password (empty)
$dbname = "placement_cell"; // Database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form data
    $role = htmlspecialchars(trim($_POST['role']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password'])); // Store plain text password
    $email = htmlspecialchars(trim($_POST['email']));

    // Initialize optional fields as NULL
    $studentId = $department = $companyId = $companyName = $address = NULL;

    // Handle role-specific fields
    if ($role === "student") {
        $studentId = isset($_POST['studentId']) ? htmlspecialchars(trim($_POST['studentId'])) : NULL;
        $department = isset($_POST['department']) ? htmlspecialchars(trim($_POST['department'])) : NULL;
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : NULL;
    } elseif ($role === "company") {
        $companyId = isset($_POST['companyId']) ? htmlspecialchars(trim($_POST['companyId'])) : NULL;
        $companyName = isset($_POST['companyName']) ? htmlspecialchars(trim($_POST['companyName'])) : NULL;
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : NULL;
    } elseif ($role !== "admin") {
        echo "Invalid role selected.";
        exit;
    }

    // Prepare SQL to insert data into the `registers` table
    $stmt = $conn->prepare("INSERT INTO registers (username, password, email, role, student_id, department, company_id, company_name, address)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $username, $password, $email, $role, $studentId, $department, $companyId, $companyName, $address);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to home page...";
        header("Refresh: 3; url=index.html"); // Redirect after 3 seconds
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>
