<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'erp');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$class = $_POST['class'];
$academic_year = $_POST['academic_year'];
$exam_type = $_POST['exam_type'];
$physics = $_POST['physics'];
$chemistry = $_POST['chemistry'];
$biology = $_POST['biology'];
$maths = $_POST['maths'];
$english = $_POST['english'];
$sst = $_POST['sst'];
$hindi = $_POST['hindi'];
$computers = $_POST['computers'];

// Insert data
$sql = "INSERT INTO student_marks (first_name, last_name, class, academic_year, exam_type, physics, chemistry, biology, maths, english, sst, hindi, computers) 
        VALUES ('$first_name', '$last_name', '$class', '$academic_year', '$exam_type', '$physics', '$chemistry', '$biology', '$maths', '$english', '$sst', '$hindi', '$computers')";

if ($conn->query($sql) === TRUE) {
    echo "Marks saved successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
