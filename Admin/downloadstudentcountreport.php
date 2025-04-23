<?php
require '../vendor/autoload.php';
include('../connect.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

// Fetch data using proper joins to get faculty name
$query = "
    SELECT 
        s.studentname, 
        s.enrollmentno, 
        s.course_code,
        b.batchcode,
        st.staffname AS faculty,
        s.studentstatus
    FROM student s
    LEFT JOIN batches b ON s.studentbatch = b.batchid
    LEFT JOIN staff st ON b.batchinstructor = st.staffid
    WHERE s.studentstatus = '$status'
    GROUP BY s.studentid
";

$result = mysqli_query($conn, $query);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$headers = ['S.No', 'Name', 'Enrollment No', 'Course Code', 'Batch Code', 'Faculty', 'Status'];
$sheet->fromArray($headers, NULL, 'A1');

// Style header row
$sheet->getStyle('A1:G1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '000000']]
]);

// Fill data rows
$rowNum = 2;
$serial = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A{$rowNum}", $serial);
    $sheet->setCellValue("B{$rowNum}", $row['studentname']);
    $sheet->setCellValue("C{$rowNum}", $row['enrollmentno']);
    $sheet->setCellValue("D{$rowNum}", $row['course_code']);
    $sheet->setCellValue("E{$rowNum}", $row['batchcode']);
    $sheet->setCellValue("F{$rowNum}", $row['faculty']);
    $sheet->setCellValue("G{$rowNum}", $row['studentstatus']);
    $rowNum++;
    $serial++;
}

// Auto adjust column widths
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
