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
        SELECT sp.*, s.enrollmentno, s.studentname, s.studentphoneno, b.batchcode, st.staffname, 
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($progressRecords as $record) { 
                $whatsappMessage = "Dear **" . $record['studentname'] . "**,\n\n" .
                "Here is your progress report for the month of **" . $record['month'] . "**.\n\n" .
                "**Enrollment No:** " . $record['enrollmentno'] . "\n" .
                "**Batch Code:** " . $record['batchcode'] . "\n" .
                "**Faculty:** " . $record['staffname'] . "\n" .
                "**Institute:** " . $record['institute'] . "\n\n" .
                "**Assignment Marks:** " . ($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted') . "\n" .
                "**Quiz Marks:** " . ($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted') . "\n" .
                "**Practical Marks:** " . ($record['practical'] > 0 ? $record['practical'] : 'Not Conducted') . "\n" .
                "**Modular Marks:** " . ($record['modular'] > 0 ? $record['modular'] : 'Not Conducted') . "\n" .
                "**Remarks:** " . (!empty($record['remarks']) ? $record['remarks'] : 'No Remarks') . "\n\n" .
                "Regards,\n**Aptech Scheme 33**";

                $whatsappURL = "https://wa.me/92" . ltrim($record['studentphoneno'], '0') . "?text=" . urlencode($whatsappMessage);
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
                        <a href="<?php echo $whatsappURL; ?>" target="_blank" class="btn btn-success">Send Report</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php include('footeradmin.php'); ?>
