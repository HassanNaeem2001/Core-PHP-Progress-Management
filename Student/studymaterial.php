<?php
session_start();
include '../connect.php';

// Ensure student is logged in
if (!isset($_SESSION['studentid'])) {
    header('Location: ../index.php');
    exit();
}

$student_id = $_SESSION['studentid'];

// Fetch student details including semester
$query = "SELECT s.studentname, b.batchcode, b.currentsem, st.staffname AS faculty
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
    $_SESSION['semester'] = $row['currentsem'];
}

$student_semester = $_SESSION['semester'];

// Fetch study materials based on semester
$book_query = "SELECT bookname, bookfile FROM books WHERE booksem = ?";
$book_stmt = $conn->prepare($book_query);
$book_stmt->bind_param("s", $student_semester);
$book_stmt->execute();
$books_result = $book_stmt->get_result();

// Logout functionality
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
    <title>Student Dashboard</title>
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
                    <li class="nav-item"><a class="nav-link active" href="studentdashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="studymaterial.php">Study Material</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenu" data-bs-toggle="dropdown">Academics</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="allmonthsprogress.php">Overall Progress</a></li>
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
            <p class="fw-bold"><?php echo $_SESSION['studentname']; ?> - Study Material</p>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="headingfontstudent text-decoration-underline text-center" style="font-size:30px">
            Showing results based on your profile
        </h2>

        <h2 class="text-center">Study Materials for <?php echo htmlspecialchars($_SESSION['semester']); ?></h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $books_result->fetch_assoc()) { 
                    $file_name = htmlspecialchars($book['bookfile']);
                    $file_path = "../uploads/" . $file_name;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['bookname']); ?></td>
                        <td>
                            <?php if (file_exists($file_path)) { ?>
                                <a href="<?php echo $file_path; ?>" class="btn btn-primary" download>Download</a>
                            <?php } else { ?>
                                <span class="text-danger">File not found</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($books_result->num_rows === 0) { ?>
                    <tr><td colspan="2" class="text-center">No books available for this semester.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <br><br>
    <?php include('footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
