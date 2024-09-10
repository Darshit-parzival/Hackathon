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
$break_time = $_POST['break_time'];
$break_duration = $_POST['break_duration'];

// Insert into colleges table
$sql = "INSERT INTO colleges (college_name, branch_name, semester, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $college_name, $branch_name, $semester, $start_time, $end_time);
$stmt->execute();

// Get the last inserted college ID
$college_id = $stmt->insert_id;

// Insert subjects
if (isset($_POST['subjects']) && isset($_POST['subjectHours'])) {
  $subjects = $_POST['subjects'];
  $subjectHours = $_POST['subjectHours'];

  for ($i = 0; $i < count($subjects); $i++) {
    $subject_name = $subjects[$i];
    $hours_per_week = $subjectHours[$i];

    $sql = "INSERT INTO subjects (college_id, subject_name, hours_per_week) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $college_id, $subject_name, $hours_per_week);
    $stmt->execute();
  }
}

// Insert breaks
$sql = "INSERT INTO breaks (college_id, break_time, break_duration) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $college_id, $break_time, $break_duration);
$stmt->execute();

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
