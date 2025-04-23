<?php
include('../connect.php');
include('headeradmin.php');

// Fetch batches
$batchQuery = mysqli_query($conn, "SELECT * FROM batches");

// Initialize variables
$progressRecords = [];
$month = $year = $batchid = null;

// Filter
if (isset($_POST['batchfilter'], $_POST['monthfilter'], $_POST['yearfilter'])) {
    $batchid = $_POST['batchfilter'];
    $month = $_POST['monthfilter'];
    $year = $_POST['yearfilter'];

    $progressQuery = mysqli_query($conn, "
        SELECT sp.*, s.studentid, s.enrollmentno, s.studentname, s.studentphoneno, s.studentguardianphoneno,
               b.batchcode, st.staffname, MONTHNAME(sp.dateofprogress) AS month, 'Aptech Scheme 33' AS institute 
        FROM studentprogress sp 
        JOIN student s ON sp.studentid = s.studentid 
        JOIN batches b ON s.studentbatch = b.batchid 
        JOIN staff st ON b.batchinstructor = st.staffid 
        WHERE s.studentbatch = '$batchid' 
        AND MONTH(sp.dateofprogress) = '$month' 
        AND YEAR(sp.dateofprogress) = '$year'
    ");

    while ($row = mysqli_fetch_assoc($progressQuery)) {
        $progressRecords[] = $row;
    }
}
?>

<div class="container-fluid mt-4">
    <h4>Filter Student Progress</h4>
    <hr>
    <form method="post">
        <div class="row">
            <div class="col-md-3">
                <select name="batchfilter" class="form-control" required>
                    <option value="" disabled selected>Select Batch</option>
                    <?php while ($row = mysqli_fetch_array($batchQuery)) {
                        echo '<option value="'.$row['batchid'].'" '.($batchid == $row['batchid'] ? 'selected' : '').'>'.$row['batchcode'].'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="monthfilter" class="form-control" required>
                    <option value="" disabled selected>Select Month</option>
                    <?php for ($m = 1; $m <= 12; $m++) {
                        echo '<option value="'.$m.'" '.($month == $m ? 'selected' : '').'>'.date('F', mktime(0, 0, 0, $m, 1)).'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="yearfilter" class="form-control" required>
                    <option value="" disabled selected>Select Year</option>
                    <?php for ($y = date('Y'); $y >= 2000; $y--) {
                        echo '<option value="'.$y.'" '.($year == $y ? 'selected' : '').'>'.$y.'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100">Fetch Progress</button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($progressRecords)) { ?>
<div class="container-fluid mt-4">
    <h4>Student Progress Records</h4>
    <hr>

    <form action="download_progress.php" method="post">
        <input type="hidden" name="batchfilter" value="<?php echo $batchid; ?>">
        <input type="hidden" name="monthfilter" value="<?php echo $month; ?>">
        <input type="hidden" name="yearfilter" value="<?php echo $year; ?>">
        <div class="text-end mb-2">
            <button type="submit" class="btn btn-success">Download Report</button>
        </div>
    </form>

    <div class="table-responsive">
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
                <?php foreach ($progressRecords as $record): 
                    $studentid = $record['studentid'];
                    $totalObtained = $record['assignmentmarks'] + $record['quizmarksinternal'] + $record['practical'] + $record['modular'];
                    $overallPercentage = round(($totalObtained / 400) * 100, 2);
                    $monthName = $record['month'];

                    $studentMsg = "Dear *{$record['studentname']}*,\n\nHere is your progress report for the month of *$monthName*.\n\n".
                    "*Enrollment No:* {$record['enrollmentno']}\n".
                    "*Batch Code:* {$record['batchcode']}\n".
                    "*Faculty:* {$record['staffname']}\n".
                    "*Institute:* {$record['institute']}\n\n".
                    "*Assignment Marks:* ".($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted')."\n".
                    "*Quiz Marks:* ".($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted')."\n".
                    "*Practical Marks:* ".($record['practical'] > 0 ? $record['practical'] : 'Not Conducted')."\n".
                    "*Modular Marks:* ".($record['modular'] > 0 ? $record['modular'] : 'Not Conducted')."\n".
                    "*Overall Percentage:* $overallPercentage%\n".
                    "*Remarks:* ".(!empty($record['remarks']) ? $record['remarks'] : 'No Remarks')."\n\nRegards,\n*Aptech Scheme 33*";

                    $guardianMsg = "Dear Guardian,\n\nThis is the progress report of *{$record['studentname']}* for the month of *$monthName*.\n\n".
                    "*Enrollment No:* {$record['enrollmentno']}\n".
                    "*Batch Code:* {$record['batchcode']}\n".
                    "*Faculty:* {$record['staffname']}\n".
                    "*Institute:* {$record['institute']}\n\n".
                    "*Assignment Marks:* ".($record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted')."\n".
                    "*Quiz Marks:* ".($record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted')."\n".
                    "*Practical Marks:* ".($record['practical'] > 0 ? $record['practical'] : 'Not Conducted')."\n".
                    "*Modular Marks:* ".($record['modular'] > 0 ? $record['modular'] : 'Not Conducted')."\n".
                    "*Overall Percentage:* $overallPercentage%\n".
                    "*Remarks:* ".(!empty($record['remarks']) ? $record['remarks'] : 'No Remarks')."\n\nRegards,\n*Aptech Scheme 33 Center*";

                    $studentURL = "https://wa.me/92".ltrim($record['studentphoneno'], '0')."?text=".urlencode($studentMsg);
                    $guardianURL = "https://wa.me/92".ltrim($record['studentguardianphoneno'], '0')."?text=".urlencode($guardianMsg);

                    // Check if report already sent
                    $studentSent = mysqli_query($conn, "SELECT 1 FROM sent_reports WHERE studentid='$studentid' AND month='$month' AND year='$year' AND report_type='student'");
                    $guardianSent = mysqli_query($conn, "SELECT 1 FROM sent_reports WHERE studentid='$studentid' AND month='$month' AND year='$year' AND report_type='guardian'");
                ?>
                <tr>
                    <td><?= htmlspecialchars($record['enrollmentno']) ?></td>
                    <td><?= htmlspecialchars($record['studentname']) ?></td>
                    <td><?= htmlspecialchars($record['batchcode']) ?></td>
                    <td><?= htmlspecialchars($record['staffname']) ?></td>
                    <td><?= $record['assignmentmarks'] > 0 ? $record['assignmentmarks'] : 'Not Conducted' ?></td>
                    <td><?= $record['quizmarksinternal'] > 0 ? $record['quizmarksinternal'] : 'Not Conducted' ?></td>
                    <td><?= $record['practical'] > 0 ? $record['practical'] : 'Not Conducted' ?></td>
                    <td><?= $record['modular'] > 0 ? $record['modular'] : 'Not Conducted' ?></td>
                    <td><?= !empty($record['remarks']) ? $record['remarks'] : 'No Remarks' ?></td>
                    <td>
                        <div class="d-flex">
                            <?php if (mysqli_num_rows($studentSent)): ?>
                                <button class="btn btn-secondary btn-sm mx-1" disabled>To Student</button>
                            <?php else: ?>
                                <a href="<?= $studentURL ?>" target="_blank"
                                   class="btn btn-success btn-sm mx-1"
                                   onclick="markAsSent('<?= $studentid ?>', '<?= $month ?>', '<?= $year ?>', 'student', this)">
                                   To Student
                                </a>
                            <?php endif; ?>

                            <?php if (mysqli_num_rows($guardianSent)): ?>
                                <button class="btn btn-secondary btn-sm mx-1" disabled>To Guardian</button>
                            <?php else: ?>
                                <a href="<?= $guardianURL ?>" target="_blank"
                                   class="btn btn-primary btn-sm mx-1"
                                   onclick="markAsSent('<?= $studentid ?>', '<?= $month ?>', '<?= $year ?>', 'guardian', this)">
                                   To Guardian
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php } else { ?>
<p class="text-center mt-4">No progress records found for the selected filters.</p>
<?php } ?>

<script>
function markAsSent(studentId, month, year, type, el) {
    fetch('mark_report_sent.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `studentid=${studentId}&month=${month}&year=${year}&type=${type}`
    }).then(res => {
        if (res.ok) {
            el.classList.remove('btn-success', 'btn-primary');
            el.classList.add('btn-secondary');
            el.innerText = 'Sent';
            el.setAttribute('disabled', 'disabled');
            el.removeAttribute('href');
        }
    });
}
</script>
