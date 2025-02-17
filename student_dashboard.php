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

// Fetch Companies & Jobs
$company_sql = "SELECT * FROM company_jobs";
$company_result = $conn->query($company_sql);

// Fetch Student Applications
$student_name = $_SESSION['username'];
$applications_sql = "SELECT * FROM job_applications WHERE student_name = '$student_name'";
$applications_result = $conn->query($applications_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="student_dashboard.css">
    <link rel="icon" href="logo.jpg" type="image/png">
    <title>MKU | Student Dashboard</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="mku.png" alt="MKU Logo">
            <h2>Placement Cell</h2>
        </div>
        <nav>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
    </header>

    <main>
        <section class="welcome">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        </section>

        <!-- Upload Resume -->
        <section class="resume-upload">
            <h3>Upload Your Resume</h3>
            <form action="upload_resume.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="resume" required>
                <button type="submit">Upload</button>
            </form>
        </section>

        <!-- View Available Jobs -->
        <section class="jobs">
            <h3>Available Jobs</h3>
            <table>
                <tr>
                    <th>Company</th>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Apply</th>
                </tr>
                <?php while ($row = $company_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['company_name']; ?></td>
                    <td><?php echo $row['job_title']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>
                        <form action="apply_job.php" method="POST">
                            <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">
                            <label>Year:</label>
                            <input type="number" name="year" required>
                            <label>Department:</label>
                            <input type="text" name="department" required>
                            <label>Course:</label>
                            <input type="text" name="course" required>
                            <button type="submit">Apply</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

        <!-- View Job Application Status -->
        <section class="application-status">
            <h3>Application Status</h3>
            <table>
                <tr>
                    <th>Company</th>
                    <th>Job Title</th>
                    <th>Status</th>
                </tr>
                <?php while ($app = $applications_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $app['company_name']; ?></td>
                    <td><?php echo $app['job_title']; ?></td>
                    <td><?php echo $app['status']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>
    </main>
</body>
</html>
