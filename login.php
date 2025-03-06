<?php
// login.php
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // Default username
$password = "";      // Default password
$dbname = "erp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and bind
$stmt = $conn->prepare("SELECT fname, lname, status FROM login_details WHERE Uname = ? AND pwd = ?");
$stmt->bind_param("ss", $user, $pass);

// Execute the query
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Bind the result to variables
    $stmt->bind_result($fname, $lname, $status);
    $stmt->fetch();
    
    // Store the fname and lname in session variables
    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;

    // Redirect based on status
    if ($status == 'student') {
        header("Location: student.php");
    } elseif ($status == 'teacher') {
        header("Location: teacher.php");
    } elseif ($status == 'admin') {
        header("Location: admin.php");
    } elseif ($status == 'parent') {
        header("Location: parent.php");
    } else {
        echo "Invalid status.";
    }
    exit();
} else {
    echo "Invalid username or password.";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
