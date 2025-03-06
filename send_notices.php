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
$conn = new mysqli("localhost", "root", "", "erp");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify user status
$status = '';
$sql = "SELECT status FROM login_details WHERE fname = ? AND lname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $fname, $lname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];
} else {
    echo "User not found.";
    exit();
}

$stmt->close();

// Handle form submission for sending notices
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $content = $_POST['content'];
    $date_time = date("Y-m-d H:i:s"); // Current date and time

    if (!empty($topic) && !empty($content)) {
        // Insert notice into the database
        $sql = "INSERT INTO notices (fname, lname, status, topic, content, date_time) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $fname, $lname, $status, $topic, $content, $date_time);

        if ($stmt->execute()) {
            echo "<p class='success'>Notice sent successfully.</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p class='error'>Please fill out all fields.</p>";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>::: Send Notice :::</title>
    <style>
        /* Frutiger Aero Styling */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("notices.png");
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
            margin-top: 20px;
        }

        form {
            background: rgba(255, 255, 255, 0.85);
            margin: 30px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            color: #246b9c;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bcd4e6;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        form textarea {
            resize: none;
        }

        form button {
            background-color: #4fa3d5;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #246b9c;
        }

        .success {
            text-align: center;
            color: #28a745;
            font-weight: bold;
        }

        .error {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
<br>
<br>
    <h1>Send Notice</h1>
    <form method="POST" action="">
        <label for="topic">Notice Topic:</label>
        <input type="text" style="width: 474px;" id="topic" name="topic" placeholder="Enter the notice topic" required>

        <label for="content">Notice Content:</label>
        <textarea id="content" style="width: 474px;" name="content" rows="5" placeholder="Enter the notice content" required></textarea>

        <button type="submit">Send Notice</button>
    </form>
</body>
</html>
