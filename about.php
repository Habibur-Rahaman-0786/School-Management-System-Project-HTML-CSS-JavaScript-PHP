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

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the status associated with the logged-in user
$query = "SELECT status FROM login_details WHERE fname = ? AND lname = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $fname, $lname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];

    // Initialize an empty array for user details
    $userDetails = [];

    // Determine the table to query based on the status
    switch ($status) {
        case 'student':
            $query = "SELECT fname, lname, phone, address, class, gender, blood_group FROM student_details WHERE fname = ? AND lname = ?";
            break;
        case 'teacher':
            $query = "SELECT fname, lname, address, phone, subject, gender, blood_group, class FROM teacher_details WHERE fname = ? AND lname = ?";
            break;
        case 'admin':
            $query = "SELECT fname, lname, address, phone, gender, blood_group FROM admin_details WHERE fname = ? AND lname = ?";
            break;
        case 'parent':
            $query = "SELECT fname, lname, stud_fname, stud_lname, phone, gender FROM parent_details WHERE fname = ? AND lname = ?";
            break;
        default:
            echo "Invalid user status.";
            exit();
    }

    // Prepare and execute the query to fetch user details
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $fname, $lname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userDetails = $result->fetch_assoc();
    } else {
        echo "No details found for the user.";
        exit();
    }
} else {
    echo "User status not found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
        body {
			background: url("about.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.8); /* Frosted glass effect */
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #0077b6; /* Ocean blue */
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        td, th {
            padding: 12px;
            text-align: left;
            border: none;
        }
        th {
            background: rgba(0, 119, 182, 0.1); /* Light ocean blue tint */
            color: #0077b6;
            border-radius: 10px;
        }
        td {
            background: white; /* Clean white boxes */
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        td:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .edit-icon {
            cursor: pointer;
            color: #0077b6;
            margin-left: 10px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Details</h2>
        <table>
            <?php foreach ($userDetails as $key => $value): ?>
                <tr>
                    <th><?php echo ucfirst(str_replace("_", " ", $key)); ?></th>
                    <td>
                        <span id="<?php echo $key; ?>"><?php echo htmlspecialchars($value); ?></span>
                        <span class="edit-icon" onclick="editField('<?php echo $key; ?>')">✎</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function editField(fieldId) {
            const spanElement = document.getElementById(fieldId);
            const currentValue = spanElement.textContent;

            // Replace text with input field
            const input = document.createElement("input");
            input.type = "text";
            input.value = currentValue;
            input.onblur = function () {
                spanElement.textContent = input.value; // Update text with new value
                spanElement.style.display = "inline";
                input.remove();

                // Optionally, send the new value to the server via AJAX
            };

            spanElement.style.display = "none";
            spanElement.parentElement.appendChild(input);
            input.focus();
        }
    </script>
</body>
</html>
