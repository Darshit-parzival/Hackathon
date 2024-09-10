<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        thead {
            background-color: #f8f9fa;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <title>Time Table</title>
</head>

<body>
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

    // Fetch college_id from session
    if (!isset($_SESSION['college_id'])) {
        die("No college_id found in session.");
    }
    $college_id = $_SESSION['college_id'];

    // Fetch college data
    $sql = "SELECT * FROM colleges WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $college_result = $stmt->get_result();
    if ($college_result === false) {
        die("Query failed: " . $conn->error);
    }
    $college = $college_result->fetch_assoc();
    $start_time = new DateTime($college['start_time']);
    $end_time = new DateTime($college['end_time']);

    // Fetch subjects
    $sql = "SELECT * FROM subjects WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $subjects_result = $stmt->get_result();
    if ($subjects_result === false) {
        die("Query failed: " . $conn->error);
    }
    $subjects = [];
    while ($row = $subjects_result->fetch_assoc()) {
        $subjects[] = $row;
    }

    // Fetch breaks
    $sql = "SELECT * FROM breaks WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $breaks_result = $stmt->get_result();
    if ($breaks_result === false) {
        die("Query failed: " . $conn->error);
    }
    $breaks = [];
    while ($row = $breaks_result->fetch_assoc()) {
        $breaks[] = $row;
    }

    // Generate timetable
    echo '<div class="container">';
    echo '<h1>Dynamic Time Table</h1>';
    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Time</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th></tr></thead>';
    echo '<tbody>';

    // Initialize time slots
    $time_slots = [];
    $current_time = clone $start_time;
    while ($current_time < $end_time) {
        $end_slot_time = clone $current_time;
        $end_slot_time->modify('+1 hour');

        // Check if the current slot overlaps with any break
        $overlaps_with_break = false;
        foreach ($breaks as $break) {
            $break_start_time = new DateTime($break['break_time']);
            $break_end_time = clone $break_start_time;
            $break_end_time->modify('+' . $break['break_duration'] . ' minutes');
            if (($current_time < $break_end_time) && ($end_slot_time > $break_start_time)) {
                $overlaps_with_break = true;
                break;
            }
        }

        // Only add time slot if it does not overlap with any break
        if (!$overlaps_with_break) {
            $time_slots[] = [
                'start' => $current_time->format('g:i A'),
                'end' => $end_slot_time->format('g:i A'),
            ];
        }
        $current_time->modify('+1 hour');
    }

    // Ensure each subject is covered and manage repetitions
    $subject_count = count($subjects);
    $max_repeats = 2;
    $subjects_per_day = ceil(count($time_slots) / 5); // Assuming 5 days a week

    foreach ($time_slots as $slot) {
        echo '<tr>';
        echo "<td>{$slot['start']} - {$slot['end']}</td>";

        $daily_subjects = [];
        $subject_indexes = array_keys($subjects);

        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day) {
            // Assign subjects for the day
            $subject_for_day = 'Free'; // Default value

            if ($subject_count > 0) {
                // Pick a subject that hasn't been used today
                do {
                    $random_index = array_rand($subject_indexes);
                    $subject_index = $subject_indexes[$random_index];
                    $subject_for_day = $subjects[$subject_index]['subject_name'];
                } while (in_array($subject_for_day, $daily_subjects) && $repeat_count >= $max_repeats);

                if (in_array($subject_for_day, $daily_subjects)) {
                    $repeat_count++;
                } else {
                    $repeat_count = 0;
                }

                $daily_subjects[] = $subject_for_day;
            }

            echo "<td>{$subject_for_day}</td>";
        }

        echo '</tr>';
    }

    // Add break times to timetable
    foreach ($breaks as $break) {
        $break_start_time = new DateTime($break['break_time']);
        $break_end_time = clone $break_start_time;
        $break_end_time->modify('+' . $break['break_duration'] . ' minutes');
        echo '<tr><td colspan="6">Break: ' . $break_start_time->format('g:i A') . ' - ' . $break_end_time->format('g:i A') . '</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    $stmt->close();
    $conn->close();
    ?>

</body>


</html>