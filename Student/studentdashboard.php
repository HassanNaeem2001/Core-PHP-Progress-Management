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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" data-bs-toggle="dropdown">
                            Academics
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="allmonthsprogress.php">Over All Progress</a></li>
                            <li><a class="dropdown-item" href="#">Jobs</a></li>
                            <li><a class="dropdown-item" href="#">Attendance</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Complaints</a></li>
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
                                    <th>Student Name</th>
                                    <th>Enrollment No</th>
                                    <th>Batch</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Faculty</th>
                                    <th>Current Semester</th>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include('footer.php');
    ?>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
if (isset($_POST['btnlogout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>
