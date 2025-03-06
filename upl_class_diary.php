<?php
session_start();

// Check if fname and lname are set in the session
if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
} else {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "erp";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the 'homework_details' table if it doesn't exist
$table_query = "
CREATE TABLE IF NOT EXISTS homework_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_fname VARCHAR(50),
    teacher_lname VARCHAR(50),
    subject_name VARCHAR(50),
    topic_name VARCHAR(100),
    homework_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_query)) {
    die("Error creating table: " . $conn->error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_name = $_POST['subject_name'];
    $topic_name = $_POST['topic_name'];
    $homework_message = $_POST['homework_message'];

    // Insert the data into the database
    $stmt = $conn->prepare("INSERT INTO homework_details (teacher_fname, teacher_lname, subject_name, topic_name, homework_message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fname, $lname, $subject_name, $topic_name, $homework_message);

    if ($stmt->execute()) {
        $message = "Homework assigned successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Homework</title>
    <style>
        /* Frutiger Aero Styling */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("diary.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        h1 {
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
            font-size: 36px;
            margin-bottom: 20px;
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }

        input[type="text"], 
        textarea, 
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #1e73be;
            outline: none;
        }

        button {
            background-color: #1e73be;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #155b8a;
            transform: scale(1.05);
        }

        p {
			color: #0078D7;
            font-size: 1.0em;
            margin-top: 10px;
			text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
        }

        .success {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <form method="post">
        <h1>Assign Homework</h1>
        <p>Welcome, <strong><?php echo htmlspecialchars($fname . " " . $lname); ?></strong>!</p>
        
        <label for="subject_name">Select Subject:</label>
        <select id="subject_name" name="subject_name" required>
            <option value="">--Select Subject--</option>
            <option value="Physics">Physics</option>
            <option value="Chemistry">Chemistry</option>
            <option value="Biology">Biology</option>
            <option value="Maths">Maths</option>
            <option value="English">English</option>
            <option value="Hindi">Hindi</option>
            <option value="Computers">Computers</option>
            <option value="SST">SST</option>
        </select>

        <label for="topic_name">Topic Name:</label>
        <input type="text" id="topic_name" name="topic_name" required>

        <label for="homework_message">Homework Message:</label>
        <textarea id="homework_message" name="homework_message" rows="5" required></textarea>

        <button type="submit">Assign Homework</button>

        <?php if (isset($message)): ?>
            <p class="<?php echo strpos($message, 'success') !== false ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
    </form>
</body>
</html>