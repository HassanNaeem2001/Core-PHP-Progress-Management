<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connect.php');
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

if (isset($_POST['download_report'])) {
    $month = $_POST['monthfilter'] ?? '';
    $year = $_POST['yearfilter'] ?? '';

    if (empty($month) || empty($year)) {
        die("Month and Year are required.");
    }

    // Convert numeric month to full name
    $monthName = date('F', mktime(0, 0, 0, $month, 1));

    // Fetch Data for Active Students only
    $query = "SELECT s.enrollmentno, s.studentname, b.batchcode, f.staffname AS faculty, 
                     sp.assignmentmarks, sp.quizmarksinternal, sp.practical, sp.modular,
                     sp.classes_conducted, sp.classes_held, sp.remarks
              FROM studentprogress sp
              JOIN student s ON sp.studentid = s.studentid
              JOIN batches b ON s.studentbatch = b.batchid
              JOIN staff f ON b.batchinstructor = f.staffid
              WHERE MONTH(sp.dateofprogress) = '$month' 
              AND YEAR(sp.dateofprogress) = '$year'
              AND s.studentstatus = 'Active'"; // Only select Active students

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }

    // Create Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Headers
    $headers = ['Enrollment No', 'Student Name', 'Batch Code', 'Faculty', 'Month', 'Center Name', 
                'Assignment Marks', 'Quiz Marks', 'Practical Marks', 'Modular Marks', 
                'Classes Conducted', 'Classes Held', 'Remarks', 'Overall Percentage'];
    
    // Apply Header Style
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']]
    ];

    // Set Header
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        $col++;
    }

    // Data
    $row = 2;
    while ($record = mysqli_fetch_assoc($result)) {
        // Calculate Attendance Percentage
        $attendancePercentage = ($record['classes_held'] > 0) 
            ? round(($record['classes_conducted'] / $record['classes_held']) * 100, 2) 
            : 0;

        // Assignment Marks (out of 100)
        $assignmentMarks = $record['assignmentmarks'] ?? 0;  

        // Quiz Marks (out of 100)
        $quizMarks = $record['quizmarksinternal'] ?? 0;  

        // Calculate Overall Weighted Percentage
        $overallPercentage = round(
            ($assignmentMarks * 0.40) + 
            ($attendancePercentage * 0.30) + 
            ($quizMarks * 0.30), 
            2
        );

        $sheet->setCellValue('A' . $row, $record['enrollmentno']);
        $sheet->setCellValue('B' . $row, $record['studentname']);
        $sheet->setCellValue('C' . $row, $record['batchcode']);
        $sheet->setCellValue('D' . $row, $record['faculty']);
        $sheet->setCellValue('E' . $row, $monthName); // Added Month after Faculty
        $sheet->setCellValue('F' . $row, 'Aptech Scheme 33'); // Center Name
        $sheet->setCellValue('G' . $row, $assignmentMarks);
        $sheet->setCellValue('H' . $row, $quizMarks);
        $sheet->setCellValue('I' . $row, $record['practical']);
        $sheet->setCellValue('J' . $row, $record['modular']);
        $sheet->setCellValue('K' . $row, $record['classes_conducted']);
        $sheet->setCellValue('L' . $row, $record['classes_held']);
        $sheet->setCellValue('M' . $row, !empty($record['remarks']) ? $record['remarks'] : 'No Remarks');
        $sheet->setCellValue('N' . $row, $overallPercentage . '%');
        $row++;
    }

    // Auto-adjust column width
    foreach (range('A', 'N') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // File Output
    $filename = "Student_Progress_{$monthName}_{$year}.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}
?>
