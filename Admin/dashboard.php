<?php
include('../connect.php');
include('headeradmin.php');

// Fetch the count of Active, Dropout, and Course Complete students
$studentCountsQuery = "
    SELECT 
        SUM(studentstatus = 'Active') AS active,
        SUM(studentstatus = 'Dropout') AS dropout,
        SUM(studentstatus = 'Course Com') AS course_complete
    FROM student
";
$studentCountsResult = mysqli_query($conn, $studentCountsQuery);
$studentCounts = mysqli_fetch_assoc($studentCountsResult);

// Fetch the count of staff by staffdesignation
$staffCountsQuery = "
    SELECT 
        SUM(staffdesignation = 'Counselor') AS counselors,
        SUM(staffdesignation = 'Faculty') AS faculty,
        SUM(staffdesignation = 'Center Manager') AS center_manager,
        SUM(staffdesignation = 'Admin') AS admin
    FROM staff
";
$staffCountsResult = mysqli_query($conn, $staffCountsQuery);
$staffCounts = mysqli_fetch_assoc($staffCountsResult);
?>

<center>
<div class="container-fluid">
    <h4 class="m-4">Dashboard</h4>
    <div class="mb-3">
    <a href="downloadstudentcountreport.php?type=active" class="btn btn-dark">Download Active Students</a>
    <a href="downloadstudentcountreport.php?type=dropout" class="btn btn-danger">Download Dropout Students</a>
    <a href="downloadstudentcountreport.php?type=coursecom" class="btn btn-warning">Download Course Completed Students</a>
</div>
</center>

<div class="container">
    
    <!-- Student Counts -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Active Students</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $studentCounts['active']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Dropout Students</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $studentCounts['dropout']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Course Complete Students</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $studentCounts['course_complete']; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Counts -->
    <div class="row" >
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Counselors</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $staffCounts['counselors']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Faculty</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $staffCounts['faculty']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark mb-3">
                <div class="card-header">Center Manager</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $staffCounts['center_manager']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Admins</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $staffCounts['admin']; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php
include('footeradmin.php');
?>
