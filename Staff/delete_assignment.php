<?php
include('../connect.php');

if (isset($_GET['id'])) {
    $assignmentid = $_GET['id'];

    // Fetch the assignment to get the file path
    $query = "SELECT assignmentfile FROM assignments WHERE assignmentid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignmentid);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();

    // Delete the file if it exists
    if ($assignment['assignmentfile'] && file_exists($assignment['assignmentfile'])) {
        unlink($assignment['assignmentfile']);
    }

    // Delete the assignment record from the database
    $deleteQuery = "DELETE FROM assignments WHERE assignmentid = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $assignmentid);

    if ($stmt->execute()) {
        echo "<script>alert('Assignment deleted successfully!'); window.location.href='uploadassignment.php';</script>";
    } else {
        echo "<script>alert('Error deleting assignment.'); window.location.href='uploadassignment.php';</script>";
    }
}
?>
