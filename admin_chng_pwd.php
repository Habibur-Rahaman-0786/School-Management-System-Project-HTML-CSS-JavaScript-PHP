<?php
// PHP code remains unchanged for server-side logic
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "erp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $stmt = $conn->prepare("SELECT * FROM login_details WHERE Uname = ?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE login_details SET pwd = ? WHERE Uname = ?");
            $stmt->bind_param("ss", $new_password, $uname);

            if ($stmt->execute()) {
                $message = "Password successfully updated.";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "Username not found.";
        }
    } else {
        $message = "Passwords do not match.";
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
    <title>Change Password</title>
    <style>
        /* Frutiger Aero Styling */
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("pwd.png");
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

        h2 {
            color: #0078D7;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
            font-size: 28px;
            margin-bottom: 20px;
        }

        form {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
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
        input[type="password"] {
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
        input[type="password"]:focus {
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
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button:hover {
            background-color: #155b8a;
        }

        p {
            font-size: 14px;
            color: #d32f2f;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Change Password</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Change Password</button>

        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </form>
</body>
</html>