<?php
session_start();

// Check if fname and lname are set in the session
if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "erp");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filtering logic
$filter = '';
$whereClause = '';
if (isset($_GET['filter']) && ($_GET['filter'] === 'Teacher' || $_GET['filter'] === 'Admin')) {
    $filter = $_GET['filter'];
    $whereClause = "WHERE status = '$filter'";
}

// Fetch notices
$sql = "SELECT fname, lname, status, topic, content, date_time FROM notices $whereClause ORDER BY date_time DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>::: Notices :::</title>
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
            text-align: center;
            margin: 20px 0;
        }

        form label {
            font-weight: bold;
            color: #246b9c;
        }

        form select {
            padding: 8px;
            border: 1px solid #bcd4e6;
            border-radius: 8px;
            background-color: white;
            font-size: 16px;
            color: #246b9c;
            outline: none;
        }

        .notices-container {
            display: flex;
            flex-direction: column; /* Stack items vertically */
            align-items: center; /* Center the cards horizontally */
            gap: 20px; /* Space between the cards */
            padding: 20px;
        }

        .notice-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 90%; /* Take up most of the width for a vertical layout */
            max-width: 500px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .notice-card h3 {
            margin: 0;
            color: #4fa3d5;
            font-size: 20px;
        }

        .notice-card .meta {
            font-size: 14px;
            color: #777;
            margin: 5px 0 15px;
        }

        .notice-card p {
            font-size: 16px;
            margin: 0;
            color: #333;
        }

        .no-notices {
            text-align: center;
            font-size: 18px;
            color: #dc3545;
            padding: 20px;
        }
    </style>
</head>
<body>
<br>
<br>
    <h1>Notices</h1>
    <form method="GET" action="">
        <label for="filter">Filter by:</label>
        <select name="filter" id="filter" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="Teacher" <?= $filter === 'Teacher' ? 'selected' : '' ?>>Teacher</option>
            <option value="Admin" <?= $filter === 'Admin' ? 'selected' : '' ?>>Admin</option>
        </select>
    </form>

    <div class="notices-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notice-card">
                    <h3><?= htmlspecialchars($row['topic']) ?></h3>
                    <div class="meta">
                        By <?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?> (<?= htmlspecialchars($row['status']) ?>) <br>
                        <?= htmlspecialchars($row['date_time']) ?>
                    </div>
                    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-notices">No notices found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
