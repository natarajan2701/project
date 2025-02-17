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
    $user_name = $_POST['name'];
    $user_password = $_POST['pswd'];

    $sql = "SELECT * FROM registers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    
        if ($user_password == $row['password']) { // Plain-text comparison
            $_SESSION['username'] = $user_name;
            $_SESSION['role'] = $row['role'];
    
            // Redirect based on role
            switch ($row['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'company':
                    header("Location: company_dashboard.php");
                    break;
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
                default:
                    echo "<script>alert('Invalid role. Contact Admin.');</script>";
                    break;
            }
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location.href = 'login.html';</script>";
        }
    } else {
        echo "<script>alert('User not found. Please register.'); window.location.href = 'register.html';</script>";
    }
    

    $stmt->close();
    $conn->close();
}
?>
