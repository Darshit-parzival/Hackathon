<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}

require 'timetablelogic.php';

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
    <title>Time Table2</title>
</head>

<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Welcome, <?php echo $_SESSION['user_name'] ?></a>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <button class="nav-link px-3" style="background-color: #333;" onclick="window.location.href='logout.php'">Sign out</button>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">
                                <span data-feather="home"></span>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file"></span>
                                Semester 1
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="shopping-cart"></span>
                                Semester 2
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">For Non technical</h1>
                </div>
                <div class="container">
                    <h1>Dynamic Time Table</h1>
                    <?php
                    // Initialize the days array
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; // List of days

                    // Assume $timeSlots is an array of time slots
                    $timeToFaculties = [];

                    // Fetch faculties for each day and organize by time
                    foreach ($days as $day) {
                        $faculties = displayDayFaculties($day, $timeSlots);
                        foreach ($faculties as $entry) {
                            $time = $entry['time'];
                            if (!isset($timeToFaculties[$time])) {
                                $timeToFaculties[$time] = array_fill(0, count($days), ''); // Initialize array for each day
                            }
                            // Map faculty or 'Break' to the correct day index
                            $dayIndex = array_search($day, $days);
                            $timeToFaculties[$time][$dayIndex] = $entry['faculty'] . '-' . $entry['subject'] . ' - ' . $entry['subject_code'];
                        }
                    }
                    ?>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <?php foreach ($days as $day): ?>
                                    <th><?php echo htmlspecialchars($day); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Output all entries in a single table
                            foreach ($timeToFaculties as $time => $facultiesByDay) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($time) . '</td>'; // Display time slot
                                foreach ($facultiesByDay as $faculty) {
                                    echo '<td>' . htmlspecialchars($faculty) . '</td>'; // Display faculty or 'Break'
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>

                </div>

                <button class="btn btn-primary" onclick="window.location.href='export_pdf.php'">Export as PDF</button>
            </main>
        </div>
    </div>
</body>


</html>