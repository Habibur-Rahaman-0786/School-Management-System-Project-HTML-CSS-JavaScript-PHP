<?php
// Connect to the database
$servername = "localhost";
$username = "root";  // your DB username
$password = "";      // your DB password
$dbname = "erp";     // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    // Check if the user exists
    $check_sql = "SELECT * FROM login_details WHERE Uname = '$user_id'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Delete the user
        $delete_sql = "DELETE FROM login_details WHERE Uname = '$user_id'";

        if ($conn->query($delete_sql) === TRUE) {
            echo "User deleted successfully!";
        } else {
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        echo "User ID not found!";
    }
}

// Close the connection
$conn->close();
?>
