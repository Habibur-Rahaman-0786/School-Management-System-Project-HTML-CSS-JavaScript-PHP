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
    <title>::: Change Password :::</title>
    <style>
        body {
            background: url("pwd.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.8); /* Glass effect */
            backdrop-filter: blur(10px);
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            font-size: 2.2em;
            color: #0077b6; /* Ocean blue */
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        label {
            font-size: 1em;
            color: #555;
            margin-bottom: 5px;
            text-align: left;
        }
        input[type="password"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
        }
        input[type="password"]:focus {
            box-shadow: 0 0 8px #0077b6;
            outline: none;
        }
        button[type="submit"] {
            padding: 12px;
            font-size: 1.2em;
            color: #fff;
            background-color: #0077b6;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button[type="submit"]:hover {
            background-color: #0096c7;
            transform: scale(1.05);
        }
        p {
            font-size: 1em;
            margin-top: 10px;
        }
        p[style*="color:green"] {
            color: #28a745;
            font-weight: bold;
        }
        p[style*="color:red"] {
            color: #e63946;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <form method="POST">
            <label for="current_pwd">Current Password:</label>
            <input type="password" id="current_pwd" name="current_pwd" required>
            
            <label for="new_pwd">New Password:</label>
            <input type="password" id="new_pwd" name="new_pwd" required>
            
            <label for="confirm_new_pwd">Confirm New Password:</label>
            <input type="password" id="confirm_new_pwd" name="confirm_new_pwd" required>
            
            <button type="submit" name="submit">Change Password</button>
        </form>
        <?php
        if (isset($_POST['submit'])) {
            $conn = new mysqli('localhost', 'root', '', 'erp');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $current_pwd = $_POST['current_pwd'];
            $new_pwd = $_POST['new_pwd'];
            $confirm_new_pwd = $_POST['confirm_new_pwd'];

            $stmt = $conn->prepare("SELECT pwd FROM login_details WHERE fname = ? AND lname = ?");
            $stmt->bind_param("ss", $fname, $lname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['pwd'] === $current_pwd) {
                    if ($new_pwd === $confirm_new_pwd) {
                        $update_stmt = $conn->prepare("UPDATE login_details SET pwd = ? WHERE fname = ? AND lname = ?");
                        $update_stmt->bind_param("sss", $new_pwd, $fname, $lname);
                        if ($update_stmt->execute()) {
                            echo "<p style='color:green;'>Password updated successfully.</p>";
                        } else {
                            echo "<p style='color:red;'>Error updating password. Please try again.</p>";
                        }
                        $update_stmt->close();
                    } else {
                        echo "<p style='color:red;'>New passwords do not match. Please try again.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Current password is incorrect. Please try again.</p>";
                }
            } else {
                echo "<p style='color:red;'>User not found. Please contact support.</p>";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
