<?php
include('../connect.php');

// Get staffid from URL
if (isset($_GET['staffid'])) {
    $staffid = $_GET['staffid'];

    // Delete query
    $deleteQuery = "DELETE FROM staff WHERE staffid = '$staffid'";

    if (mysqli_query($conn, $deleteQuery)) {
        header("Location: staff.php?message=Staff deleted successfully!");
    } else {
        header("Location: staff.php?message=Error in deleting staff!");
    }
} else {
    header("Location: staff.php");
}
?>
