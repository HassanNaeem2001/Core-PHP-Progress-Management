<?php
require '../vendor/autoload.php';
include('../connect.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Fetch data
$sql = "SELECT s.enrollmentno, s.studentname, s.course_code, b.batchcode, st.staffname, 
        DATE_FORMAT(som.awarded_at, '%M %Y') AS awarded_month
        FROM student_of_the_month som
        JOIN studentprogresssystem.student s ON som.studentid = s.studentid
        JOIN studentprogresssystem.batches b ON som.batchcode = b.batchid
        JOIN studentprogresssystem.staff st ON b.batchinstructor = st.staffid
        ORDER BY som.awarded_at DESC";

$result = $conn->query($sql);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ['Enrollment No', 'Name', 'Course Code', 'Batch', 'Faculty', 'Awarded Month'];
$sheet->fromArray($headers, null, 'A1');

// Header style
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Insert data
$row = 2;
while ($r = $result->fetch_assoc()) {
    $sheet->setCellValue("A$row", $r['enrollmentno']);
    $sheet->setCellValue("B$row", $r['studentname']);
    $sheet->setCellValue("C$row", $r['course_code']);
    $sheet->setCellValue("D$row", $r['batchcode']);
    $sheet->setCellValue("E$row", $r['staffname']);
    $sheet->setCellValue("F$row", $r['awarded_month']);
    $row++;
}

// Auto size and border for all data
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
$sheet->getStyle("A2:F" . ($row - 1))->applyFromArray([
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
]);

// Output to browser
$filename = 'student_of_the_month.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
