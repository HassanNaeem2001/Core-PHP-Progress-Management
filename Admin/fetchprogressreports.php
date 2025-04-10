<?php
include('../connect.php');
include('headeradmin.php');

// Fetch batches for dropdown
$batchQuery = mysqli_query($conn, "SELECT * FROM batches");

// Initialize variables
$progressRecords = [];

// Fetch progress based on filters
if (isset($_POST['batchfilter'], $_POST['monthfilter'], $_POST['yearfilter'])) {
    $batchid = $_POST['batchfilter'];
    $month = $_POST['monthfilter'];
    $year = $_POST['yearfilter'];
    
    $progressQuery = mysqli_query($conn, "
    SELECT sp.progressno, sp.*, s.enrollmentno, s.studentname, s.studentphoneno, 
           s.studentguardianphoneno, b.batchcode, st.staffname, 
           MONTHNAME(sp.dateofprogress) AS month, 'Aptech Scheme 33' AS institute 
    FROM studentprogress sp 
    JOIN student s ON sp.studentid = s.studentid 
    JOIN batches b ON s.studentbatch = b.batchid 
    JOIN staff st ON b.batchinstructor = st.staffid 
    WHERE s.studentbatch = '$batchid' 
    AND MONTH(sp.dateofprogress) = '$month' 
    AND YEAR(sp.dateofprogress) = '$year'");

    
    while ($row = mysqli_fetch_assoc($progressQuery)) {
        $progressRecords[] = $row;
    }
}
?>

<!-- Batch, Month, and Year Selection Form -->
<div class="container-fluid mt-4">
    <h4>Filter Student Progress</h4>
    <hr>
    <form method="post">
        <div class="row">
            <div class="col-md-3">
                <select name="batchfilter" class="form-control" required>
                    <option value="" selected disabled>Select Batch</option>
                    <?php while ($row = mysqli_fetch_array($batchQuery)) {
                        echo '<option value="'.$row['batchid'].'">'.$row['batchcode'].'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="monthfilter" class="form-control" required>
                    <option value="" selected disabled>Select Month</option>
                    <?php for ($m = 1; $m <= 12; $m++) {
                        echo '<option value="'.$m.'">'.date('F', mktime(0, 0, 0, $m, 1)).'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="yearfilter" class="form-control" required>
                    <option value="" selected disabled>Select Year</option>
                    <?php for ($y = date('Y'); $y >= 2000; $y--) {
                        echo '<option value="'.$y.'">'.$y.'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Fetch Progress</button>
            </div>
        </div>
    </form>
</div>

<!-- Student Progress Table -->
<?php if (!empty($progressRecords)) { ?>
<div class="container-fluid mt-4">
    <h4>Student Progress Records</h4>
    <hr>
    
    <!-- Download Report Form -->
    <form action="download_progress.php" method="post">
        <input type="hidden" name="batchfilter" value="<?php echo $_POST['batchfilter']; ?>">
        <input type="hidden" name="monthfilter" value="<?php echo $_POST['monthfilter']; ?>">
        <input type="hidden" name="yearfilter" value="<?php echo $_POST['yearfilter']; ?>">
        <div class="text-end mb-2">
            <button type="submit" class="btn btn-success">Download Report</button>
        </div>
    </form>
    
    <div class="table table-responsive">
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
                <th>Remarks</th>
                <th>Send Report</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($progressRecords as $record) { 
    // Calculate Overall Percentage
    $maxAssignmentMarks = 100;
    $maxQuizMarks = 100;
    $maxPracticalMarks = 100;
    $maxModularMarks = 100;
    
    // Calculate total marks obtained by the student
    $totalMarksObtained = $record['assignmentmarks'] + $record['quizmarksinternal'] + $record['practical'] + $record['modular'];

    // Calculate total possible marks
    $totalPossibleMarks = $maxAssignmentMarks + $maxQuizMarks + $maxPracticalMarks + $maxModularMarks;

    // Calculate overall percentage
    $overallPercentage = ($totalMarksObtained / $totalPossibleMarks) * 100;

    // Round the percentage to 2 decimal places
    $overallPercentage = round($overallPercentage, 2);

    // Message for Student
    $studentMessage = "Dear *" . $record['studentname'] . "*,\n\n" .
    "Here is your progress report for the month of *" . $record['month'] . "*.\n\n" .
    "*Enrollment No:* " . $record['enrollmentno'] . "\n" .
    "*Batch Code:* " . $record['batchcode'] . "\n" .
    "*Faculty:* " . $record['staffname'] . "\n" .
    "*Institute:* " . $record['institute'] . "\n\n" .
    "*Assignment Marks:* " . ($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted') . "\n" .
    "*Quiz Marks:* " . ($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted') . "\n" .
    "*Practical Marks:* " . ($record['practical'] > 0 ? $record['practical'] : 'Not Conducted') . "\n" .
    "*Modular Marks:* " . ($record['modular'] > 0 ? $record['modular'] : 'Not Conducted') . "\n" .
    "*Overall Percentage:* " . $overallPercentage . "%\n" .
    "*Remarks:* " . (!empty($record['remarks']) ? $record['remarks'] : 'No Remarks') . "\n\n" .
    "Regards,\n*Aptech Scheme 33*";

    // Message for Guardian
    $guardianMessage = "Dear Guardian,\n\n" .
    "This is the progress report of *" . $record['studentname'] . "* for the month of *" . $record['month'] . "*.\n\n" .
    "*Enrollment No:* " . $record['enrollmentno'] . "\n" .
    "*Batch Code:* " . $record['batchcode'] . "\n" .
    "*Faculty:* " . $record['staffname'] . "\n" .
    "*Institute:* " . $record['institute'] . "\n\n" .
    "*Assignment Marks:* " . ($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted') . "\n" .
    "*Quiz Marks:* " . ($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted') . "\n" .
    "*Practical Marks:* " . ($record['practical'] > 0 ? $record['practical'] : 'Not Conducted') . "\n" .
    "*Modular Marks:* " . ($record['modular'] > 0 ? $record['modular'] : 'Not Conducted') . "\n" .
    "*Overall Percentage:* " . $overallPercentage . "%\n" .
    "*Remarks:* " . (!empty($record['remarks']) ? $record['remarks'] : 'No Remarks') . "\n\n" .
    "Regards,\n*Aptech Scheme 33 Center*";

    // Generate WhatsApp URLs for student and guardian
    $studentPhone = "https://wa.me/92" . ltrim($record['studentphoneno'], '0') . "?text=" . urlencode($studentMessage);
    $guardianPhone = "https://wa.me/92" . ltrim($record['studentguardianphoneno'], '0') . "?text=" . urlencode($guardianMessage);
?>

<tr>
    <td><?php echo $record['enrollmentno']; ?></td>
    <td><?php echo $record['studentname']; ?></td>
    <td><?php echo $record['batchcode']; ?></td>
    <td><?php echo $record['staffname']; ?></td>
    <td><?php echo ($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted'); ?></td>
    <td><?php echo ($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted'); ?></td>
    <td><?php echo ($record['practical'] > 0 ? $record['practical'] : 'Not Conducted'); ?></td>
    <td><?php echo ($record['modular'] > 0 ? $record['modular'] : 'Not Conducted'); ?></td>
    <td><?php echo (!empty($record['remarks']) ? $record['remarks'] : 'No Remarks'); ?></td>
    <td>
        <div class="d-flex">
            <a href="<?php echo $studentPhone; ?>" target="_blank" class="btn btn-success btn-sm mx-2">To Student</a>
            <a href="<?php echo $guardianPhone; ?>" target="_blank" class="btn btn-primary btn-sm">To Guardian</a>
        </div>
    </td>
</tr>

<?php } ?>


        </tbody>
    </table>
    </div>
</div>
<?php } ?>

<?php include('footeradmin.php'); ?>