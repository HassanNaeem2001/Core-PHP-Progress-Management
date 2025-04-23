<?php
include('../connect.php');
include('headeradmin.php');

// Fetch student status counts
$studentCountsQuery = "
    SELECT 
        SUM(studentstatus = 'Active') AS active,
        SUM(studentstatus = 'Dropout') AS dropout,
        SUM(studentstatus = 'Course Com') AS course_complete
    FROM student
";
$studentCountsResult = mysqli_query($conn, $studentCountsQuery);
$studentCounts = mysqli_fetch_assoc($studentCountsResult);

// Fetch total staff count
$staffCountQuery = "SELECT COUNT(*) AS total_staff FROM staff";
$staffCountResult = mysqli_query($conn, $staffCountQuery);
$staffCount = mysqli_fetch_assoc($staffCountResult);
?>

<style>
    .dashboard-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    .card-custom {
        border: 1px solid #ddd;
        border-radius: 12px;
        transition: box-shadow 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .card-custom:hover {
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .card-header {
        font-weight: 500;
        font-size: 16px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
    }

    .card-body h5 {
        font-size: 28px;
        color: #343a40;
        font-weight: 600;
    }

    .stats-container {
        margin-top: 40px;
    }

    .btn-report {
        margin: 0 8px 20px 0;
        border-radius: 8px;
    }

    .staff-box {
        border-radius: 12px;
        padding: 30px;
        background: #f1f3f5;
        text-align: center;
        border: 1px solid #ddd;
        transition: box-shadow 0.3s;
    }

    .staff-box:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .staff-box h2 {
        font-weight: 700;
        color: #212529;
        margin-bottom: 10px;
    }

    .staff-box p {
        color: #555;
    }
</style>

<div class="container-fluid px-4 py-4">
    <h3 class="dashboard-title text-center mb-4">Dashboard Overview</h3>

    <div class="text-center mb-3">
        <a href="downloadstudentcountreport.php?type=active" class="btn btn-outline-dark btn-report">Download Active</a>
        <a href="downloadstudentcountreport.php?type=dropout" class="btn btn-outline-danger btn-report">Download Dropout</a>
        <a href="downloadstudentcountreport.php?type=coursecom" class="btn btn-outline-primary btn-report">Download Course Completed</a>
    </div>

    <!-- Student Stats Cards -->
    <div class="row stats-container">
        <div class="col-md-4 mb-4">
            <div class="card card-custom" onclick="window.location.href='students.php'">
                <div class="card-header">Active Students</div>
                <div class="card-body text-center">
                    <h5><?php echo $studentCounts['active']; ?></h5>
                    <p class="mb-0 text-muted">Currently enrolled</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-custom" onclick="window.location.href='dropouts.php'">
                <div class="card-header">Dropout Students</div>
                <div class="card-body text-center">
                    <h5><?php echo $studentCounts['dropout']; ?></h5>
                    <p class="mb-0 text-muted">Students who discontinued</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card card-custom" onclick="window.location.href='coursecompleted.php'">
                <div class="card-header">Course Completed</div>
                <div class="card-body text-center">
                    <h5><?php echo $studentCounts['course_complete']; ?></h5>
                    <p class="mb-0 text-muted">Graduated students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Count -->
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="staff-box mt-3">
                <h2><?php echo $staffCount['total_staff']; ?> Staff Members</h2>
                <p>Total staff members in the organization</p>
            </div>
        </div>
    </div>
</div>

<?php include('footeradmin.php'); ?>
