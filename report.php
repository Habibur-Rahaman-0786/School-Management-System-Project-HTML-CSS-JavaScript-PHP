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

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to generate the report card
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected academic year
    $academic_year = $_POST['academic_year'];

    // Query the student marks for the selected academic year
    $sql = "SELECT * FROM student_marks WHERE first_name = '$fname' AND last_name = '$lname' AND academic_year = '$academic_year'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Initialize an array to store marks for each exam type
        $marks_data = [];

        // Fetch the marks for each exam type and organize them by subject and exam type
        while ($row = $result->fetch_assoc()) {
            $marks_data[$row['exam_type']][] = [
                'physics' => $row['physics'],
                'chemistry' => $row['chemistry'],
                'biology' => $row['biology'],
                'maths' => $row['maths'],
                'english' => $row['english'],
                'sst' => $row['sst'],
                'hindi' => $row['hindi'],
                'computers' => $row['computers']
            ];
        }

        // Store marks data in session to access later in the HTML
        $_SESSION['marks_data'] = $marks_data;
    } else {
        $_SESSION['marks_data'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report Card</title>
    <style>
        /* General body and background */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("final_report.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Card-like container */
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 90%;
            max-width: 700px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 90%; /* Ensures content stretches to 90% of the viewport height */
            overflow: hidden;
        }

        h1, h2 {
            color: #0078D7;
            font-weight: 600;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }

        /* Styling for the form */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
            flex-grow: 0; /* Prevents the form from taking too much space */
        }

        select, button {
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            background-color: #f1f9ff;
            transition: background-color 0.3s ease;
        }

        select:focus, button:focus {
            background-color: #e1effb;
        }

        button {
            cursor: pointer;
            background-color: #1e73be;
            color: white;
            border: none;
        }

        button:hover {
            background-color: #15679b;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            max-height: 300px; /* Set max height */
            overflow-y: auto; /* Allow scrolling if the table is too large */
            display: block; /* Make the table scrollable */
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #1e73be;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        p {
            color: #388e3c;
            font-weight: 600;
            margin-top: 20px;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2>Generate Report Card</h2>
        <form method="POST" action="">
            <label for="academic_year">Select Academic Year:</label>
            <select name="academic_year" id="academic_year" required>
                <option value="2024-2025">2024-2025</option>
                <option value="2023-2024">2023-2024</option>
                <option value="2022-2023">2022-2023</option>
                <!-- Add other academic years as needed -->
            </select>
            <button type="submit">Generate Report Card</button>
        </form>

        <?php
        // Check if marks data exists in session
        if (isset($_SESSION['marks_data']) && !empty($_SESSION['marks_data'])) {
            $marks_data = $_SESSION['marks_data'];

            // Display the report card in a table
            echo "<h1>Report Card for " . $fname . " " . $lname . " (" . $_POST['academic_year'] . ")</h1>";
            echo "<table>
                    <tr>
                        <th>Subject</th>
                        <th>FA1</th>
                        <th>SA1</th>
                        <th>FA2</th>
                        <th>SA2</th>
                    </tr>";

            // Define subjects
            $subjects = ['Physics', 'Chemistry', 'Biology', 'Maths', 'English', 'SST', 'Hindi', 'Computers'];

            // Loop through subjects and display marks for each exam type
            foreach ($subjects as $subject) {
                echo "<tr><td>$subject</td>";

                // Loop through exam types (FA1, SA1, FA2, SA2) and get the marks for each subject
                foreach (['FA1', 'SA1', 'FA2', 'SA2'] as $exam_type) {
                    if (isset($marks_data[$exam_type])) {
                        $marks_found = false;
                        foreach ($marks_data[$exam_type] as $marks_row) {
                            // Match the subject to display its marks
                            if (isset($marks_row[strtolower($subject)])) {
                                echo "<td>" . $marks_row[strtolower($subject)] . "</td>";
                                $marks_found = true;
                                break;
                            }
                        }
                        if (!$marks_found) {
                            echo "<td>-</td>"; // If no marks found for that subject in the exam type
                        }
                    } else {
                        echo "<td>-</td>"; // If no marks found for that exam type
                    }
                }

                echo "</tr>";
            }

            echo "</table>";
        } elseif (isset($_POST['academic_year'])) {
            echo "<p>No marks found for the selected academic year.</p>";
        }
        ?>

    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
