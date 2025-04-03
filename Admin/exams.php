<?php
include('../connect.php');
include('headeradmin.php');

// Handle form submission for scheduling an exam
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skillname = $_POST['skillname'];
    $examtype = $_POST['examtype'];
    $examdate = $_POST['examdate'];
    $examofbatch = $_POST['examofbatch'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO exams (skillname, examtype, examdate, examofbatch) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $skillname, $examtype, $examdate, $examofbatch);

    if ($stmt->execute()) {
        echo "<script>alert('Exam Scheduled Successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Handle exam deletion
if (isset($_GET['delete'])) {
    $examid = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM exams WHERE examsid = ?");
    $stmt->bind_param("i", $examid);

    if ($stmt->execute()) {
        echo "<script>alert('Exam Deleted Successfully!'); window.location.href='exams.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fetch batches for dropdown
$batchQuery = $conn->query("SELECT batchid, batchcode FROM batches");
?>

<div class="container-fluid">
    <h4 class="m-4">Exams - Schedule Exam</h4>

    <!-- Schedule Exam Form -->
    <div class="container">
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Skill Name:</label>
                <input type="text" name="skillname" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Exam Type:</label>
                <select name="examtype" class="form-control" required>
                    <option value="Modular">Modular</option>
                    <option value="Practical">Practical</option>
                    <option value="Prepratory">Prepratory</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Exam Date:</label>
                <input type="date" name="examdate" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Batch:</label>
                <select name="examofbatch" class="form-control" required>
                    <option value="">Select Batch</option>
                    <?php while ($batch = $batchQuery->fetch_assoc()) { ?>
                        <option value="<?= $batch['batchid'] ?>"><?= $batch['batchcode'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Schedule Exam</button>
        </form>
    </div>

    <hr>

    <!-- Display Scheduled Exams -->
    <h4 class="m-4">Scheduled Exams</h4>
   <div class="table table-responsive container">
   
   <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Exam ID</th>
                <th>Skill Name</th>
                <th>Exam Type</th>
                <th>Exam Date</th>
                <th>Batch Code</th>
                <th>Status</th>
                <th>Action</th> <!-- Added delete option -->
            </tr>
        </thead>
        <tbody>
            <?php
            $today = date("Y-m-d");
            $result = $conn->query("SELECT e.*, b.batchcode FROM exams e JOIN batches b ON e.examofbatch = b.batchid");

            while ($row = $result->fetch_assoc()) {
                $status = ($row['examdate'] > $today) ? "Pending" : "Conducted";
                echo "<tr>
                        <td>{$row['examsid']}</td>
                        <td>{$row['skillname']}</td>
                        <td>{$row['examtype']}</td>
                        <td>{$row['examdate']}</td>
                        <td>{$row['batchcode']}</td>
                        <td><span class='badge bg-" . ($status == "Pending" ? "warning" : "success") . "'>$status</span></td>
                        <td>
                            <a href='exams.php?delete={$row['examsid']}' 
                               class='btn btn-danger btn-sm'
                               onclick='return confirm(\"Are you sure you want to delete this exam?\")'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
   </div>
</div>

<?php include('footeradmin.php'); ?>
