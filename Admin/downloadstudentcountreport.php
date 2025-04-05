<?php
require '../vendor/autoload.php'; // Adjust path if needed
include('../connect.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

$type = $_GET['type'];

switch ($type) {
    case 'active':
        $status = 'Active';
        $filename = 'active_students.xlsx';
        break;
    case 'dropout':
        $status = 'Dropout';
        $filename = 'dropout_students.xlsx';
        break;
    case 'coursecom':
        $status = 'Course Com';
        $filename = 'course_completed_students.xlsx';
        break;
    default:
        die("Invalid report type.");
}

// Fetch data
$query = "
    SELECT 
        s.studentname, 
        s.enrollmentno, 
        s.studentemail, 
        b.batchcode AS batchcode, 
        sc.faculty,
        s.studentstatus 
    FROM student s
    LEFT JOIN batches b ON s.studentbatch = b.batchid
    LEFT JOIN student_complaints sc ON sc.student_name = s.studentname
    WHERE s.studentstatus = '$status'
    GROUP BY s.studentid
";

$result = mysqli_query($conn, $query);

// Initialize spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Headers
$headers = ['Name', 'Enrollment No', 'Email', 'Batch Code', 'Faculty', 'Status'];
$sheet->fromArray($headers, NULL, 'A1');

// Style headers
$styleArray = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '000000'],
    ],
];

$sheet->getStyle('A1:F1')->applyFromArray($styleArray);

// Add data
$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A{$rowNum}", $row['studentname']);
    $sheet->setCellValue("B{$rowNum}", $row['enrollmentno']);
    $sheet->setCellValue("C{$rowNum}", $row['studentemail']);
    $sheet->setCellValue("D{$rowNum}", $row['batchcode']);
    $sheet->setCellValue("E{$rowNum}", $row['faculty']);
    $sheet->setCellValue("F{$rowNum}", $row['studentstatus']);
    $rowNum++;
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
