<?php
include('../connect.php');
require '../vendor/autoload.php'; // Make sure you have PHPSpreadsheet installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Fetch records based on filters
$batchid = $_POST['batchfilter'] ?? '';
$month = $_POST['monthfilter'] ?? '';

$query = "SELECT s.enrollmentno, s.studentname, b.batchcode, f.staffname AS faculty, 
                 MONTHNAME(sp.dateofprogress) AS month, 'Aptech Scheme 33' AS institute,
                 sp.assignmentmarks, sp.quizmarksinternal, sp.practical, sp.modular
          FROM studentprogress sp
          JOIN student s ON sp.studentid = s.studentid
          JOIN batches b ON s.studentbatch = b.batchid
          JOIN staff f ON b.batchinstructor = f.staffid
          WHERE s.studentbatch = '$batchid' AND MONTH(sp.dateofprogress) = '$month'";

$result = mysqli_query($conn, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ["Enrollment No", "Student Name", "Batch Code", "Faculty", "Month", "Institute", "Assignment Marks", "Quiz Marks", "Practical Marks", "Modular Marks"];
$sheet->fromArray([$headers], NULL, 'A1');

// Apply styling to headers
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
];
$sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

// Fill data
$row = 2;
while ($record = mysqli_fetch_assoc($result)) {
    $sheet->fromArray([
        $record['enrollmentno'],
        $record['studentname'],
        $record['batchcode'],
        $record['faculty'],
        $record['month'],
        $record['institute'],
        ($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted'),
        ($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted'),
        ($record['practical'] > 0 ? $record['practical'] : 'Not Conducted'),
        ($record['modular'] > 0 ? $record['modular'] : 'Not Conducted')
    ], NULL, "A$row");
    $row++;
}

// Set column width for readability
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="student_progress.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
