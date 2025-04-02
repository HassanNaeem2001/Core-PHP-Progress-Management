<?php
include('../connect.php');

if (isset($_POST['btnupdatepassword'])) {
    $studentid = $_POST['studentid'];
    $newpassword = $_POST['newpassword'];
    $confirmpassword = $_POST['confirmpassword'];

    // Check if passwords match
    if ($newpassword !== $confirmpassword) {
        header("Location: student_profile.php?error=Passwords do not match");
        exit();
    }

    // Hash the new password
    $hashedPassword = md5($newpassword);

    // Update the password in the database
    $query = "UPDATE student SET studentpassword = '$hashedPassword' WHERE studentid = '$studentid'";
    if (mysqli_query($conn, $query)) {
        header("Location: studentdashboard.php?success=Password updated successfully");
    } else {
        header("Location: studentdashboard.php?error=Error updating password");
    }
}
?>
