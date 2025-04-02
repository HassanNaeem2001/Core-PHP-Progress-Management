<?php
ob_start(); // Output buffering to prevent header issues
include('../connect.php');
include('sessionstudent.php');

if (!isset($_SESSION['studentid'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$query = mysqli_query($conn, "
    SELECT 
        assignmentmarks, 
        quizmarksinternal, 
        practical, 
        modular, 
        classes_conducted, 
        classes_held, 
        remarks, 
        dateofprogress 
    FROM studentprogress 
    WHERE studentid = {$_SESSION['studentid']}
    ORDER BY dateofprogress DESC
");
?>
<!doctype html>
<html lang="en">
    <head>
        <title>All Month Progress - Student</title>
        <link rel="stylesheet" href="../style.css">
        <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
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
                    <li class="nav-item"><a class="nav-link active" href="studentdashboard.php">Home</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="studymaterial.php">Study Material</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" data-bs-toggle="dropdown">Academics</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Over All Progress</a></li>
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
            <p class="fw-bold"><?php echo $_SESSION['studentname']; ?> - Over All Progress</p>
        </div>
    </div>

 <div class="container mt-4">
 <div class="table-responsive">
   <table class="table table-dark table-striped table-bordered table-hover shadow-lg">
        <thead class="text-center">
            <tr>
                <th>Month</th>
                <th>Assignment Marks (40%)</th>
                <th>Quiz Marks (30%)</th>
                <th>Practical & Modular (30%)</th>
                <th>Attendance (%)</th>
                <th>Final Percentage</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($progress = mysqli_fetch_assoc($query)) : ?>
                <?php
               $attendance_percentage = ($progress['classes_held'] > 0) 
               ? round(($progress['classes_conducted'] / $progress['classes_held']) * 100, 2) 
               : 0;
           
           $final_percentage = round(
               ($progress['assignmentmarks'] * 0.4) +  // 40% weight for assignments
               ($progress['quizmarksinternal'] * 0.3) +  // 30% weight for quizzes
               ($attendance_percentage * 0.3), // 30% weight for attendance
               2
           );
           
                ?>
                <tr class="text-center">
                    <td><?php echo date('F Y', strtotime($progress['dateofprogress'])); ?></td>
                    <td><?php echo $progress['assignmentmarks']; ?></td>
                    <td><?php echo $progress['quizmarksinternal']; ?></td>
                    <td><?php echo ($progress['practical'] + $progress['modular']) / 2; ?></td>
                    <td><?php echo $attendance_percentage . "%"; ?></td>
                    <td><?php echo $final_percentage . "%"; ?></td>
                    <td><?php echo $progress['remarks']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
   </div>
 </div>
 <?php
    include('footer.php');
    ?>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
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