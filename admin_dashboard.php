<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}
echo "Welcome, " . $_SESSION["username"] . "! You are logged in as an Admin.";
?>
<a href="logout.php">Logout</a>
