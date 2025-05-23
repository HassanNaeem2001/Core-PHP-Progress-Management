<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">

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
$attendanceHeld = [];
$attendanceAttended = [];
$studentPhone = '';
$parentPhone = '';

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
        $stmt = $conn->prepare("SELECT s.studentid, s.studentname, s.enrollmentno, s.studentbatch, s.studentphoneno, b.batchcode, b.currentsem, st.staffname, s.studentguardianphoneno FROM student s LEFT JOIN batches b ON s.studentbatch = b.batchid LEFT JOIN staff st ON b.batchinstructor = st.staffid WHERE s.enrollmentno = ?");
        $stmt->bind_param("s", $enrollmentNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $studentDetails = $result->fetch_assoc();
        $studentPhone = $studentDetails['studentphoneno'] ?? '';
        $parentPhone = $studentDetails['studentguardianphoneno'] ?? '';

        $stmt->close();

        if ($studentDetails) {
            $studentId = $studentDetails['studentid'];
            $stmt = $conn->prepare("SELECT YEAR(dateofprogress) AS year, MONTH(dateofprogress) AS month, assignmentmarks, quizmarksinternal, modular, practical, classes_conducted, classes_held, remarks FROM studentprogress WHERE studentid = ? ORDER BY dateofprogress ASC");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $month = (int)$row['month'];
                if ($selectedQuarter && in_array($month, $quarterMonths[$selectedQuarter])) {
                    $progressData[$month][] = $row;
                    $attendanceHeld[$month] = $row['classes_held'] ?? '-';
                    $attendanceAttended[$month] = $row['classes_conducted'] ?? '-';
                }
            }
            $stmt->close();
        }
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">

<style>
    .table thead th {
        background-color: orange !important;
        color: black !important;
        font-family: 'Times New Roman', Times, serif;
    }
    h5.section-heading {
        background: orange;
        color: black;
        padding: 10px;
        margin-top: 30px;
        font-family: 'Oswald', sans-serif;
        text-align: center;
    }
    tr {
        border: 1px solid black !important;
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
        <?php
        $monthsInQuarter = $quarterMonths[(int)$selectedQuarter];
        $monthNames = array_map(fn($m) => date('M', mktime(0, 0, 0, $m, 10)), $monthsInQuarter);

        $assignments = $quizzes = $modulars = $practicals = $attendanceHeld = $attendanceConducted = [];

        foreach ($monthsInQuarter as $month) {
            $entry = $progressData[$month][0] ?? null;
            $assignments[$month] = $entry['assignmentmarks'] ?? '-';
            $quizzes[$month] = $entry['quizmarksinternal'] ?? '-';
            $modulars[$month] = $entry['modular'] ?? '-';
            $practicals[$month] = $entry['practical'] ?? '-';
            $attendanceHeld[$month] = isset($entry['classes_held']) ? $entry['classes_held'] . " Classes" : '- Classes';
            $attendanceConducted[$month] = $entry['classes_conducted'] ?? '-';
        }
        ?>

       <center>
       <div id="report" style="border: 2px solid #000; padding: 20px; background: #f9f9f9;width:800px">
            <div style="text-align: center;">
                <img src="../Images/aptlogo.png" alt="Aptech Logo" style="height: 150px;">
                <h4>APTECH LEARNING</h4>
                <h5>SCHEME 33 CENTER</h5>
                <h5 style="text-decoration: underline;">Student Appraisal Report <?= date('M', mktime(0, 0, 0, $monthsInQuarter[0])) ?> - <?= date('M Y', mktime(0, 0, 0, end($monthsInQuarter))) ?></h5>
            </div>

            <table class="table table-bordered mt-3">
                <tr>
                    <td><strong>Student ID:</strong></td><td><?= safe($studentDetails['enrollmentno']) ?></td>
                </tr>
                <tr>
                    <td><strong>Student Name:</strong></td><td><?= safe($studentDetails['studentname']) ?></td>
                </tr>
                <tr>
                    <td><strong>Faculty:</strong></td><td><?= safe($studentDetails['staffname']) ?></td>
                </tr>
                
                <tr>
                    <td><strong>Batch Code:</strong></td><td><?= safe($studentDetails['batchcode']) ?></td>
                </tr>
                <tr>
                    <td><strong>Current Semester:</strong></td><td><?= safe($studentDetails['currentsem']) ?></td>
                </tr>
            </table>

            <!-- Assignments -->
            <table class="table table-bordered text-center">
                <thead><tr><?php foreach ($monthsInQuarter as $month): ?><th>Assignment <?= date('M', mktime(0, 0, 0, $month)) ?> (100)</th><?php endforeach; ?></tr></thead>
                <tbody><tr><?php foreach ($assignments as $val): ?><td><?= safe($val) ?></td><?php endforeach; ?></tr></tbody>
            </table>

            <!-- Quizzes -->
            <table class="table table-bordered text-center">
                <thead><tr><?php foreach ($monthsInQuarter as $month): ?><th>Quiz <?= date('M', mktime(0, 0, 0, $month)) ?> (100)</th><?php endforeach; ?></tr></thead>
                <tbody><tr><?php foreach ($quizzes as $val): ?><td><?= safe($val) ?></td><?php endforeach; ?></tr></tbody>
            </table>

            <!-- Attendance -->
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                        <?php foreach ($monthsInQuarter as $month): ?>
                            <th>Classes Held - <?= date('M', mktime(0, 0, 0, $month)) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    
                    <tr>
                        <?php foreach ($attendanceHeld as $val): ?>
                            <td><?= safe($val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                   <tr style="border:none !important;">
                    <td></td>
                    <td></td>
                   </tr>
                  
                </thead>
                <tbody>
                <tr>
                        <?php foreach ($monthsInQuarter as $month): ?>
                            <th style="background-color:black;color:white; !important;font-family: 'Times New Roman'">Classes Attended - <?= date('M', mktime(0, 0, 0, $month)) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($attendanceAttended as $val): ?>
                            <td><?= safe($val) ?> - Classes</td>
                        <?php endforeach; ?>
                    </tr>
                    
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-4">
    <div style="text-align: center;">
        
        <img src="../Images/cmsignature.png" alt="Manager Signature" style="height: 80px;"><br>
       
        ________________________________
        <br>

        <strong>Center Manager</strong><br>
    </div>
    <div style="text-align: center;">
        
        <img src="../Images/cahsignature.png"  alt="Academic Head Signature" style="height: 80px;"><br>
        ________________________________
        <br>

        <strong>Center Academic Head</strong><br>
    </div>
</div>


            <div class="text-center mt-3 fw-bold" style="background: orange; padding: 10px;">
                Contact No: 021-34664922-3 &nbsp; 0336-2197164
            </div>
        </div>
       </center>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning">No data found for the selected quarter and enrollment number.</div>
    <?php endif; ?>
</div>


<div class="d-flex justify-content-center align-items-center">
    <button id="sendToStudent" class="btn btn-dark w-25 mt-4 m-3">Send Report to Student</button>
    <button id="sendToParent" class="btn btn-dark w-25 mt-4 m-3">Send Report to Parent</button>
    <button class="btn btn-dark mt-4 w-25 m-3" onclick="printReport()">Print Report</button>

</div>



<?php
function formatPhoneForWhatsApp($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);
    return (strpos($clean, '0') === 0) ? '92' . substr($clean, 1) : $clean;
}

$studentPhoneFormatted = formatPhoneForWhatsApp($studentPhone);
$parentPhoneFormatted = formatPhoneForWhatsApp($parentPhone);

?>
<script>
    const studentPhone = "<?= $studentPhoneFormatted ?>";
    const parentPhone = "<?= $parentPhoneFormatted ?>";
</script>


<?php include('footeradmin.php'); ob_end_flush(); ?>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>

function sendReportToWhatsApp(phoneNumber) {
    const report = document.getElementById('report');
    if (!report) {
        alert('No report to capture.');
        return;
    }

    html2canvas(report, { scale: 2 }).then(canvas => {
        canvas.toBlob(async function (blob) {
            try {
                await navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]);
                alert('Screenshot copied to clipboard! Now opening WhatsApp...');
                
                if (phoneNumber) {
                    const whatsappUrl = `https://wa.me/${phoneNumber}`;
                    window.open(whatsappUrl, '_blank');
                } else {
                    alert('Phone number is missing!');
                }

            } catch (err) {
                console.error(err);
                alert('Failed to copy screenshot. Use Chrome on HTTPS or localhost.');
            }
        });
    });
}

document.getElementById('sendToStudent').addEventListener('click', function () {
    sendReportToWhatsApp(studentPhone);
});

document.getElementById('sendToParent').addEventListener('click', function () {
    sendReportToWhatsApp(parentPhone);
});
function printReport() {
    const reportContent = document.getElementById('report').innerHTML;
    const win = window.open('', '', 'width=900,height=700');
    win.document.write(`
        <html>
            <head>
                <title>Student Appraisal Report</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Times New Roman', serif; padding: 20px; }
                    .table th, .table td { border: 1px solid black !important; }
                    .text-center { text-align: center; }
                    h4, h5 { margin: 0; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${reportContent}
            </body>
        </html>
    `);
    win.document.close();
}
</script>

