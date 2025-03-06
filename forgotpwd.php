<?php
// Connect to the database
$servername = "localhost";
$username = "root";  // your DB username
$password = "";      // your DB password
$dbname = "erp";     // your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_username = $_POST['sender_username'];
    $receiver_username = 'admin1'; // Admin's username
    $message_topic = 'Password Change Request';
    $message_content = "The user $sender_username has forgotten their password, please change it.";
    $is_read = 0; // Message not read by admin yet
    $sent_at = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare SQL query to insert message into the database
    $sql = "INSERT INTO messages (sender_username, receiver_username, message_topic, message_content, is_read, sent_at) 
            VALUES ('$sender_username', '$receiver_username', '$message_topic', '$message_content', '$is_read', '$sent_at')";

    // Execute query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        echo "Password change request sent successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>
