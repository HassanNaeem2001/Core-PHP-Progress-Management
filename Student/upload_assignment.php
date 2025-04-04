<?php
session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["assignmentfile"])) {
    $assignmentId = intval($_POST['assignmentid']); // Get assignment ID
    $studentId = intval($_SESSION['studentid']); // Get student ID

    // File Handling
    $uploadDir = "uploads/assignments/";
    
    // Ensure the upload directory exists, if not create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Get the uploaded file details
    $fileName = basename($_FILES["assignmentfile"]["name"]);
    $fileTempPath = $_FILES["assignmentfile"]["tmp_name"];
    $filePath = $uploadDir . time() . "_" . $fileName;

    // Check if file is uploaded without errors
    if ($_FILES["assignmentfile"]["error"] == UPLOAD_ERR_OK) {
        // Move uploaded file to the desired location
        if (move_uploaded_file($fileTempPath, $filePath)) {
            // Insert assignment submission record into the database
            $stmt = $conn->prepare("INSERT INTO assignments_uploaded (uploading_for, uploaded_by, uploaded_file, uploaded_on) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $assignmentId, $studentId, $filePath);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Assignment submitted successfully!";
            } else {
                $_SESSION['error'] = "Database error. Please try again!";
            }
        } else {
            $_SESSION['error'] = "Failed to move uploaded file!";
        }
    } else {
        // Handle file upload error
        $_SESSION['error'] = "File upload failed with error code: " . $_FILES["assignmentfile"]["error"];
    }
} else {
    $_SESSION['error'] = "No file uploaded or invalid request!";
}

// Redirect back to student dashboard
header("Location: studentdashboard.php");
exit();
?>
