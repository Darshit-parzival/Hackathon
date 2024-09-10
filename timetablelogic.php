<?php

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

$college_id = $_SESSION['college_id']; // You can change this to whatever ID you need
$sql = "SELECT start_time, end_time FROM colleges WHERE id = $college_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];
} else {
    echo "No results found.";
}

$break_sql = "SELECT break_time, break_duration FROM breaks WHERE college_id = $college_id";
$break_result = $conn->query($break_sql);
$breaks = [];

if ($break_result->num_rows > 0) {
    while ($break_row = $break_result->fetch_assoc()) {
        $break_start_time = $break_row['break_time'];
        $break_duration = $break_row['break_duration']; // in minutes
        $break_end_time = date("H:i", strtotime($break_start_time) + ($break_duration * 60));
        $breaks[] = [
            'start' => $break_start_time,
            'end' => $break_end_time
        ];
    }
} else {
    echo "No breaks found.";
}

function generateTimeSlotsWithBreaks($start_time, $end_time, $breaks)
{
    $timeSlots = [];
    $currentTime = strtotime($start_time);
    $endTime = strtotime($end_time);

    while ($currentTime < $endTime) {
        $slotStart = date("h:i A", $currentTime);
        $nextTime = strtotime('+1 hour', $currentTime);
        $slotEnd = date("h:i A", $nextTime);

        // Check if the current time falls within a break
        $isBreak = false;
        foreach ($breaks as $break) {
            $breakStart = strtotime($break['start']);
            $breakEnd = strtotime($break['end']);
            if ($currentTime >= $breakStart && $currentTime < $breakEnd) {
                $isBreak = true;
                $breakSlot = date("h:i A", $breakStart) . ' - ' . date("h:i A", $breakEnd);
                $timeSlots[] = ['slot' => $breakSlot, 'isBreak' => true];
                $currentTime = $breakEnd;
                break;
            }
        }

        if (!$isBreak) {
            $timeSlots[] = ['slot' => $slotStart . ' - ' . $slotEnd, 'isBreak' => false];
            $currentTime = $nextTime;
        }
    }

    return $timeSlots;
}

$timeSlots = generateTimeSlotsWithBreaks($start_time, $end_time, $breaks);


function getAvailableFaculties() {
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

    // Initialize an array to hold the available faculties for each day
    $available_faculties = [
        'Mon' => [],
        'Tue' => [],
        'Wed' => [],
        'Thu' => [],
        'Fri' => [],
        'Sat' => []
    ];

    // Retrieve available faculties for each day
    foreach ($available_faculties as $day => &$faculties) {
        $sql = "SELECT faculty_id FROM availability WHERE day = '$day'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $faculties[] = $row['faculty_id'];
            }
        }
    }

    // Close connection
    $conn->close();

    return $available_faculties;
}


function getFacultyDetails($faculty_id) {
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

    // Retrieve faculty details
    $faculty_name_sql = "SELECT faculty_name FROM faculties WHERE id = $faculty_id";
    $faculty_name_result = $conn->query($faculty_name_sql);

    if (!$faculty_name_result) {
        echo "Error executing query for faculty ID $faculty_id: " . $conn->error . "<br>";
        return null;
    }

    $faculty_name_row = $faculty_name_result->fetch_assoc();

    // Close connection
    $conn->close();

    return $faculty_name_row ? $faculty_name_row['faculty_name'] : null;
}

// function displayMondayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Mon']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Mon'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

// function displayTuesdayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Tue']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Tue'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

// function displayWednesdayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Wed']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Wed'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

// function displayThursdayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Thu']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Thu'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

// function displayFridayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Fri']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Fri'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

// function displaySaturdayFaculties($timeSlots) {
//     $available_faculties = getAvailableFaculties();
//     $num_faculties = count($available_faculties['Fri']);
//     $faculty_names = [];

//     for ($i = 0; $i < $num_faculties; $i++) {
//         $faculty_names[$i] = getFacultyDetails($available_faculties['Sat'][$i]);
//     }

//     $faculty_count = 0;
//     $result = [];

//     foreach ($timeSlots as $slot) {
//         if (!$slot['isBreak']) {
//             $current_time = $slot['slot'];
//             $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
//             $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
//             $faculty_count++;
//         } else {
//             $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
//         }
//     }

//     return $result;
// }

function displayDayFaculties($day, $timeSlots) {
    $available_faculties = getAvailableFaculties();
    $day_key = ucfirst(strtolower($day)); // Capitalize the first letter of the day
    $num_faculties = count($available_faculties[$day_key]);
    $faculty_names = [];

    for ($i = 0; $i < $num_faculties; $i++) {
        $faculty_names[$i] = getFacultyDetails($available_faculties[$day_key][$i]);
    }

    $faculty_count = 0;
    $result = [];

    foreach ($timeSlots as $slot) {
        if (!$slot['isBreak']) {
            $current_time = $slot['slot'];
            $faculty_name = $num_faculties > 0 ? $faculty_names[$faculty_count % $num_faculties] : 'N/A';
            $result[] = ['time' => $current_time, 'faculty' => $faculty_name];
            $faculty_count++;
        } else {
            $result[] = ['time' => $slot['slot'], 'faculty' => 'Break'];
        }
    }

    return $result;
}
