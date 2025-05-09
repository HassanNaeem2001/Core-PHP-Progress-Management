<?php
session_start();
include '../connect.php';

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
    $studentid = $_SESSION['studentid'];
    $batch = $_SESSION['batch'];
    $faculty = $_SESSION['faculty'];
    $complaint_type = $_POST['complaint_type'];
    $remarks = trim($_POST['remarks']);

    if (!empty($complaint_type) && !empty($remarks)) {
        $sql = "INSERT INTO student_complaints (student_name, batch, faculty, complaint_type, remarks, studentid) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $student_name, $batch, $faculty, $complaint_type, $remarks, $studentid);

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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
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
                <li class="nav-item"><a class="nav-link" href="studymaterial.php">Study Material</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" data-bs-toggle="dropdown">Academics</a>
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
        <p class="fw-bold"> <?php echo $_SESSION['studentname']; ?> - Complaints</p>
    </div>
</div>

<div class="container mt-5">
    <h2 class="headingfontstudent text-decoration-underline">Submit a Complaint</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Student Name</label>
            <input type="text" class="form-control" name="student_name" value="<?php echo $_SESSION['studentname']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Batch</label>
            <input type="text" class="form-control" name="batch" value="<?php echo $_SESSION['batch']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Faculty</label>
            <input type="text" class="form-control" name="faculty" value="<?php echo $_SESSION['faculty']; ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Complaint Type</label>
            <select class="form-control" name="complaint_type" required>
                <option value="">Select Type</option>
                <option value="Academic">Academic</option>
                <option value="Administration">Administration</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Remarks / Message</label>
            <textarea class="form-control" name="remarks" rows="4" placeholder="Explain your complaint in detail..." required></textarea>
        </div>
        <button type="submit" name="submit_complaint" class="btn btn-dark float-end w-50">Submit Complaint</button>
    </form>
</div>

<div class="container mt-5">
    <h2 class="headingfontstudent text-decoration-underline">My Complaints</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Complaint ID</th>
                <th>Type</th>
                <th>Remarks</th>
                <th>Reply</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM student_complaints WHERE studentid = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
        <td>" . htmlspecialchars($row['id']) . "</td>
        <td>" . htmlspecialchars($row['complaint_type']) . "</td>
        <td>" . htmlspecialchars($row['remarks']) . "</td>
        <td>";
            if (!empty($row['adminremarks'])) {
                echo htmlspecialchars($row['adminremarks']);
            } else {
                echo "<span class='text-danger'>No Reply Yet</span>";
            }
echo "</td>
    </tr>";
        }
            ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
