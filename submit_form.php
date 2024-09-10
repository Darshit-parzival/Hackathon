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
$classrooms= $_POST['classrooms'];
$batches = $_POST['batches'];

// Insert into colleges table
$sql = "INSERT INTO colleges (college_name, branch_name, semester, start_time, end_time, classrooms, batches) VALUES (?, ?, ?, ?, ?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssissii", $college_name, $branch_name, $semester, $start_time, $end_time, $classrooms, $batches);
$stmt->execute();

// Get the last inserted college ID
$college_id = $stmt->insert_id;

// Insert subjects
if (isset($_POST['subjects']) && isset($_POST['subjectHours'])) {
    $subjects = $_POST['subjects'];
    $subjectcodes = $_POST['subjectcodes'];
    $subjectHours = $_POST['subjectHours'];

    for ($i = 0; $i < count($subjects); $i++) {
        $id = $subjectcodes[$i];
        $subject_name = $subjects[$i];
        $hours_per_week = $subjectHours[$i];

        $sql = "INSERT INTO subjects (id, college_id, subject_name, hours_per_week) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $id, $college_id, $subject_name, $hours_per_week);
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
    $subjectcodes = $_POST['codes'];
    foreach ($_POST['faculties'] as $index => $faculty_name) {
        $id = $subjectcodes[$index];
        // Insert into faculties table
        $sql = "INSERT INTO faculties (college_id, faculty_name, subject_code) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $college_id, $faculty_name, $id);
        $stmt->execute();
        $faculty_id = $stmt->insert_id; // Get the inserted faculty ID

        // Handle dynamic availability checkboxes
        $availability_key = 'availability_faculty-' . $index; // Matching the dynamically generated name from form

        // Check if the availability key exists in the form submission
        if (isset($_POST[$availability_key])) {
            foreach ($_POST[$availability_key] as $day) {
                // Insert into availability table
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
