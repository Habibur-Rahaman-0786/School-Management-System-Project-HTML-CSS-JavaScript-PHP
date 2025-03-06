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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>::: Marks :::</title>
    <style>
        /* Frutiger Aero Styling */
        body {
            font-family:'Segoe UI', Tahoma, sans-serif;
            background: url("marks.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }
        form {
            background: rgba(255, 255, 255, 0.8);
            margin: 20px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            color: #246b9c;
        }
        form label {
            font-weight: bold;
        }
        form input, form select, form button {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #bcd4e6;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
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
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        table th {
            background-color: #4fa3d5;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #e3f2fd;
        }
        p {
            text-align: center;
            font-size: 18px;
            color: #d9534f;
        }
    </style>
</head>
<body>
	<br>
    <h1>Retrieve Marks</h1>
    <form method="POST">
        <label for="exam_type">Select Exam Type:</label>
        <select name="exam_type" id="exam_type" required>
            <option value="FA1">FA1</option>
            <option value="SA1">SA1</option>
            <option value="FA2">FA2</option>
            <option value="SA2">SA2</option>
        </select>

        <label for="academic_year">Select Academic Year:</label>
        <input type="text" style="width: 213px;" name="academic_year" id="academic_year" placeholder="e.g., 2023-2024" required>

        <button type="submit" name="retrieve">Retrieve Marks</button>
    </form>

    <?php
    if (isset($_POST['retrieve'])) {
        // Get the selected exam type and academic year
        $exam_type = $_POST['exam_type'];
        $academic_year = $_POST['academic_year'];

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'erp');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to retrieve marks
        $stmt = $conn->prepare("SELECT physics, chemistry, biology, maths, english, sst, hindi, computers FROM student_marks WHERE first_name = ? AND last_name = ? AND exam_type = ? AND academic_year = ?");
        $stmt->bind_param("ssss", $fname, $lname, $exam_type, $academic_year);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>Marks for $fname $lname</h2>";
            echo "<table>
                    <tr>
                        <th>Physics</th>
                        <th>Chemistry</th>
                        <th>Biology</th>
                        <th>Maths</th>
                        <th>English</th>
                        <th>SST</th>
                        <th>Hindi</th>
                        <th>Computers</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['physics']}</td>
                        <td>{$row['chemistry']}</td>
                        <td>{$row['biology']}</td>
                        <td>{$row['maths']}</td>
                        <td>{$row['english']}</td>
                        <td>{$row['sst']}</td>
                        <td>{$row['hindi']}</td>
                        <td>{$row['computers']}</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No marks found for the selected exam type and academic year.</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>