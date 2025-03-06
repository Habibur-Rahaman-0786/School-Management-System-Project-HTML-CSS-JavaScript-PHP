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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the fee details
    $fee_type = $_POST['fee_type'];
    $installment = $_POST['installment'];
    $amount = 0;

    // Determine the amount based on fee type and installment
    if ($fee_type == 'academic') {
        if ($installment == 'first') {
            $amount = 50000;
        } elseif ($installment == 'second') {
            $amount = 50000;
        }
    } elseif ($fee_type == 'transportation') {
        if ($installment == 'first') {
            $amount = 15000;
        } elseif ($installment == 'second') {
            $amount = 15000;
        }
    }

    // Store the fee payment in the database
    $sql = "INSERT INTO fee_submission (fname, lname, fee_type, installment, amount, payment_date)
            VALUES ('$fname', '$lname', '$fee_type', '$installment', '$amount', NOW())";

    if ($conn->query($sql) === TRUE) {
        $confirmation_message = "Fee for $fee_type ($installment) installment of Rs. $amount has been successfully paid!";
    } else {
        $confirmation_message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Submission</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("fee_info.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Card Container */
        .container {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        /* Title Styling */
        h1 {
            color: #1e73be;
            font-size: 28px;
            text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        }

        h2 {
            font-size: 20px;
            color: #444;
            margin-bottom: 20px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        /* Adjusting the radio buttons to be side by side */
        .radio-group {
            display: flex;
            justify-content: space-around;
        }

        .radio-group input[type="radio"] {
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #1e73be;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #15679b;
        }

        /* Confirmation Message Styling */
        p {
            color: #388e3c;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $fname . " " . $lname; ?>!</h1>
        <h2>Fee Submission</h2>

        <?php
        if (isset($confirmation_message)) {
            echo "<p>$confirmation_message</p>";
        }
        ?>

        <form method="POST" action="">
            <label for="fee_type">Fee Type:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="fee_type" value="academic" required> Academic
                </label>
                <label>
                    <input type="radio" name="fee_type" value="transportation" required> Transportation
                </label>
            </div>

            <label for="installment">Installment:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="installment" value="first" required> First Installment
                </label>
                <label>
                    <input type="radio" name="installment" value="second" required> Second Installment
                </label>
            </div>

            <input type="submit" value="Pay Fee">
        </form>
    </div>
</body>
</html>
