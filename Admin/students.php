<?php
include('../connect.php');
include('headeradmin.php');

// Handle Student Form Submission
if (isset($_POST['btnaddstudent'])) {
    $studentname = $_POST['studentname'];
    $enrollmentno = $_POST['enrollmentno'];
    $studentemail = $_POST['studentemail'];
    $studentpassword = md5($_POST['studentpassword']); // Encrypt password
    $studentbatch = $_POST['studentbatch']; // Stores batch ID
    $studentphoneno = $_POST['studentphoneno'];
    $studentguardianphoneno = $_POST['studentguardianphoneno'];
    $studentstatus = $_POST['studentstatus'];

    $query = "INSERT INTO student (studentname, enrollmentno, studentemail, studentpassword, studentbatch, studentphoneno, studentguardianphoneno, studentstatus) 
              VALUES ('$studentname', '$enrollmentno', '$studentemail', '$studentpassword', '$studentbatch', '$studentphoneno', '$studentguardianphoneno', '$studentstatus')";

    if (mysqli_query($conn, $query)) {
        $success = "Student Added Successfully!";
    } else {
        $error = "Error in Adding Student!";
    }
}

// Handle Student Status Update
if (isset($_POST['update_status'])) {
    $student_id = $_POST['student_id'];
    $new_status = $_POST['new_status'];
    $updateQuery = "UPDATE student SET studentstatus='$new_status' WHERE studentid='$student_id'";
    mysqli_query($conn, $updateQuery);
}

// Fetch students and join with batches for batch name
$query = "SELECT student.*, batches.batchcode FROM student
          INNER JOIN batches ON student.studentbatch = batches.batchid"; // Assuming batchid is the PK in batches
$result = mysqli_query($conn, $query);

// Fetch all batches for dropdown
$batchQuery = mysqli_query($conn, "SELECT * FROM batches");
?>

<div class="container mt-3">
    <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
    <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
</div>

<div class="container-fluid">
    <h4 class="m-4">Students - Add Student</h4>
    <hr>
    <div class="d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form action="" method="post">
                <input type="text" name="studentname" class="mt-2 form-control" placeholder="Enter Student Name" required>
                <input type="text" name="enrollmentno" class="mt-2 form-control" placeholder="Enter Enrollment No" required>
                <input type="text" name="studentemail" class="mt-2 form-control" placeholder="Enter Student Email or Student Id again" required>
                <input type="password" name="studentpassword" class="mt-2 form-control" placeholder="Enter Password" required>
                <select name="studentbatch" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Student Batch</option>
                    <?php while ($row = mysqli_fetch_array($batchQuery)) {
                        echo '<option value="'.$row['batchid'].'">'.$row['batchcode'].'</option>';
                    } ?>
                </select>
                <input type="text" name="studentphoneno" class="mt-2 form-control" placeholder="Enter Student Phone No" required>
                <input type="text" name="studentguardianphoneno" class="mt-2 form-control" placeholder="Enter Guardian Phone No" required>
                <select name="studentstatus" class="mt-2 w-100 p-1 form-control" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <button type="submit" class="btn btn-dark mt-2 w-50 float-end" name="btnaddstudent">Add Student</button>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid mt-5">
    <h4 class="m-4 pt-3">Students - View</h4>
    <hr>
    <div class="table table-responsive">
    <table id="studentsTable" class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Batch</th>
                <th>Status</th>
                <th>Update Status</th>
                <th>Actions</th>
                <th>Edit Details</th> <!-- Added Actions column -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_array($result)) { ?>
                <tr>
                    <td><?php echo $row['enrollmentno']; ?></td>
                    <td><?php echo $row['studentname']; ?></td>
                    <td><?php echo $row['batchcode']; ?></td>
                    <td><span class="badge bg-<?php echo ($row['studentstatus'] == 'Active') ? 'success' : 'danger'; ?>"> <?php echo $row['studentstatus']; ?> </span></td>
                    
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="student_id" value="<?php echo $row['studentid']; ?>">
                            <select name="new_status" class="form-select d-inline w-50">
                                <option value="Active">Active</option>
                                <option value="Course Complete">Course Complete</option>
                                <option value="Dropout">Dropout</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" name="update_status">Update</button>
                        </form>
                    </td>
                    <td>
                        <!-- Delete Button -->
                        <form method="post" action="">
                            <input type="hidden" name="student_id" value="<?php echo $row['studentid']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" name="delete_student">Delete</button>
                        </form>
                    </td>
                    <td>
    <form method="get" action="edit_student.php">
        <input type="hidden" name="student_id" value="<?php echo $row['studentid']; ?>">
        <button type="submit" class="btn btn-warning btn-sm">Edit</button>
    </form>
</td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>


<?php include('footeradmin.php'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
});
</script>
<?php
// Handle the delete request
if (isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];

    // Step 1: Delete related records from studentprogress table
    $deleteProgressQuery = "DELETE FROM studentprogress WHERE studentid = '$student_id'";
    if (mysqli_query($conn, $deleteProgressQuery)) {
        // Step 2: Delete the student record from the students table
        $deleteStudentQuery = "DELETE FROM student WHERE studentid = '$student_id'";

        if (mysqli_query($conn, $deleteStudentQuery)) {
            echo "<script>alert('Student deleted successfully'); window.location.href='students.php';</script>";
        } else {
            echo "<script>alert('Error deleting student');</script>";
        }
    } else {
        echo "<script>alert('Error deleting student progress records');</script>";
    }
}
?>
