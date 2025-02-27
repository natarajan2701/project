<?php
$servername = "localhost"; // Change if needed
$username = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$dbname = "placementdb"; // Replace with your actual DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Available Jobs
$jobsQuery = "SELECT * FROM jobs";
$jobsResult = $conn->query($jobsQuery);

// Handle Job Application Submission
if (isset($_POST["applyJob"])) {
    $student_id = $_POST["student_id"];
    $name = $_POST["name"];
    $class = $_POST["class"];
    $final_mark = $_POST["final_mark"];
    $qualification = $_POST["qualification"];
    $job_id = $_POST["job_id"];
    $resume = $_FILES["resume"]["name"];

    // ✅ Get the correct company_id for the selected job
    $companyQuery = "SELECT company_id FROM jobs WHERE job_id = '$job_id'";
    $companyResult = $conn->query($companyQuery);
    $companyRow = $companyResult->fetch_assoc();
    $company_id = $companyRow['company_id']; // Fetch correct company_id

    // ✅ Upload Resume
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["resume"]["name"]);
    move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file);

    // ✅ Insert into applications table with correct company_id
    $insertQuery = "INSERT INTO applications (student_id, name, class, final_mark, qualification, resume, job_id, company_id, status) 
                    VALUES ('$student_id', '$name', '$class', '$final_mark', '$qualification', '$resume', '$job_id', '$company_id', 'Pending')";
    
    if ($conn->query($insertQuery) === TRUE) {
        echo "<script>alert('Application Submitted Successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch Applied Jobs
$applicationsQuery = "SELECT * FROM applications WHERE student_id='student_id'"; // Example student_id
$applicationsResult = $conn->query($applicationsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKU | Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" 
    referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="logo.jpg" type="icon">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div>
<img src="mku.png" alt="MKU Logo" title="MKU Logo">
</div>
    <h1>Welcome, Student</h1>

    <h2>Apply for a Job</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Student ID:</label>
        <input type="text" name="student_id" required>
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Class:</label>
        <input type="text" name="class" required>
        <label>Final Mark:</label>
        <input type="number" name="final_mark" required>
        <label>Qualification:</label>
        <input type="text" name="qualification" required>
        <lable>Company ID:</lable>
        <input type="text" name="company_id" required>
        <lable>Job ID:</lable>
        <input type="text" name="job_id" required>
        <lable>Job Title:</lable>
        <input type="text" name="job_title" required>
        <label>Upload Resume:</label>
        <input type="file" name="resume" required>

        <label>Select Job:</label>
        <select name="job_id" required>
            <?php while ($row = $jobsResult->fetch_assoc()) { ?>
                <option value="<?php echo $row['job_id']; ?>">
                    <?php echo $row['job_title']; ?> - <?php echo $row['company_id']; ?> -<?php echo $row['job_id'];?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" name="applyJob">Apply</button>
    </form>

    <h2>Your Applications</h2>
    <table border="1">
        <tr>
            <th>Job ID</th>
            <th>Company</th>
            <th>Status</th>
            <th>Test Score</th>
        </tr>
        <?php while ($row = $applicationsResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row["job_id"]; ?></td>
                <td><?php echo $row["company_id"]; ?></td>
                <td><?php echo $row["status"]; ?></td>
                <td><?php echo $row["test_score"] ? $row["test_score"] : "Not Available"; ?></td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
