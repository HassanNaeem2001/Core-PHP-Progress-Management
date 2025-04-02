<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../connect.php');
require '../vendor/autoload.php'; // Ensure PHPSpreadsheet is installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Fetch the month and year from POST
$month = $_POST['monthfilter'] ?? '';
$year = $_POST['yearfilter'] ?? '';

$query = "SELECT s.enrollmentno, s.studentname, b.batchcode, f.staffname AS faculty, 
                 MONTHNAME(sp.dateofprogress) AS month, 'Aptech Scheme 33' AS institute,
                 sp.assignmentmarks, sp.quizmarksinternal, sp.practical, sp.modular,
                 sp.classes_conducted, sp.classes_held, sp.remarks
          FROM studentprogress sp
          JOIN student s ON sp.studentid = s.studentid
          JOIN batches b ON s.studentbatch = b.batchid
          JOIN staff f ON b.batchinstructor = f.staffid
          WHERE MONTH(sp.dateofprogress) = '$month' AND YEAR(sp.dateofprogress) = '$year'";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Query Failed: " . mysqli_error($conn)); // Debug SQL errors
}
?>

<?php
include('headeradmin.php');
?>
<div class="container mt-4">
    <h4>Select Month and Year for Student Progress</h4>
    <hr>
    <form method="post">
        <div class="row">
            <div class="col-md-3">
                <select name="monthfilter" class="form-control" required>
                    <option value="" selected disabled>Select Month</option>
                    <?php for ($m = 1; $m <= 12; $m++) {
                        $selected = ($m == $month) ? 'selected' : '';
                        echo '<option value="'.$m.'" '.$selected.'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="yearfilter" class="form-control" required>
                    <option value="" selected disabled>Select Year</option>
                    <?php for ($y = date('Y'); $y >= 2000; $y--) {
                        $selected = ($y == $year) ? 'selected' : '';
                        echo '<option value="'.$y.'" '.$selected.'>'.$y.'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Fetch Progress</button>
            </div>
        </div>
    </form>
</div>

<?php if ($month && $year && mysqli_num_rows($result) > 0) { ?>
<div class="container mt-4">
    <h4>Student Progress Records for <?php echo date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year; ?></h4>
    <hr>
    
    <form action="download_report.php" method="post">
        <input type="hidden" name="monthfilter" value="<?php echo $month; ?>">
        <input type="hidden" name="yearfilter" value="<?php echo $year; ?>">
        <div class="text-end mb-2">
            <button type="submit" class="btn btn-success" name="download_report">Download Report</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Enrollment No</th>
                <th>Student Name</th>
                <th>Batch Code</th>
                <th>Faculty</th>
                <th>Assignment Marks</th>
                <th>Quiz Marks</th>
                <th>Practical Marks</th>
                <th>Modular Marks</th>
                <th>Classes Conducted</th>
                <th>Classes Held</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($record = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $record['enrollmentno']; ?></td>
                <td><?php echo $record['studentname']; ?></td>
                <td><?php echo $record['batchcode']; ?></td>
                <td><?php echo $record['faculty']; ?></td>
                <td><?php echo $record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted'; ?></td>
                <td><?php echo $record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted'; ?></td>
                <td><?php echo $record['practical'] > 0 ? $record['practical'] : 'Not Conducted'; ?></td>
                <td><?php echo $record['modular'] > 0 ? $record['modular'] : 'Not Conducted'; ?></td>
                <td><?php echo $record['classes_conducted'] > 0 ? $record['classes_conducted'] : 'N/A'; ?></td>
                <td><?php echo $record['classes_held'] > 0 ? $record['classes_held'] : 'N/A'; ?></td>
                <td><?php echo !empty($record['remarks']) ? $record['remarks'] : 'No Remarks'; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
    <div class="container mt-4">
        <p class="text-danger">No progress records found for the selected month and year.</p>
    </div>
<?php } ?>

<?php include('footeradmin.php'); ?>
