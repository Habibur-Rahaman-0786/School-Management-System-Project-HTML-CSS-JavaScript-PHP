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

// Database connection (assuming localhost, default username, password, and database)
$servername = "localhost";
$username = "root"; // update with your db username
$password = ""; // update with your db password
$dbname = "erp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student details
$sql = "SELECT fname, lname, class FROM student_details ORDER BY fname ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, sans-serif;
            background: url("attend.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            text-align: center;
            padding: 20px;
        }
        h2 {
            color: #0078D7;
            font-size: 2.5em;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #0078D7;
            color: white;
            font-size: 1.1em;
        }
        tr:nth-child(even) {
            background-color: rgba(220, 235, 255, 0.5);
        }
        button {
            background: rgba(255, 255, 255, 0.7);
            border: 2px solid #0078D7;
            border-radius: 20px;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 1em;
            color: #0078D7;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-shadow: 0px 0px 5px rgba(255, 255, 255, 0.5);
        }
        button:hover {
            background: #0078D7;
            color: #fff;
            box-shadow: 0px 8px 20px rgba(0, 120, 215, 0.5);
        }
        input[type="radio"] {
            transform: scale(1.2);
            accent-color: #0078D7;
            margin: 0 10px;
        }
    </style>
</head>
<body>
<h2>Mark Attendance</h2>

<form action="mark_attendance.php" method="POST">
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Class</th>
            <th>Attendance</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['fname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['lname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['class']) . "</td>";
                echo "<td>";
                echo "<label><input type='radio' name='attendance[" . htmlspecialchars($row['fname']) . "_" . htmlspecialchars($row['lname']) . "]' value='Present'> Present</label>";
                echo "<label><input type='radio' name='attendance[" . htmlspecialchars($row['fname']) . "_" . htmlspecialchars($row['lname']) . "]' value='Absent'> Absent</label>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students found.</td></tr>";
        }
        ?>

    </table>
    <button type="submit" name="submit">Submit Attendance</button>
</form>

<?php
$conn->close();
?>
</body>
</html>
