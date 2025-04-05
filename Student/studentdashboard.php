<?php
include 'sessionstudent.php';
include('../connect.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="studentdashboard.php">
                <img src="../Images/aptlogo.png" height="50px" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="studymaterial.php">Study Material</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" data-bs-toggle="dropdown">
                            Academics
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="allmonthsprogress.php">Over All Progress</a></li>
                            <li><a class="dropdown-item" href="studentdashboard.php">Jobs</a></li>
                            <li><a class="dropdown-item" href="#">Attendance</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="studentcomplaints.php">Complaints</a></li>
                </ul>
                <form method="post" class="d-flex">
                    <button type="submit" name="btnlogout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Student Dashboard Header -->
    <div class="bgstudent d-flex justify-content-center align-items-center text-center text-white">
        <div>
            <h3 class="headingfontstudent">Student Dashboard</h3>
            <p class="fw-bold"><?php echo $_SESSION['studentname']; ?></p>
        </div>
    </div>

    <!-- Student Information Table -->
    <div class="container-fluid mt-4 text-light d-flex justify-content-center">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                <?php if (isset($_GET['success'])) { ?>
    <div class="alert alert-success"><?php echo $_GET['success']; ?></div>
<?php } ?>

<?php if (isset($_GET['error'])) { ?>
    <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
<?php } ?>
                    <h4 class="text-dark headingfontstudent" style="font-size:25px">Your Details</h4>
                    <div class="table-responsive">
                            <table class="table table-dark table-striped table-bordered table-hover shadow-lg">
                                <thead class="text-center">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Enrollment No</th>
                                        <th>Batch</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Faculty</th>
                                        <th>Current Semester</th>
                                        <th>Update Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $query = mysqli_query($conn, "
                                        SELECT 
                                            batches.batchcode, 
                                            batches.currentsem, 
                                            staff.staffname 
                                        FROM batches 
                                        LEFT JOIN staff ON batches.batchinstructor = staff.staffid
                                        WHERE batches.batchid = {$_SESSION['studentbatch']}
                                    ");
                                    $data = mysqli_fetch_assoc($query);
                                    ?>
                                    <tr class="text-center">
                                        <td><?php echo $_SESSION['studentname']; ?></td>
                                        <td><?php echo $_SESSION['enrollmentno']; ?></td>
                                        <td><?php echo $data['batchcode']; ?></td>
                                        <td><?php echo $_SESSION['studentemail']; ?></td>
                                        <td><?php echo $_SESSION['studentphoneno']; ?></td>
                                        <td><?php echo $data['staffname']; ?></td>
                                        <td><?php echo $data['currentsem']; ?></td>
                                        <td>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">Update Password</button>
</td>

                                    </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Progress Table -->
    <div class="container-fluid mt-4 text-light d-flex justify-content-center">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h4 class="text-dark headingfontstudent" style="font-size:25px">Your Progress - LAST MONTH</h4>
                    
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-hover shadow-lg">
                            <thead class="text-center">
                                <tr>
                                    <th>Assignment Marks</th>
                                    <th>Quiz Marks</th>
                                    <th>Practical</th>
                                    <th>Modular</th>
                                    <th>Classes Attended</th>
                                    <th>Classes Held</th>
                                    <th>Percentage</th>
                                    <th>Remarks</th>
                                    <th>Month of Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
$studentid = $_SESSION['studentid'];
$query = mysqli_query($conn, "
    SELECT * FROM studentprogress 
    WHERE studentid = '$studentid'
    ORDER BY dateofprogress DESC 
    LIMIT 1
");

if (mysqli_num_rows($query) > 0) {
    $progress = mysqli_fetch_assoc($query);
    
    // Calculate Attendance Percentage
    $attendancePercentage = ($progress['classes_held'] > 0) 
        ? round(($progress['classes_conducted'] / $progress['classes_held']) * 100, 2) 
        : 0;

    // Assignment Marks (out of 100)
    $assignmentMarks = $progress['assignmentmarks'];  

    // Quiz Marks (out of 100)
    $quizMarks = $progress['quizmarksinternal'];  

    // Calculate Overall Weighted Percentage
    $overallPercentage = round(
        ($assignmentMarks * 0.40) + 
        ($attendancePercentage * 0.30) + 
        ($quizMarks * 0.30), 
        2
    );
?>
    <tr class="text-center">
        <td><?php echo $progress['assignmentmarks']; ?></td>
        <td><?php echo $progress['quizmarksinternal']; ?></td>
        <td><?php echo $progress['practical']; ?></td>
        <td><?php echo $progress['modular']; ?></td>
        <td><?php echo $progress['classes_conducted']; ?></td>
        <td><?php echo $progress['classes_held']; ?></td>
        <td><?php echo $overallPercentage . '%'; ?></td>
        <td><?php echo $progress['remarks']; ?></td>
        <td><?php echo date('F', strtotime($progress['dateofprogress'])); ?></td>
    </tr>
<?php
} else {
    echo "<tr><td colspan='9' class='text-center'>No progress data available</td></tr>";
}
?>

                            </tbody>
                        </table>
                        <center>
                        <a href="allmonthsprogress.php" class="text-center">View Every Month's Progress</a>

                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Jobs Update Table -->
     <div class="container-fluid mt-4 text-light d-flex justify-content-center">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h4 class="text-dark headingfontstudent" style="font-size:25px">Jobs Update</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-hover shadow-lg">
                            <thead class="text-center">
                                <tr>
                                    <th>Job Title</th>
                                    <th>Job Description</th>
                                    <th>Apply Before</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $query = mysqli_query($conn, "
                                    SELECT * from jobs
                                        
                                ");
                                while($data = mysqli_fetch_array($query))
                                {
                                ?>
                                <tr class="text-center">
                                    <td><?php echo $data['jobtitle']?></td>
                                    <td><?php echo $data['jobdescription']?></td>
                                    <td><?php echo date('d M Y', strtotime($data['applybefore'])); ?></td>
                                    <td>
                                        <form action="" method="post">
                                            <button type="submit" name="btnpplyforjob" class="btn btn-primary">Apply Now</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Assignments Table -->
<div class="container-fluid mt-4 text-light d-flex justify-content-center">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h4 class="text-dark headingfontstudent" style="font-size:25px">Pending Assignments</h4>
                <div class="table-responsive">
                <?php
                $studentId = $_SESSION['studentid'];
                $studentBatch = $_SESSION['studentbatch']; // Student batch from session
                $today = date('Y-m-d'); // Current date
                
                // Modified query to fetch assignments specific to the student's batch
                $assignmentQuery = $conn->prepare("
                    SELECT
                        a.assignmentid,
                        a.assignmentname,
                        a.assignmentdescription,
                        a.assignmentdeadline,
                        a.marks,
                        a.assignmentfile
                    FROM assignments a
                    INNER JOIN batches b ON a.assignedto = b.batchid
                    WHERE b.batchid = ?
                    AND a.assignmentid NOT IN (
                        SELECT uploading_for
                        FROM assignments_uploaded
                        WHERE uploadedby = ?
                    )
                    ORDER BY a.assignmentdeadline ASC
                ");
                
                $assignmentQuery->bind_param("ii", $studentBatch, $studentId);
                $assignmentQuery->execute();
                $assignmentResult = $assignmentQuery->get_result();
                ?>

<table class="table table-dark table-striped table-bordered table-hover shadow-lg">
            <thead class="text-center">
                <tr>
                    <th>Assignment Name</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Marks</th>
                    <th>Faculty File</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($assignment = $assignmentResult->fetch_assoc()): ?>
                <?php
                // Check if the student has submitted this assignment
                $submissionQuery = $conn->prepare("
                    SELECT uploaded_file
                    FROM assignments_uploaded
                    WHERE uploading_for = ? AND uploaded_by = ?
                ");
                $submissionQuery->bind_param("ii", $assignment['assignmentid'], $studentId);
                $submissionQuery->execute();
                $submissionResult = $submissionQuery->get_result();
                $isSubmitted = $submissionResult->num_rows > 0;
                $submissionData = $submissionResult->fetch_assoc();
                $submittedFilePath = $submissionData['uploaded_file'] ?? '';

                // Check if the deadline has passed
                $isMissed = strtotime($assignment['assignmentdeadline']) < time() && !$isSubmitted;
                ?>

                <tr class="text-center">
                    <td><?= htmlspecialchars($assignment['assignmentname']) ?></td>
                    <td><?= htmlspecialchars($assignment['assignmentdescription']) ?></td>
                    <td><?= date('jS F Y', strtotime($assignment['assignmentdeadline'])) ?></td>
                    <td>
                        <?= is_numeric($assignment['marks']) ? htmlspecialchars($assignment['marks']) : '<span class="text-muted">N/A</span>' ?>
                    </td>

                    <td>
                        <?php if (!$isSubmitted && !$isMissed && $assignment['assignmentfile']): ?>
                            <a href="<?= htmlspecialchars($assignment['assignmentfile']) ?>" target="_blank" class="btn btn-info btn-sm">View Faculty File</a>
                        <?php elseif ($isSubmitted): ?>
                            <span class="text-white">Expired</span>
                        <?php else: ?>
                            <span class="text-white">No Faculty File</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($isSubmitted): ?>
                            <span class="text-warning">Submitted</span><br>
                            <?php if (!empty($submittedFilePath)): ?>
                                <a href="<?= htmlspecialchars($submittedFilePath) ?>" target="_blank" class="btn btn-info btn-sm mt-1">View Your File</a>
                            <?php else: ?>
                                <span class="text-muted">No Attachments</span>
                            <?php endif; ?>
                        <?php elseif ($isMissed): ?>
                            <span class="text-danger">Missed Assignment</span>
                        <?php else: ?>
                            <form action="upload_assignment.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="assignmentid" value="<?= $assignment['assignmentid'] ?>">
                                <div class="form-group mb-2">
                                    <input type="file" name="assignmentfile" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm">Submit Assignment</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($assignmentResult->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center">No assignments found for your batch.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
                </div>
            </div>
        </div>
    </div>
</div>


      <!-- Exams Update Table -->
      <div class="container-fluid mt-4 text-light d-flex justify-content-center">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h4 class="text-dark headingfontstudent" style="font-size:25px">UpComing Exams</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-hover shadow-lg">
                            <thead class="text-center">
                                <tr>
                                    <th>Skill Name</th>
                                    <th>Exam Type</th>
                                    <th>Exam Date</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                              // Fetch pending exams for the student's batch
$studentBatch = $_SESSION['studentbatch'];
$examQuery = $conn->prepare("SELECT skillname, examtype, examdate FROM exams 
WHERE examofbatch = ? AND examdate > CURDATE()");
$examQuery->bind_param("i", $studentBatch);
$examQuery->execute();
$examResult = $examQuery->get_result();
                              ?>
                                <?php while ($exam = $examResult->fetch_assoc()) { ?>
                                <tr class="text-center">
                                    <td><?= $exam['skillname'] ?></td>
                                    <td><?= $exam['examtype'] ?></td>
                                    <td><?= date('jS F Y', strtotime($exam['examdate'])) ?></td>

                                </tr>
                            <?php } ?>
                            <?php if ($examResult->num_rows == 0) { ?>
                                <tr><td colspan="3" class="text-center">No upcoming exams</td></tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Update Modal -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="update_password.php" method="post">
                    <input type="hidden" name="studentid" value="<?php echo $_SESSION['studentid']; ?>">
                    
                    <div class="mb-3">
                        <label for="newpassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newpassword" name="newpassword" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirmpassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" name="btnupdatepassword">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if(isset($_POST['btnlogout']))
{
    session_destroy();
    header('Location:../index.php');
    exit(); // It's a good practice to call exit after a redirect
}
?>