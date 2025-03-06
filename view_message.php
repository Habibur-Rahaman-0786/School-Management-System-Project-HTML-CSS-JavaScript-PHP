<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'erp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a message ID is passed via the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $message_id = intval($_GET['id']);

    // Fetch the message details
    $sql = "SELECT sender_username, receiver_username, message_topic, message_content, is_read, sent_at 
            FROM messages 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();

        // Check if the logged-in user is the receiver
        $current_user = $_SESSION['fname'] . " " . $_SESSION['lname'];
        $sql_get_username = "SELECT Uname FROM login_details WHERE fname = ? AND lname = ?";
        $stmt_get_username = $conn->prepare($sql_get_username);
        $stmt_get_username->bind_param("ss", $_SESSION['fname'], $_SESSION['lname']);
        $stmt_get_username->execute();
        $username_result = $stmt_get_username->get_result();
        $username = $username_result->fetch_assoc()['Uname'];

        if ($message['receiver_username'] !== $username) {
            echo "You are not authorized to view this message.";
            exit();
        }

        // Mark the message as read if it is unread
        if ($message['is_read'] == 0) {
            $sql_mark_read = "UPDATE messages SET is_read = 1 WHERE id = ?";
            $stmt_mark_read = $conn->prepare($sql_mark_read);
            $stmt_mark_read->bind_param("i", $message_id);
            $stmt_mark_read->execute();
        }
    } else {
        echo "Message not found.";
        exit();
    }
} else {
    echo "Invalid message ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <style>
        /* Frutiger Aero Styling */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("msgs.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
            margin-top: 40px;
        }

        .message-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            max-width: 800px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .message-container p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .message-container strong {
            color: #1e73be;
        }

		.button-container { 
			text-align: center; 
			margin-top: 20px; 
		}

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #1e73be;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #155b8a;
        }

        .message-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .message-content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            line-height: 1.6;
        }

    </style>
</head>
<body>
<br>
    <h1>Message Details</h1>

    <div class="message-container">
        <div class="message-header">
            <p><strong>From:</strong> <?php echo htmlspecialchars($message['sender_username']); ?></p>
            <p><strong>To:</strong> <?php echo htmlspecialchars($message['receiver_username']); ?></p>
            <p><strong>Topic:</strong> <?php echo htmlspecialchars($message['message_topic']); ?></p>
        </div>
		<p><strong>Message:</strong><br>
        <div class="message-content">
            <?php echo nl2br(htmlspecialchars($message['message_content'])); ?></p>
        </div>

        <p><strong>Sent At:</strong> <?php echo htmlspecialchars($message['sent_at']); ?></p>
		<div class="button-container">
        <a href="msgs.php" style="display: inline-block; text-align: center;">Back to Inbox</a>
		</div>
    </div>

</body>
</html>
