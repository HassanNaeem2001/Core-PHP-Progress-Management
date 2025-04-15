<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('headeradmin.php');
include('../connect.php');
ob_start();

function safe($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$enrollmentNo = '';
$selectedQuarter = '';
$studentDetails = null;
$progressData = [];

$quarterMonths = [
    1 => [1, 2, 3],
    2 => [4, 5, 6],
    3 => [7, 8, 9],
    4 => [10, 11, 12],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollmentNo = trim($_POST['enrollmentno'] ?? '');
    $selectedQuarter = $_POST['quarter'] ?? '';

    if (!empty($enrollmentNo)) {
        // Get student info + batch data (currentsem, instructor)
        $stmt = $conn->prepare("
            SELECT s.studentid, s.studentname, s.enrollmentno, s.studentbatch, 
                   b.batchcode, b.currentsem, b.batchinstructor
            FROM student s
            LEFT JOIN batches b ON s.studentbatch = b.batchcode
            WHERE s.enrollmentno = ?
        ");
        $stmt->bind_param("s", $enrollmentNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $studentDetails = $result->fetch_assoc();
        $stmt->close();

        if ($studentDetails) {
            $studentId = $studentDetails['studentid'];
            $stmt = $conn->prepare("
                SELECT YEAR(dateofprogress) AS year, MONTH(dateofprogress) AS month,
                       assignmentmarks, quizmarksinternal, modular, practical,
                       classes_conducted, classes_held, remarks 
                FROM studentprogress 
                WHERE studentid = ?
                ORDER BY dateofprogress ASC
            ");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $month = (int)$row['month'];
                if ($selectedQuarter && in_array($month, $quarterMonths[$selectedQuarter])) {
                    $progressData[$month][] = $row;
                }
            }
            $stmt->close();
        }
    }
}
?>

<style>
    .table thead th {
        background-color: orange !important;
        color: white !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    h5.section-heading {
        background: orange;
        color: white;
        padding: 10px;
        margin-top: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>

<div class="container mt-4">
    <h3 class="text-center mb-4">Student Progress Report (Quarter-wise)</h3>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="enrollmentno" class="form-label">Enrollment Number</label>
            <input type="text" name="enrollmentno" id="enrollmentno" class="form-control" required value="<?= safe($enrollmentNo) ?>">
        </div>
        <div class="col-md-4">
            <label for="quarter" class="form-label">Select Quarter</label>
            <select name="quarter" id="quarter" class="form-select" required>
                <option value="">-- Select Quarter --</option>
                <option value="1" <?= $selectedQuarter == '1' ? 'selected' : '' ?>>Quarter 1 (Jan - Mar)</option>
                <option value="2" <?= $selectedQuarter == '2' ? 'selected' : '' ?>>Quarter 2 (Apr - Jun)</option>
                <option value="3" <?= $selectedQuarter == '3' ? 'selected' : '' ?>>Quarter 3 (Jul - Sep)</option>
                <option value="4" <?= $selectedQuarter == '4' ? 'selected' : '' ?>>Quarter 4 (Oct - Dec)</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-dark w-100">Generate Report</button>
        </div>
    </form>

    <?php if ($studentDetails && !empty($progressData)): ?>
        <div id="report" style="border: 2px solid #000; padding: 20px; background: #f9f9f9;">
            <div style="text-align: center;">
                <img src="../Images/aptlogo.png" alt="Aptech Logo" style="height: 150px;">
                <h4>APTECH LEARNING</h4>
                <h5>SCHEME-33 CENTER</h5>
                <h5>STUDENT APPRAISAL REPORT</h5>
            </div>

            <table class="table table-bordered mt-3">
                <tr>
                    <td><strong>Enrollment No</strong></td><td><?= safe($studentDetails['enrollmentno']) ?></td>
                    <td><strong>Student Name</strong></td><td><?= safe($studentDetails['studentname']) ?></td>
                </tr>
                <tr>
                    <td><strong>Faculty</strong></td><td><?= safe($studentDetails['batchinstructor']) ?></td>
                    <td><strong>Batch Code</strong></td><td><?= safe($studentDetails['batchcode']) ?></td>
                </tr>
                <tr>
                    <td><strong>Current Semester</strong></td><td><?= safe($studentDetails['currentsem']) ?></td>
                    <td><strong>Quarter</strong></td><td>Q<?= safe($selectedQuarter) ?></td>
                </tr>
            </table>

            <?php foreach ($progressData as $month => $entries): ?>
                <?php 
                    $entry = $entries[0]; // Only 1 entry per month expected
                    $assignment = (int)$entry['assignmentmarks'];
                    $quiz = (int)$entry['quizmarksinternal'];
                    $modular = (int)$entry['modular'];
                    $practical = (int)$entry['practical'];
                    $totalObtained = $assignment + $quiz + $modular + $practical;
                    $totalMarks = 20 + 20 + 100 + 20;
                    $percentage = round(($totalObtained / $totalMarks) * 100, 2);
                ?>
                <h5 class="section-heading">Progress for the Month of <?= date('F', mktime(0, 0, 0, $month, 1)) ?></h5>

                <table class="table table-bordered text-center">
                    <thead>
                        <tr><th>Classes Held</th><th>Classes Attended</th><th>Late Comings</th><th>Absents</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= safe($entry['classes_conducted']) ?></td>
                            <td><?= safe($entry['classes_held']) ?></td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered text-center">
                    <thead>
                        <tr><th>Assignments (20)</th><th>Quizzes (20)</th><th>Modular (100)</th><th>Practical (20)</th><th>Percentage</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= safe($assignment) ?></td>
                            <td><?= safe($quiz) ?></td>
                            <td><?= safe($modular) ?></td>
                            <td><?= safe($practical) ?></td>
                            <td><?= $percentage ?>%</td>
                        </tr>
                    </tbody>
                </table>

                <p><strong>Remarks:</strong> <?= safe($entry['remarks']) ?></p>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between mt-4">
                <div><strong>Sent By:</strong><br>Counselor</div>
                <div><strong>Academic Head</strong><br><br>___________________</div>
                <div><strong>Center Manager</strong><br><br>___________________</div>
            </div>

            <div class="text-center mt-3 fw-bold">
                Contact No : 0334 0621597 &nbsp; | &nbsp; PTCL : 021 - 34693992-3
            </div>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">No data found for the selected quarter and enrollment number.</div>
    <?php endif; ?>
</div>

<?php include('footeradmin.php'); ob_end_flush(); ?>
