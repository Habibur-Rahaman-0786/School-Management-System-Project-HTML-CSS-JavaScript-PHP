<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>::: Add User :::</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: url("add_usr.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 100vh; /* Ensure the body takes the full height */
            overflow-y: auto;  /* Make page scrollable if content overflows */
        }

        /* Center aligned container for Add User */
        .container {
            width: 60%;  /* Adjust width of the container */
            background-color: rgba(255, 255, 255, 0.8);  /* White semi-transparent background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            color: black;  /* Black text color */
            overflow-y: auto;  /* Ensure the container is scrollable */
        }

        h2 {
            text-align: center;  /* Center-align the 'Add User' heading */
            margin-bottom: 20px;
            color: #333;
        }

        /* Style for the tab links */
        .tab {
            overflow: hidden;
            border-bottom: 1px solid #ccc;
            text-align: center;
            margin-bottom: 20px;
        }

        .tab button {
            background-color: #f1f1f1;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            margin: 0 5px;
            border-radius: 8px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Glow effect on hover */
        .tab button:hover {
            background-color: #add8e6;
            box-shadow: 0 0 10px #add8e6;
        }

        /* Active tab button styling */
        .tab button.active {
            background-color: #add8e6;
            color: #fff;
            box-shadow: 0 0 10px #add8e6;
        }

        /* Style for the tab content */
        .tabcontent {
            display: none;
            padding: 20px;
            border-top: none;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            margin-top: 20px;
        }

        /* Style for the form inputs */
        input[type="text"], input[type="number"]{
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        /* Narrower submit button */
        input[type="submit"] {
  cursor: pointer;
  padding: 12px 24px; /* Added padding to make the button larger */
  border-radius: 12px; /* Rounded corners */
  border: 0;
  text-shadow: 1px 1px #000a;
  background: linear-gradient(#006caa, #00c3ff);
  box-shadow: 0px 4px 6px 0px #0008;
  font-weight: 550;
  color: white;
  font-size: 16px;
  outline: none; /* Remove the default outline */
  transition: box-shadow 0.3s ease; /* Smooth transition for glow effect */
}

input[type="submit"]:hover {
  box-shadow: 0px 6px 12px 0px #0009, /* Standard shadow */
              0 0 12px 4px #00c3ff;   /* Glow effect */
}

input[type="submit"]:active {
  box-shadow: 0px 0px 0px 0px #0000; /* Remove shadow on active */
}

        /* Add some margin to the form */
        form {
            padding: 20px;
        }
    </style>
</head>
<body>

<?php
// Database connection
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";      // Default password for XAMPP
$dbname = "erp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert data into tables
function insertData($table, $data) {
    global $conn;
    $columns = implode(", ", array_keys($data));
    $values = implode("', '", array_values($data));
    $sql = "INSERT INTO $table ($columns) VALUES ('$values')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data submitted successfully!');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];
    $loginData = [
        'Uname' => $_POST['username'],
        'pwd' => $_POST['password'],
        'fname' => $_POST['login_fname'],
        'lname' => $_POST['login_lname'],
        'status' => $_POST['status']
    ];

    // Insert into login_details table
    insertData('login_details', $loginData);

    switch ($formType) {
        case 'student':
            $studentData = [
                'fname' => $_POST['student_fname'],
                'lname' => $_POST['student_lname'],
                'phone' => $_POST['student_phone'],
                'address' => $_POST['student_address'],
                'class' => $_POST['student_class'],
                'gender' => $_POST['student_gender'],
                'blood_group' => $_POST['student_blood_group']
            ];
            insertData('student_details', $studentData);
            break;
            
        case 'parent':
            $parentData = [
                'fname' => $_POST['parent_fname'],
                'lname' => $_POST['parent_lname'],
                'stud_fname' => $_POST['student_fname'],
                'stud_lname' => $_POST['student_lname'],
                'phone' => $_POST['parent_phone'],
                'gender' => $_POST['parent_gender']
            ];
            insertData('parent_details', $parentData);
            break;

        case 'teacher':
            $teacherData = [
                'fname' => $_POST['teacher_fname'],
                'lname' => $_POST['teacher_lname'],
                'address' => $_POST['teacher_address'],
                'phone' => $_POST['teacher_phone'],
                'subject' => $_POST['teacher_subject'],
                'gender' => $_POST['teacher_gender'],
                'blood_group' => $_POST['teacher_blood_group'],
                'class' => $_POST['teacher_class']
            ];
            insertData('teacher_details', $teacherData);
            break;

        case 'admin':
            $adminData = [
                'fname' => $_POST['admin_fname'],
                'lname' => $_POST['admin_lname'],
                'address' => $_POST['admin_address'],
                'phone' => $_POST['admin_phone'],
                'gender' => $_POST['admin_gender'],
                'blood_group' => $_POST['admin_blood_group']
            ];
            insertData('admin_details', $adminData);
            break;
    }
}
?>

<div class="container">

    <h2>Add User</h2>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Tab1')">Add Student</button>
        <button class="tablinks" onclick="openTab(event, 'Tab2')">Add Parent</button>
        <button class="tablinks" onclick="openTab(event, 'Tab3')">Add Teacher</button>
        <button class="tablinks" onclick="openTab(event, 'Tab4')">Add Admin</button>
    </div>

    <!-- Add Student -->
    <div id="Tab1" class="tabcontent">
        <h3>Add Student Details</h3>
        <form method="post">
            <input type="hidden" name="form_type" value="student">
            <p><b>Login Details</b></p>
            Username: <input type="text" name="username"><br>
            Password: <input type="text" name="password"><br>
            First Name: <input type="text" name="login_fname"><br>
            Last Name: <input type="text" name="login_lname"><br>
            Status: <input type="text" name="status" value="Student" readonly><br>
            <p><b>Student Details</b></p>
            First Name: <input type="text" name="student_fname"><br>
            Last Name: <input type="text" name="student_lname"><br>
            Phone: <input type="text" name="student_phone"><br>
            Address: <input type="text" name="student_address"><br>
            Class: <input type="number" name="student_class"><br>
            Gender: <input type="text" name="student_gender"><br>
            Blood Group: <input type="text" name="student_blood_group"><br>
            <input type="submit" value="Submit" style="display: block; margin: 0 auto; padding: 12px 24px; border-radius: 12px; border: 0; background: linear-gradient(#006caa, #00c3ff); color: white; font-weight: 550; font-size: 16px; cursor: pointer;">

        </form>
    </div>

    <!-- Add Parent -->
    <div id="Tab2" class="tabcontent">
        <h3>Add Parent Details</h3>
        <form method="post">
            <input type="hidden" name="form_type" value="parent">
            <p><b>Login Details</b></p>
            Username: <input type="text" name="username"><br>
            Password: <input type="text" name="password"><br>
            First Name: <input type="text" name="login_fname"><br>
            Last Name: <input type="text" name="login_lname"><br>
            Status: <input type="text" name="status" value="Parent" readonly><br>
            <p><b>Parent Details</b></p>
            First Name: <input type="text" name="parent_fname"><br>
            Last Name: <input type="text" name="parent_lname"><br>
            Student First Name: <input type="text" name="student_fname"><br>
            Student Last Name: <input type="text" name="student_lname"><br>
            Phone: <input type="text" name="parent_phone"><br>
            Gender: <input type="text" name="parent_gender"><br>
            <input type="submit" value="Submit" style="display: block; margin: 0 auto; padding: 12px 24px; border-radius: 12px; border: 0; background: linear-gradient(#006caa, #00c3ff); color: white; font-weight: 550; font-size: 16px; cursor: pointer;">
        </form>
    </div>

    <!-- Add Teacher -->
    <div id="Tab3" class="tabcontent">
        <h3>Add Teacher Details</h3>
        <form method="post">
            <input type="hidden" name="form_type" value="teacher">
            <p><b>Login Details</b></p>
            Username: <input type="text" name="username"><br>
            Password: <input type="text" name="password"><br>
            First Name: <input type="text" name="login_fname"><br>
            Last Name: <input type="text" name="login_lname"><br>
            Status: <input type="text" name="status" value="Teacher" readonly><br>
            <p><b>Teacher Details</b></p>
            First Name: <input type="text" name="teacher_fname"><br>
            Last Name: <input type="text" name="teacher_lname"><br>
            Address: <input type="text" name="teacher_address"><br>
            Phone: <input type="text" name="teacher_phone"><br>
            Subject: <input type="text" name="teacher_subject"><br>
            Gender: <input type="text" name="teacher_gender"><br>
            Blood Group: <input type="text" name="teacher_blood_group"><br>
            Class: <input type="number" name="teacher_class"><br>
            <input type="submit" value="Submit" style="display: block; margin: 0 auto; padding: 12px 24px; border-radius: 12px; border: 0; background: linear-gradient(#006caa, #00c3ff); color: white; font-weight: 550; font-size: 16px; cursor: pointer;">
        </form>
    </div>

    <!-- Add Admin -->
    <div id="Tab4" class="tabcontent">
        <h3>Add Admin Details</h3>
        <form method="post">
            <input type="hidden" name="form_type" value="admin">
            <p><b>Login Details</b></p>
            Username: <input type="text" name="username"><br>
            Password: <input type="text" name="password"><br>
            First Name: <input type="text" name="login_fname"><br>
            Last Name: <input type="text" name="login_lname"><br>
            Status: <input type="text" name="status" value="Admin" readonly><br>
            <p><b>Admin Details</b></p>
            First Name: <input type="text" name="admin_fname"><br>
            Last Name: <input type="text" name="admin_lname"><br>
            Phone: <input type="text" name="admin_phone"><br>
            Gender: <input type="text" name="admin_gender"><br>
            Blood Group: <input type="text" name="admin_blood_group"><br>
            <input type="submit" value="Submit" style="display: block; margin: 0 auto; padding: 12px 24px; border-radius: 12px; border: 0; background: linear-gradient(#006caa, #00c3ff); color: white; font-weight: 550; font-size: 16px; cursor: pointer;">
        </form>
    </div>

</div>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Set default active tab
    document.getElementsByClassName("tablinks")[0].click();
</script>

</body>
</html>
<?php $conn->close(); ?>