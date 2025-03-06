<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['fname']) || !isset($_SESSION['lname'])) {
    // Redirect to login if session variables are not set
    header("Location: login.php");
    exit();
}

// Database connection (adjust your username and password accordingly)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve fee submission data
$sql = "SELECT * FROM fee_submission";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Submission Records</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("fee_info.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Card Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 30px;
            max-width: 800px;
            width: 90%;
            text-align: left;
        }

        /* Title Styling */
        h1 {
            font-size: 30px;
            color: #1e73be;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }

        h2 {
            font-size: 22px;
            color: #444;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f0f8ff;
            color: #1e73be;
            font-weight: bold;
        }

        td {
            background-color: #fafafa;
            color: #333;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #e3f2fd;
            cursor: pointer;
        }

        /* Empty Data Styling */
        .no-data {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fee Submission Records</h1>
        <h2>Welcome, <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?>!</h2>

        <table>
            <thead>
                <tr>
                    <th>Parent Name</th>
                    <th>Fee Type</th>
                    <th>Installment</th>
                    <th>Amount (Rs.)</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any records
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['fname'] . " " . $row['lname'] . "</td>
                                <td>" . ucfirst($row['fee_type']) . "</td>
                                <td>" . ucfirst($row['installment']) . "</td>
                                <td>" . $row['amount'] . "</td>
                                <td>" . $row['payment_date'] . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr class='no-data'><td colspan='5'>No fee submissions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
