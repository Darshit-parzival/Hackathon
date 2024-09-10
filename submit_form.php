<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hackathon";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$college_name = $_POST['college'];
$branch_name = $_POST['branch'];
$semester = $_POST['semester'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Insert into colleges table
$sql = "INSERT INTO colleges (college_name, branch_name, semester, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $college_name, $branch_name, $semester, $start_time, $end_time);
$stmt->execute();

// Get the last inserted college ID
$college_id = $stmt->insert_id;

// Insert faculties and availability
if (isset($_POST['faculties'])) {
  foreach ($_POST['faculties'] as $index => $faculty_name) {
    // Insert into faculties table
    $sql = "INSERT INTO faculties (college_id, faculty_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $college_id, $faculty_name);
    $stmt->execute();
    $faculty_id = $stmt->insert_id;

    // Insert into availability table
    if (isset($_POST['availability_' . $index])) {
      foreach ($_POST['availability_' . $index] as $day) {
        $sql = "INSERT INTO availability (faculty_id, day) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $faculty_id, $day);
        $stmt->execute();
      }
    }
  }
}

// Close connection
$stmt->close();
$conn->close();

echo "Data submitted successfully!";
?>
