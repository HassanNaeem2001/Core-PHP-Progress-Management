<?php
session_start();
include '../connect.php';

// Ensure student is logged in
if (!isset($_SESSION['studentid'])) {
    header('Location: ../index.php');
    exit();
}

$student_id = $_SESSION['studentid'];

// Fetch student details
$query = "SELECT s.studentname, b.batchcode, st.staffname AS faculty
          FROM studentprogresssystem.student s
          JOIN studentprogresssystem.batches b ON s.studentbatch = b.batchid
          JOIN studentprogresssystem.staff st ON b.batchinstructor = st.staffid
          WHERE s.studentid = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $_SESSION['studentname'] = $row['studentname'];
    $_SESSION['batch'] = $row['batchcode'];
    $_SESSION['faculty'] = $row['faculty'];
}

if (isset($_POST['submit_complaint'])) {
    $student_name = $_SESSION['studentname'];
    $batch = $_SESSION['batch'];
    $faculty = $_SESSION['faculty'];
    $complaint_type = $_POST['complaint_type'];
    $remarks = trim($_POST['remarks']);

    if (!empty($complaint_type) && !empty($remarks)) {
        $sql = "INSERT INTO student_complaints (student_name, batch, faculty, complaint_type, remarks) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $student_name, $batch, $faculty, $complaint_type, $remarks);

        if ($stmt->execute()) {
            echo "<script>alert('We have received your complaint , You will be contacted shortly'); window.location.href='studentdashboard.php';</script>";
        } else {
            echo "<script>alert('Error submitting complaint');</script>";
        }
    } else {
        echo "<script>alert('All fields are required');</script>";
    }
}

if (isset($_POST['btnlogout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Complaints</title>
    <style>
        label{
            font-weight:bold
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
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
                        <a class="nav-link active" href="studentdashboard.php">Home</a>
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

    <div class="bgstudent d-flex justify-content-center align-items-center text-center text-white">
        <div>
            <h3 class="headingfontstudent">Student Dashboard</h3>
            <p class="fw-bold"> <?php echo $_SESSION['studentname']; ?> - Feedback</p>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="headingfontstudent text-decoration-underline">Monthly Feedback </h2>
        <form method="post">
          <div class="row">
            <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Student Name</label>
                <input type="text" class="form-control" name="student_name" value="<?php echo $_SESSION['studentname']; ?>" readonly>
            </div>
           
            </div>
            <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Batch</label>
                <input type="text" class="form-control" name="batch" value="<?php echo $_SESSION['batch']; ?>" readonly>
            </div>
            </div>
            <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Faculty</label>
                <input type="text" class="form-control" name="faculty" value="<?php echo $_SESSION['faculty']; ?>" readonly>
            </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
            <div class="mt-2">
                <label class="form-label ms-3">Does the classes start and end on time ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            <hr>
            <div class="mt-2">
                <label class="form-label ms-3">Are you satisfied with the course coverage so far ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            <hr>

            <div class="mt-2">
                <label class="form-label ms-3">Are you satisfied with the technical staff ? </label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            
            </div>
            <hr>
            <div class="mt-2">
                <label class="form-label ms-3">Do you get the monthly academics report on time?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            </div>
            <div class="col-lg-6">
            <div class="mt-2">
                <label class="form-label ms-3">Is the faculty able to clear your doubts during lecture ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            <hr>
            <div class="mt-2">
                <label class="form-label ms-3">Are the exams and assignments taken on time ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            <hr>
            <div class="mt-2">
                <label class="form-label ms-3">Are the books being followed in classes ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>
            <hr>
            <div class="mt-2">
                <label class="form-label ms-3">Do you get the time for practice for the lecture delivered ?</label>
            </div>
            <div class="w-100 p-2">
            <select name="" class="w-100 p-1" id="">
                    <option value="">Everytime</option>
                    <option value="">Mostly</option>
                    <option value="">Rarely</option>
                    <option value="">Never</option>
                 </select>
            </div>    
        </div>

            
          </div>
          <br>
            <div class="mb-3">
                <label class="form-label ms-3">Remarks / Message</label> 
                <textarea class="form-control" style="border:1px solid black" name="remarks" rows="4" placeholder="Explain your feedback in detail..." required></textarea>
            </div>
            <br>

            <button type="submit" name="submit_complaint" class="btn btn-dark float-end w-50">Submit Complaint</button>
        </form>
    </div>
    <br><br>
    <?php include('footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>