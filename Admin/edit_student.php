<?php
include('../connect.php');
include('headeradmin.php');

// Check if student_id is passed
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student details from the database
    $query = "SELECT student.*, batches.batchcode FROM student
              INNER JOIN batches ON student.studentbatch = batches.batchid
              WHERE student.studentid = '$student_id'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);
}

if (isset($_POST['update_student'])) {
    $studentname = $_POST['studentname'];
    $enrollmentno = $_POST['enrollmentno'];
    $studentemail = $_POST['studentemail'];
    $studentpassword = isset($_POST['studentpassword']) ? md5($_POST['studentpassword']) : $student['studentpassword'];
    $studentbatch = $_POST['studentbatch'];
    $studentphoneno = $_POST['studentphoneno'];
    $studentguardianphoneno = $_POST['studentguardianphoneno'];
    $studentstatus = $_POST['studentstatus'];

    // Update the student information in the database
    $updateQuery = "UPDATE student SET studentname='$studentname', enrollmentno='$enrollmentno', studentemail='$studentemail', 
                    studentpassword='$studentpassword', studentbatch='$studentbatch', studentphoneno='$studentphoneno', 
                    studentguardianphoneno='$studentguardianphoneno', studentstatus='$studentstatus' WHERE studentid='$student_id'";

    if (mysqli_query($conn, $updateQuery)) {
        $success = "Student Updated Successfully!";
        header("Location: students.php"); // Redirect back to the students list
    } else {
        $error = "Error updating student!";
    }
}

$batchQuery = mysqli_query($conn, "SELECT * FROM batches");
?>

<div class="container mt-3">
    <h4>Edit Student Details</h4>
    <hr>
    <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
    <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <form method="POST">
        <input type="text" name="studentname" class="mt-2 form-control" placeholder="Enter Student Name" value="<?= $student['studentname'] ?>" required>
        <input type="text" name="enrollmentno" class="mt-2 form-control" placeholder="Enter Enrollment No" value="<?= $student['enrollmentno'] ?>" required>
        <input type="text" name="studentemail" class="mt-2 form-control" placeholder="Enter Student Email" value="<?= $student['studentemail'] ?>" required>
        <input type="password" name="studentpassword" class="mt-2 form-control" placeholder="Enter New Password (Leave blank to keep current)">
        <select name="studentbatch" class="mt-2 form-control" required>
            <option value="" disabled>Select Student Batch</option>
            <?php while ($row = mysqli_fetch_array($batchQuery)) { ?>
                <option value="<?= $row['batchid'] ?>" <?= ($row['batchid'] == $student['studentbatch']) ? 'selected' : '' ?>>
                    <?= $row['batchcode'] ?>
                </option>
            <?php } ?>
        </select>
        <input type="text" name="studentphoneno" class="mt-2 form-control" placeholder="Enter Student Phone No" value="<?= $student['studentphoneno'] ?>" required>
        <input type="text" name="studentguardianphoneno" class="mt-2 form-control" placeholder="Enter Guardian Phone No" value="<?= $student['studentguardianphoneno'] ?>" required>
        <select name="studentstatus" class="mt-2 form-control" required>
            <option value="Active" <?= ($student['studentstatus'] == 'Active') ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= ($student['studentstatus'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>
        <button type="submit" class="btn btn-dark float-end w-50 mt-2 w-50" name="update_student">Update Student</button>
    </form>
</div>

<?php include('footeradmin.php'); ?>
