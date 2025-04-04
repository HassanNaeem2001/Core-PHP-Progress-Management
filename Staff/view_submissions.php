<?php
include('staffheader.php');
include('../connect.php');

// Get assignment ID from URL
if (!isset($_GET['assignmentid']) || empty($_GET['assignmentid'])) {
    die("Invalid Assignment ID");
}
$assignmentid = $_GET['assignmentid'];

// Fetch assignment details
$assignmentQuery = "SELECT * FROM assignments WHERE assignmentid = ?";
$stmt = $conn->prepare($assignmentQuery);
$stmt->bind_param("i", $assignmentid);
$stmt->execute();
$assignmentResult = $stmt->get_result();
$assignment = $assignmentResult->fetch_assoc();

// Fetch students of the batch assigned to this assignment
$studentsQuery = "SELECT studentid, studentname FROM student WHERE studentbatch = ?";
$stmt = $conn->prepare($studentsQuery);
$stmt->bind_param("i", $assignment['assignedto']);
$stmt->execute();
$studentsResult = $stmt->get_result();

// Fetch students who submitted this assignment
$submittedQuery = "SELECT uploaded_by, uploaded_file FROM assignments_uploaded WHERE uploading_for = ?";
$stmt = $conn->prepare($submittedQuery);
$stmt->bind_param("i", $assignmentid);
$stmt->execute();
$submittedResult = $stmt->get_result();

// Store submitted students in an array
$submittedStudents = [];
while ($row = $submittedResult->fetch_assoc()) {
    $submittedStudents[$row['uploaded_by']] = $row['uploaded_file'];
}
?>

<div class="container mt-4">
    <h4 class="mb-3">Assignment: <?= htmlspecialchars($assignment['assignmentname']) ?></h4>
    <p><strong>Description:</strong> <?= htmlspecialchars($assignment['assignmentdescription']) ?></p>
    <p><strong>Deadline:</strong> <?= date('jS F Y', strtotime($assignment['assignmentdeadline'])) ?></p>

    <h5 class="mt-4">Student Submissions</h5>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Submission Status</th>
                <th>Uploaded File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $studentsResult->fetch_assoc()) { 
                $studentid = $student['studentid'];
                $submittedFile = isset($submittedStudents[$studentid]) ? $submittedStudents[$studentid] : null;
            ?>
                <tr class="<?= $submittedFile ? 'text-success' : 'text-danger' ?>">
                    <td><?= htmlspecialchars($student['studentname']) ?></td>
                    <td><?= $submittedFile ? 'Submitted' : 'Missed' ?></td>
                    <td>
                        <?php if ($submittedFile) { ?>
                            <a href="../Student/<?= htmlspecialchars($submittedFile) ?>" target="_blank" class="btn btn-sm btn-primary">View File</a>
                            <?php } else { ?>
                            <span class="text-danger">Not Submitted</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('stafffooter.php'); ?>
