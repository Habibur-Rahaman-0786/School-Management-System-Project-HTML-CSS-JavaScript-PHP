<?php
// Connect to the database
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";      // Replace with your database password
$dbname = "erp";     // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_username = $_POST['sender_username'];
    $receiver_username = "admin1"; // Admin's username
    $message_topic = $_POST['message_topic'];
    $message_content = $_POST['message_content'];
    $is_read = 0; // Message not read yet
    $sent_at = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare SQL query to insert the message
    $sql = "INSERT INTO messages (sender_username, receiver_username, message_topic, message_content, is_read, sent_at) 
            VALUES ('$sender_username', '$receiver_username', '$message_topic', '$message_content', $is_read, '$sent_at')";

    if ($conn->query($sql) === TRUE) {
        echo "Your problem has been reported successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
