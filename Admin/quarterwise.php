<?php
include('headeradmin.php');
include('../connect.php');
ob_start();

// Fetch all batch codes from batches table
$batchQuery = "SELECT DISTINCT batchcode FROM batches";
$batchResult = $conn->query($batchQuery);

// Fetch students by selected batch
$students = [];
if (isset($_POST['batchfilter'])) {
    $batchFilter = $_POST['batchfilter'];
    $studentQuery = "SELECT studentid, studentname, enrollmentno FROM student WHERE studentbatch = ?";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $batchFilter);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch student progress
$progressData = [];
$studentDetails = null;

if (!empty($_POST['studentfilter']) && !empty($_POST['quarterfilter'])) {
    $studentId = $_POST['studentfilter'];
    $quarter = $_POST['quarterfilter'];

    $studentInfoQuery = "SELECT s.studentname, s.enrollmentno, b.batchcode 
                         FROM student s 
                         JOIN batches b ON s.studentbatch = b.batchcode 
                         WHERE s.studentid = ?";
    $stmtInfo = $conn->prepare($studentInfoQuery);
    $stmtInfo->bind_param("i", $studentId);
    $stmtInfo->execute();
    $studentDetails = $stmtInfo->get_result()->fetch_assoc();

    $progressQuery = "
        SELECT
            YEAR(dateofprogress) AS year,
            MONTH(dateofprogress) AS month,
            assignmentmarks,
            quizmarksinternal,
            practical,
            modular,
            classes_conducted,
            classes_held,
            remarks
        FROM studentprogress
        WHERE studentid = ? AND QUARTER(dateofprogress) = ?
        ORDER BY dateofprogress ASC
    ";
    $stmt = $conn->prepare($progressQuery);
    $stmt->bind_param("ii", $studentId, $quarter);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $progressData[$row['month']][] = $row;
    }
}
?>

<div class="container-fluid mt-4">
    <h3 class="mb-3">Quarter Wise Progress Reports</h3>
    <hr>

    <!-- Filter Form -->
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="batchfilter">Select Batch</label>
                <select name="batchfilter" id="batchfilter" class="form-control" required>
                    <option value="">Select Batch</option>
                    <?php while ($row = $batchResult->fetch_assoc()): ?>
                        <option value="<?= $row['batchcode']; ?>" <?= (isset($_POST['batchfilter']) && $_POST['batchfilter'] === $row['batchcode']) ? 'selected' : ''; ?>>
                            <?= $row['batchcode']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="studentfilter">Select Student</label>
                <select name="studentfilter" id="studentfilter" class="form-control" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['studentid']; ?>" <?= ($_POST['studentfilter'] ?? '') == $student['studentid'] ? 'selected' : ''; ?>>
                            <?= $student['studentname']; ?> (<?= $student['enrollmentno']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="quarterfilter">Select Quarter</label>
                <select name="quarterfilter" id="quarterfilter" class="form-control" required>
                    <option value="">Select Quarter</option>
                    <?php for ($q = 1; $q <= 4; $q++): ?>
                        <option value="<?= $q; ?>" <?= ($_POST['quarterfilter'] ?? '') == $q ? 'selected' : ''; ?>>Q<?= $q; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
    </form>

    <?php if (!empty($progressData)): ?>
        <div id="student-report" class="bg-white p-4 shadow rounded">
            <h4>Report for <?= $studentDetails['studentname']; ?> (<?= $studentDetails['enrollmentno']; ?>)</h4>
            <h6>Batch: <?= $studentDetails['batchcode']; ?></h6>
            <hr>

            <?php foreach ($progressData as $month => $records): ?>
                <h5>Month: <?= date("F", mktime(0, 0, 0, $month, 1)); ?></h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr style="background-color: black; color: white; font-weight: bold;">
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
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?= $record['assignmentmarks']; ?></td>
                                    <td><?= $record['quizmarksinternal']; ?></td>
                                    <td><?= $record['practical']; ?></td>
                                    <td><?= $record['modular']; ?></td>
                                    <td><?= $record['classes_conducted']; ?></td>
                                    <td><?= $record['classes_held']; ?></td>
                                    <td><?= $record['remarks']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <button class="btn btn-success" onclick="downloadReportImage()">Download Report as Image</button>
        </div>
    <?php endif; ?>
</div>

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
     document.getElementById('batchfilter').addEventListener('change', function () {
        this.form.submit();
    });
function downloadReportImage() {
    const report = document.getElementById('student-report');
    html2canvas(report).then(canvas => {
        const link = document.createElement('a');
        link.download = 'quarterly-student-report.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}
</script>

<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<?php include('footeradmin.php'); ?>
