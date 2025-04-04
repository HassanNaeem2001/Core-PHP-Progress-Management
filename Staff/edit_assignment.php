<?php
include('staffheader.php');
include('../connect.php');

// Fetch assignment details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid Assignment ID');
}
$id = $_GET['id'];

$query = "SELECT * FROM assignments WHERE assignmentid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment) {
    die("Assignment not found");
}

// Fetch batch list
$batchQuery = "SELECT batchid, batchcode FROM batches";
$batchResult = mysqli_query($conn, $batchQuery);

// Handle update
if (isset($_POST['update'])) {
    $assignmentname = $_POST['assignmentname'];
    $assignmentdescription = $_POST['assignmentdescription'];
    $assignmentdeadline = $_POST['assignmentdeadline'];
    $assignedto = $_POST['assignedto'];
    $marks = $_POST['marks'];

    $file_destination = $assignment['assignmentfile'];

    // If new file is uploaded
    if (!empty($_FILES['assignmentfile']['name'])) {
        $file_name = $_FILES['assignmentfile']['name'];
        $file_tmp_name = $_FILES['assignmentfile']['tmp_name'];
        $file_error = $_FILES['assignmentfile']['error'];

        if ($file_error === 0) {
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid('', true) . '.' . $file_extension;
            $file_destination = '../Student/uploads/' . $new_file_name;
            move_uploaded_file($file_tmp_name, $file_destination);
        }
    }

    $updateQuery = "UPDATE assignments 
                    SET assignmentname = ?, assignmentdescription = ?, assignmentfile = ?, 
                        assignmentdeadline = ?, assignedto = ?, marks = ?
                    WHERE assignmentid = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssssi", $assignmentname, $assignmentdescription, $file_destination, $assignmentdeadline, $assignedto, $marks, $id);

    if ($updateStmt->execute()) {
        echo "<div class='alert alert-success'>Assignment updated successfully!</div>";
        // Refresh assignment data
        $stmt->execute();
        $assignment = $stmt->get_result()->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Update failed: " . $updateStmt->error . "</div>";
    }
}
?>

<div class="container mt-4">
    <h4 class="mb-3">Edit Assignment</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Assignment Name</label>
            <input type="text" class="form-control mt-2" name="assignmentname" value="<?= htmlspecialchars($assignment['assignmentname']) ?>" required>
        </div>

        <div class="form-group mt-3">
            <label>Description</label>
            <textarea class="form-control mt-2" name="assignmentdescription" rows="4" required><?= htmlspecialchars($assignment['assignmentdescription']) ?></textarea>
        </div>

        <div class="form-group mt-3">
            <label>Current File</label><br>
            <?php if ($assignment['assignmentfile']) { ?>
                <a href="<?= $assignment['assignmentfile'] ?>" target="_blank">View File</a>
            <?php } else { ?>
                <span class="text-muted">No Attachment</span>
            <?php } ?>
        </div>

        <div class="form-group mt-3">
            <label>Change File (Optional)</label>
            <input type="file" class="form-control-file mt-2" name="assignmentfile">
        </div>

        <div class="form-group mt-3">
            <label>Deadline</label>
            <input type="date" class="form-control mt-2" name="assignmentdeadline" value="<?= $assignment['assignmentdeadline'] ?>" required>
        </div>

        <div class="form-group mt-3">
            <label>Batch</label>
            <select class="w-100 p-1 mt-2" name="assignedto" required>
                <?php while ($row = mysqli_fetch_assoc($batchResult)) {
                    $selected = $row['batchid'] == $assignment['assignedto'] ? "selected" : "";
                    echo "<option value='" . $row['batchid'] . "' $selected>" . $row['batchcode'] . "</option>";
                } ?>
            </select>
        </div>

        <div class="form-group mt-3">
            <label>Marks</label>
            <input type="number" class="form-control mt-2" name="marks" value="<?= $assignment['marks'] ?? '' ?>" placeholder="Enter marks (optional)">
        </div>

        <button type="submit" class="btn btn-dark mt-4 mb-4 w-100" name="update">Update Assignment</button>
    </form>
</div>

<?php include('stafffooter.php'); ?>
