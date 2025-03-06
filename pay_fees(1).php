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

// Database connection (XAMPP - MySQL)
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "erp"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch fee payment status from the database
$sql = "SELECT * FROM fee_payment_status WHERE fname = '$fname' AND lname = '$lname'";
$result = $conn->query($sql);

$paymentStatus = [
    'academic_first_installment' => 'unpaid',
    'academic_second_installment' => 'unpaid',
    'transport_first_installment' => 'unpaid',
    'transport_second_installment' => 'unpaid'
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $paymentStatus['academic_first_installment'] = $row['academic_first_installment'];
        $paymentStatus['academic_second_installment'] = $row['academic_second_installment'];
        $paymentStatus['transport_first_installment'] = $row['transport_first_installment'];
        $paymentStatus['transport_second_installment'] = $row['transport_second_installment'];
    }
}

// Handle fee payment (after confirmation)
if (isset($_POST['pay_fee'])) {
    $installment = $_POST['installment'];

    // Update payment status to 'paid' for the selected installment
    $updateSql = "UPDATE fee_payment_status SET $installment = 'paid' WHERE fname = '$fname' AND lname = '$lname'";
    if ($conn->query($updateSql) === TRUE) {
        // Success message
        echo "<script>alert('Fee payment for $installment has been successfully updated to paid.');</script>";
    } else {
        echo "<script>alert('Error updating payment status: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payment</title>
    <script type="text/javascript">
        function confirmPayment(installment) {
            if (confirm("Are you sure you want to pay for the " + installment.replace("_", " ") + "?")) {
                document.getElementById('installment').value = installment;
                document.getElementById('payForm').submit();
            }
        }
    </script>
</head>
<body>
    <h1>Welcome, <?php echo $fname . " " . $lname; ?>!</h1>
    <h2>Fee Payment</h2>
    
    <form id="payForm" method="POST" style="display: none;">
        <input type="hidden" name="installment" id="installment">
        <input type="hidden" name="pay_fee" value="true">
    </form>

    <table border="1">
        <tr>
            <th>Installment</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>Academic First Installment</td>
            <td>Rs. 50,000</td>
            <td><?php echo $paymentStatus['academic_first_installment']; ?></td>
            <td>
                <?php if ($paymentStatus['academic_first_installment'] == 'unpaid') { ?>
                    <button type="button" onclick="confirmPayment('academic_first_installment')">Pay Fees</button>
                <?php } else { ?>
                    Paid
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Academic Second Installment</td>
            <td>Rs. 50,000</td>
            <td><?php echo $paymentStatus['academic_second_installment']; ?></td>
            <td>
                <?php if ($paymentStatus['academic_second_installment'] == 'unpaid') { ?>
                    <button type="button" onclick="confirmPayment('academic_second_installment')">Pay Fees</button>
                <?php } else { ?>
                    Paid
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Transport First Installment</td>
            <td>Rs. 15,000</td>
            <td><?php echo $paymentStatus['transport_first_installment']; ?></td>
            <td>
                <?php if ($paymentStatus['transport_first_installment'] == 'unpaid') { ?>
                    <button type="button" onclick="confirmPayment('transport_first_installment')">Pay Fees</button>
                <?php } else { ?>
                    Paid
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Transport Second Installment</td>
            <td>Rs. 15,000</td>
            <td><?php echo $paymentStatus['transport_second_installment']; ?></td>
            <td>
                <?php if ($paymentStatus['transport_second_installment'] == 'unpaid') { ?>
                    <button type="button" onclick="confirmPayment('transport_second_installment')">Pay Fees</button>
                <?php } else { ?>
                    Paid
                <?php } ?>
            </td>
        </tr>
    </table>
</body>
</html>
