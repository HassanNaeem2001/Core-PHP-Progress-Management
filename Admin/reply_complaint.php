<?php
include('../connect.php'); // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = $_POST['complaint_id'];
    $adminremarks = trim($_POST['adminremarks']);

    if (!empty($complaint_id)) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE student_complaints SET adminremarks = ? WHERE id = ?");
        $stmt->bind_param("si", $adminremarks, $complaint_id);

        if ($stmt->execute()) {
            // Redirect with success message (optional: add flash or GET param)
            header("Location: view_complaints.php?status=success");
            exit();
        } else {
            echo "Error updating reply: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid complaint ID.";
    }
} else {
    echo "Invalid request.";
}
?>
