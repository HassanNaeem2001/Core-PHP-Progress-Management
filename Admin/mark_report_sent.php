<?php
include('../connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentid = $_POST['studentid'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $type = $_POST['type']; // student or guardian

    $stmt = $conn->prepare("INSERT INTO sent_reports (studentid, month, year, report_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $studentid, $month, $year, $type);
    $stmt->execute();
    echo "OK";
}
