<?php
session_start();

if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // update with your db username
$password = ""; // update with your db password
$dbname = "erp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit']) && isset($_POST['attendance'])) {
    $attendance = $_POST['attendance'];
    $date = date('Y-m-d'); // Set attendance date

    foreach ($attendance as $key => $status) {
        list($firstName, $lastName) = explode('_', $key);

        // Prepare the SQL query to insert attendance record
        $sql = "INSERT INTO attendance_details (first_name, last_name, class, attendance_date, attendance_status) 
                VALUES ('$firstName', '$lastName', 'class_placeholder', '$date', '$status')";

        if ($conn->query($sql) === TRUE) {
            echo "Attendance marked successfully for $firstName $lastName.<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
