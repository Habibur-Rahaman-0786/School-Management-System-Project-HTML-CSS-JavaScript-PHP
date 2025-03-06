<?php
session_start();

// Check if fname and lname are set in the session
if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
} else {
    // Redirect to login if session variables are not set
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'erp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username from login_details table
$sql = "SELECT Uname FROM login_details WHERE fname = ? AND lname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $fname, $lname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['Uname'];
} else {
    echo "User not found.";
    exit();
}

// Create table for messages if not exists
$sql_create_table = "
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(50) NOT NULL,
    receiver_username VARCHAR(50) NOT NULL,
    message_topic VARCHAR(100) NOT NULL,
    message_content TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql_create_table);

// Handle form submission to send messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $receiver_username = $_POST['receiver_username'];
    $message_topic = $_POST['message_topic'];
    $message_content = $_POST['message_content'];

    $sql_insert_message = "INSERT INTO messages (sender_username, receiver_username, message_topic, message_content) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert_message);
    $stmt->bind_param("ssss", $username, $receiver_username, $message_topic, $message_content);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch messages for the user
$sql_fetch_messages = "SELECT id, sender_username, message_topic, message_content, is_read FROM messages WHERE receiver_username = ? ORDER BY sent_at DESC";
$stmt = $conn->prepare($sql_fetch_messages);
$stmt->bind_param("s", $username);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System</title>
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

        h1, h2 {
            text-align: center;
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .compose-btn {
            padding: 10px 20px;
            background-color: #1e73be;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .compose-btn:hover {
            background-color: #155b8a;
        }

        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin: 10px 0 5px;
            color: #555;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #1e73be;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #155b8a;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #1e73be;
            color: white;
        }

        .unread {
            font-weight: bold;
            background-color: #e7f1fb;
        }

        .read {
            background-color: #f5f5f5;
        }

        .message-link {
            color: #1e73be;
            text-decoration: none;
            font-weight: bold;
        }

        .message-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<br>
<br>
    <h1>Welcome, <?php echo htmlspecialchars($fname . " " . $lname); ?></h1>

    <div class="button-container">
        <button class="compose-btn" id="composeBtn">Compose Message</button>
    </div>

    <!-- Modal for composing a message -->
    <div id="composeModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Send a Message</h2>
            <form method="post" action="">
                <label for="receiver_username">Receiver Username:</label>
                <input type="text" name="receiver_username" id="receiver_username" required><br>
                
                <label for="message_topic">Message Topic:</label>
                <input type="text" name="message_topic" id="message_topic" required><br>
                
                <label for="message_content">Message Content:</label><br>
                <textarea name="message_content" id="message_content" rows="5" cols="40" required></textarea><br>
                
                <button type="submit" name="send_message">Send Message</button>
            </form>
        </div>
    </div>

    <h2>Your Inbox</h2>
    <table>
        <tr>
            <th>Sender</th>
            <th>Topic</th>
            <th>Preview</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $messages->fetch_assoc()): ?>
            <tr class="<?php echo $row['is_read'] ? 'read' : 'unread'; ?>">
                <td><?php echo htmlspecialchars($row['sender_username']); ?></td>
                <td><?php echo htmlspecialchars($row['message_topic']); ?></td>
                <td><?php echo htmlspecialchars(substr($row['message_content'], 0, 50)) . '...'; ?></td>
                <td>
                    <a class="message-link" href="view_message.php?id=<?php echo $row['id']; ?>">View</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        // Modal functionality
        var modal = document.getElementById("composeModal");
        var btn = document.getElementById("composeBtn");
        var span = document.getElementById("closeModal");

        // Open the modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal if clicked outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
