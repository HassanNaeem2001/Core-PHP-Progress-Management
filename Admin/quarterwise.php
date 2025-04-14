<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('headeradmin.php');
include('../connect.php');
ob_start();

$studentId = '';
$selectedQuarter = '';
$progressData = [];
$studentDetails = null;

$quarterMonths = [
    1 => [1, 2, 3],
    2 => [4, 5, 6],
    3 => [7, 8, 9],
    4 => [10, 11, 12],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['studentid'] ?? '';
    $selectedQuarter = (int)($_POST['quarter'] ?? 0);

    if (!empty($studentId) && isset($quarterMonths[$selectedQuarter])) {
        // Get student info
        $stmt = $conn->prepare("SELECT s.studentname, s.enrollmentno, s.studentbatch, b.batchcode 
                                FROM student s 
                                LEFT JOIN batches b ON s.studentbatch = b.batchcode 
                                WHERE s.studentid = ?");
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        $studentDetails = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Fetch progress data
        $months = $quarterMonths[$selectedQuarter];
        $placeholders = implode(',', array_fill(0, count($months), '?'));
        $sql = "SELECT YEAR(dateofprogress) AS year, MONTH(dateofprogress) AS month, assignmentmarks, quizmarksinternal, practical, modular, classes_conducted, classes_held, remarks
                FROM studentprogress 
                WHERE studentid = ? 
                AND MONTH(dateofprogress) IN ($placeholders)
                ORDER BY dateofprogress ASC";
        $stmt = $conn->prepare($sql);

        $types = 's' . str_repeat('i', count($months));
        $params = array_merge([$studentId], $months);
        $binds = [];
        $binds[] = $types;
        foreach ($params as $key => $val) {
            $binds[] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $binds);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $progressData[$row['month']][] = $row;
        }

        $stmt->close();
    }
}
?>

<div class="container mt-4">
    <h3>Quarterly Student Progress Report</h3>
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="studentid" class="form-label">Student ID</label>
            <input type="text" name="studentid" id="studentid" class="form-control" value="<?= htmlspecialchars($studentId) ?>" required>
        </div>
        <div class="col-md-4">
            <label for="quarter" class="form-label">Select Quarter</label>
            <select name="quarter" id="quarter" class="form-control" required>
                <option value="">Select Quarter</option>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>" <?= ($selectedQuarter == $i) ? 'selected' : '' ?>>Quarter <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Get Report</button>
        </div>
    </form>

    <?php if ($studentDetails && !empty($progressData)): ?>
        <div class="bg-light p-4 rounded shadow" id="report">
            <h4><?= htmlspecialchars($studentDetails['studentname']) ?> (<?= htmlspecialchars($studentDetails['enrollmentno']) ?>)</h4>
            <p>Batch: <?= htmlspecialchars($studentDetails['studentbatch']) ?></p>
            <hr>

            <?php foreach ($progressData as $month => $entries): ?>
                <h5><?= date("F", mktime(0, 0, 0, $month, 1)) ?></h5>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Assignment</th>
                            <th>Quiz</th>
                            <th>Practical</th>
                            <th>Modular</th>
                            <th>Classes Conducted</th>
                            <th>Classes Held</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['assignmentmarks']) ?></td>
                                <td><?= htmlspecialchars($entry['quizmarksinternal']) ?></td>
                                <td><?= htmlspecialchars($entry['practical']) ?></td>
                                <td><?= htmlspecialchars($entry['modular']) ?></td>
                                <td><?= htmlspecialchars($entry['classes_conducted']) ?></td>
                                <td><?= htmlspecialchars($entry['classes_held']) ?></td>
                                <td><?= htmlspecialchars($entry['remarks']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endforeach ?>

            <button class="btn btn-success mt-3" onclick="downloadReportImage()">Download as Image</button>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning mt-3">No records found for the selected student and quarter.</div>
    <?php endif ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    function downloadReportImage() {
        const report = document.getElementById('report');
        html2canvas(report).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL();
            link.download = 'progress_report.png';
            link.click();
        });
    }
</script>

<?php
include('footeradmin.php');
ob_end_flush();
?>
