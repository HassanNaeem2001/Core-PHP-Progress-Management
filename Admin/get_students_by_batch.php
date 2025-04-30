<?php
include('../connect.php');

if (isset($_POST['batchid'])) {
    $batchid = $_POST['batchid'];

    $stmt = $conn->prepare("SELECT studentid, studentname FROM studentprogresssystem.student WHERE studentbatch = ?");
    $stmt->bind_param("i", $batchid);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>Select Student</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['studentid'] . "'>" . $row['studentname'] . "</option>";
    }
}
?>
