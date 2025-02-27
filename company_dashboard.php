<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "company") {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "placementdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Ensure company_id is correctly retrieved
$company_id = isset($_SESSION["username"]) ? trim($_SESSION["username"]) : null;

if (!$company_id) {
    die("<script>alert('Error: Company ID is missing! Please log in again.'); window.location.href='login.html';</script>");
}

// ✅ Debugging: Print session variables (remove after testing)
// var_dump($_SESSION);

// ✅ Handle Job Posting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addJob"])) {
    $job_id = $_POST["job_id"];
    $job_title = $_POST["job_title"];
    $vacancies = $_POST["vacancies"];
    $last_date = $_POST["last_date"];

    // ✅ Check if company exists
    $company_id = $conn->real_escape_string($_POST["company_id"]);
$checkCompany = "SELECT * FROM company WHERE company_id = '$company_id'";
$result = $conn->query($checkCompany);


    if ($result->num_rows > 0) {
        // ✅ Insert job if company exists
        $sql = $conn->prepare("INSERT INTO jobs (company_id, job_id, job_title, vacancies, last_date) 
                               VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssis", $company_id, $job_id, $job_title, $vacancies, $last_date);

        if ($sql->execute()) {
            echo "<script>alert('Job added successfully!'); window.location.href='company_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error inserting job: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Company does not exist in the database! Please contact admin.');</script>";
    }
}

// ✅ Handle Test Score Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateScore"])) {
    $application_id = $_POST["application_id"];
    $score = $_POST["score"];

    $sql = $conn->prepare("UPDATE applications SET test_score = ? WHERE id = ?");
    $sql->bind_param("ii", $score, $application_id);

    if ($sql->execute()) {
        echo "<script>alert('Score updated successfully!'); window.location.href='company_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating score: " . $conn->error . "');</script>";
    }
}

// ✅ Fetch Company Jobs
$jobsQuery = $conn->prepare("SELECT * FROM jobs WHERE company_id = ?");
$jobsQuery->bind_param("s", $company_id);
$jobsQuery->execute();
$jobsResult = $jobsQuery->get_result();

// ✅ Fetch Applications
$applicationsQuery = $conn->prepare("SELECT applications.*, jobs.job_id, jobs.job_title, jobs.company_id 
                                     FROM applications 
                                     JOIN jobs ON applications.job_id = jobs.job_id 
                                     WHERE jobs.company_id = ?");
$applicationsQuery->bind_param("s", $company_id);
$applicationsQuery->execute();
$applicationsResult = $applicationsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKU | Company Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div>
    <img src="mku.png" alt="MKU Logo" title="MKU Logo">
</div>
<h1>Welcome, <?php echo htmlspecialchars($company_id); ?></h1>

<h2>Post a New Job</h2>
<form method="POST">
    <lable>Company ID:</lable>
    <input type="text" name="company_id" required>
    <label>Job ID:</label>
    <input type="text" name="job_id" required>
    <label>Job Title:</label>
    <input type="text" name="job_title" required>
    <label>No. of Vacancies:</label>
    <input type="number" name="vacancies" required>
    <label>Apply Last Date:</label>
    <input type="date" name="last_date" required>
    <button type="submit" name="addJob">Post Job</button>
</form>

<h2>Job Listings</h2>
<table border="1">
    <tr>
        <th>Job ID</th>
        <th>Title</th>
        <th>Vacancies</th>
        <th>Last Date</th>
    </tr>
    <?php if ($jobsResult->num_rows > 0): ?>
        <?php while ($row = $jobsResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["job_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["job_title"]); ?></td>
                <td><?php echo htmlspecialchars($row["vacancies"]); ?></td>
                <td><?php echo htmlspecialchars($row["last_date"]); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">No jobs posted yet.</td></tr>
    <?php endif; ?>
</table>

<h2>Job Applications</h2>
<table border="1">
    <tr>
        <th>Application ID</th>
        <th>Company ID</th>
        <th>Job ID</th>
        <th>Student ID</th>
        <th>Name</th>
        <th>Class</th>
        <th>Final mark</th>
        <th>Qualification</th>
        <th>Resume</th>
        <th>Status</th>
        <th>Update score</th>
    </tr>
    <?php if ($applicationsResult->num_rows > 0): ?>
        <?php while ($row = $applicationsResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["application_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["company_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["job_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["student_id"]); ?></td>
                <td><?php echo htmlspecialchars($row["name"]); ?></td>
                <td><?php echo htmlspecialchars($row["class"]); ?></td>
                <td><?php echo htmlspecialchars($row["final_mark"]); ?></td>
                <td><?php echo htmlspecialchars($row["qualification"]); ?></td>
                <td><?php echo htmlspecialchars($row["resume"]); ?></td>
                <td><?php echo htmlspecialchars($row["status"]); ?></td>
                <td><?php echo htmlspecialchars($row["score"]); ?></td>
                <td><?php echo $row["test_score"] !== null ? htmlspecialchars($row["test_score"]) : "Not Updated"; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="score" required>
                        <button type="submit" name="updateScore">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">No applications received yet.</td></tr>
    <?php endif; ?>
</table>

<br>
<a href="logout.php">Logout</a>
</body>
</html>

<?php $conn->close(); ?>
