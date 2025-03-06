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
    <title>::: Admin :::</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("main.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            text-align: center;
            padding: 20px;
        }
        h1 {
            color: #0078D7;
            font-size: 2.5em;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        }
        h4 {
            color: #0078D7;
            font-size: 1.5em;
            margin-top: 20px;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
        }
        button {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.7);
            border: 2px solid #0078D7;
            border-radius: 20px;
            padding: 10px 20px;
            margin: 5px;
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
        button img {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.3));
        }
		.top-right-icon {
            position: absolute;
            top: 20px;
            right: 50px;
            width: 80px;
            height: 80px;
            cursor: pointer;
        }
		.menu {
            display: none; /* Hidden by default */
            position: absolute;
            top: 95px;  /* Adjust as needed to place the menu below the icon */
            right: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 150px;
            z-index: 1000;
        }
		.menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }
		.menu a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
<img src="a.png" alt="Icon" class="top-right-icon" onclick="toggleMenu()">
	<div id="dropdownMenu" class="menu">
        <a href="about.php">About</a>
        <a href="chng_pwd.php">Change Password</a>
        <a href="report_problem.html">Report a Problem</a>
        <a href="logout.php">Logout</a>
    </div>
    <h1>Welcome, <?php echo htmlspecialchars($fname . ' ' . $lname); ?>!</h1>
	<p>This is the admin dashboard.</p>
	
	<h4>Accounts Management</h4>
    <button onclick="window.location.href='add_user.php'"><img src="397.png" alt="icon"> Add User</button>
    <button onclick="window.location.href='del_user.html'"><img src="487.png" alt="icon"> Delete User</button>
    <button onclick="window.location.href='admin_chng_pwd.php'"><img src="354.png" alt="icon"> Change Password</button><br>
	
	<h4>Communications</h4>
    <button onclick="window.location.href='send_notices.php'"><img src="336.png" alt="icon"> Send Notices</button>
	<button onclick="window.location.href='msgs.php'"><img src="335.png" alt="icon"> Personal Messages</button>
	
	<h4>Financial</h4>
    <button onclick="window.location.href='fee_payments.php'"><img src="191.png" alt="icon"> Fee Payments</button>
	
	<script>
        // Function to toggle the visibility of the menu
        function toggleMenu() {
            var menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
        }

        // Close the menu if clicking outside of it
        document.addEventListener("click", function(event) {
            var menu = document.getElementById("dropdownMenu");
            var icon = document.querySelector(".top-right-icon");
            if (!menu.contains(event.target) && !icon.contains(event.target)) {
                menu.style.display = "none";
            }
        });
    </script>
</body>
</html>