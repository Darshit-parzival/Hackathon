<?php
session_start();

ob_start(); // Start output buffering

require 'fpdf186/fpdf.php';
require 'timetablelogic.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Dynamic Time Table', 0, 1, 'C');
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Time Table table
    function TimeTable($days, $timeToFaculties)
    {
        // Table header
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 10, 'Time', 1, 0, 'C');
        foreach ($days as $day) {
            $this->Cell(30, 10, $day, 1, 0, 'C');
        }
        $this->Ln();

        // Table body
        $this->SetFont('Arial', '', 10);
        foreach ($timeToFaculties as $time => $facultiesByDay) {
            $this->Cell(30, 10, $time, 1);
            foreach ($facultiesByDay as $faculty) {
                $this->Cell(30, 10, $faculty, 1);
            }
            $this->Ln();
        }
    }
}

// Initialize the days array
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; // List of days

// Fetch timetable data
$timeToFaculties = [];
foreach ($days as $day) {
    $faculties = displayDayFaculties($day, $timeSlots);
    foreach ($faculties as $entry) {
        $time = $entry['time'];
        if (!isset($timeToFaculties[$time])) {
            $timeToFaculties[$time] = array_fill(0, count($days), ''); // Initialize array for each day
        }
        $dayIndex = array_search($day, $days);
        $timeToFaculties[$time][$dayIndex] = $entry['faculty'] . '-' . $entry['subject'] . ' - ' . $entry['subject_code'];
    }
}

// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Add the timetable
$pdf->TimeTable($days, $timeToFaculties);

// Output the PDF directly without any extra output before this
$pdf->Output('D', 'timetable.pdf'); // D for Download

ob_end_clean(); // Clean the output buffer

exit(); // End script after PDF is generated
