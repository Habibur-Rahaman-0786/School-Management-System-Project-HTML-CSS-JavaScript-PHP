<?php
// Start session to access session variables
session_start();

// Check if user is logged in and session variables are set
if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    echo "Please log in to view your attendance details.";
    exit;
}

// Get the user's first and last names from the session
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];

// Database connection
$servername = "localhost";
$username = "root"; // Update with your username
$password = "";     // Update with your password
$dbname = "erp";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the start and end dates for the current academic year
$currentYear = date("Y");
$start_date = date("Y-m-d", strtotime("April 1 " . ($currentYear - (date("n") < 4 ? 1 : 0))));
$end_date = date("Y-m-d", strtotime("March 31 " . ($currentYear + (date("n") >= 4 ? 1 : 0))));

// Query to count the number of times the student was marked present in the current academic year
$sql = "SELECT COUNT(*) AS present_count FROM attendance_details 
        WHERE first_name = ? AND last_name = ? 
        AND attendance_status = 'Present'
        AND attendance_date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $fname, $lname, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Check if data was retrieved
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $present_count = $row['present_count'];
} else {
    $present_count = 0; // Default to 0 if no attendance records are found
}

$total_classes = 220;
$attendance_percentage = ($present_count / $total_classes) * 100;

// Determine the image based on attendance percentage
if ($attendance_percentage < 55) {
    $image = "lvl1.png";
} elseif ($attendance_percentage == 55) {
    $image = "lvl2.png";
} else {
    $image = "lvl3.png";
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
    <style>
        /* Frutiger Aero inspired styling */
        body {
            background: url("attend.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, sans-serif;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            backdrop-filter: blur(8px);
        }
        h2 {
            font-size: 2.5em;
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
            margin: 10px 0;
        }
        .highlight {
            font-size: 1.2em;
            color: #0078D7;
            font-weight: bold;
        }
        .note {
            font-size: 1em;
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
        }
        .note.red {
            background: rgba(255, 102, 102, 0.15);
            color: #d9534f;
        }
        .note.green {
            background: rgba(102, 255, 102, 0.15);
            color: #5cb85c;
        }
        img {
            margin: 20px 0;
            width: 150px;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Attendance Summary</h2>

        <!-- Display dynamic image based on attendance percentage -->
        <img src="<?php echo $image; ?>" alt="Attendance Level Image">

        <p>Present Count: <span class="highlight"><?php echo $present_count; ?></span> / <?php echo $total_classes; ?></p>
        <p>Attendance Percentage: <span class="highlight"><?php echo number_format($attendance_percentage, 2); ?>%</span></p>

        <?php if ($attendance_percentage < 55): ?>
            <p class="note red">Note: Your attendance is below the required threshold. Please make efforts to improve it.</p>
        <?php else: ?>
            <p class="note green">Good job! Keep up your attendance.</p>
        <?php endif; ?>
    </div>
</body>
</html>
