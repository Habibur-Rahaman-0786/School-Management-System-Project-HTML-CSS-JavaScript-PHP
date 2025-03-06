<?php
session_start();

if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
} else {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "erp";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date = date("Y-m-d");
$homework_data = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['date'];
}

$query = "SELECT teacher_fname, teacher_lname, subject_name, topic_name, homework_message, created_at FROM homework_details WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $homework_data[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>::: Class Diary :::</title>
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
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        h1 {
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        form input[type="date"] {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        form input[type="date"]:focus {
            border-color: #1e73be;
            outline: none;
        }

        form button {
            background-color: #1e73be;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-left: 10px;
        }

        form button:hover {
            background-color: #155b8a;
            transform: scale(1.05);
        }

        .cards-container {
            width: 100%;
            max-width: 800px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            font-size: 18px;
            font-weight: bold;
            color: #1e73be;
        }

        .card-body {
            margin-top: 10px;
            font-size: 16px;
        }

        .card-footer {
            margin-top: 10px;
            font-size: 14px;
            color: #777;
        }

        p {
            font-size: 16px;
        }
    </style>
</head>
<body>
<br>
<br>
    <h1>Class Diary</h1>
    <p>Welcome, <?php echo htmlspecialchars($fname . " " . $lname); ?>!</p>

    <form method="post">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
        <button type="submit">Check Homework</button>
    </form>

    <h2>Homework for <?php echo htmlspecialchars($date); ?>:</h2>

    <div class="cards-container">
        <?php if (!empty($homework_data)): ?>
            <?php foreach ($homework_data as $homework): ?>
                <div class="card">
                    <div class="card-header">
                        <?php echo htmlspecialchars($homework['subject_name']); ?> - 
                        <?php echo htmlspecialchars($homework['topic_name']); ?>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars($homework['homework_message']); ?></p>
                    </div>
                    <div class="card-footer">
                        Assigned by: <?php echo htmlspecialchars($homework['teacher_fname'] . " " . $homework['teacher_lname']); ?><br>
                        Assigned At: <?php echo htmlspecialchars($homework['created_at']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No homework messages found for this date.</p>
        <?php endif; ?>
    </div>
</body>
</html>