<?php
include('staffheader.php');
include('../connect.php');

// Get the current staff ID from the session
$staffid = $_SESSION['staffsession'];

if (isset($_POST['submit'])) {
    // Get form data
    $assignmentname = $_POST['assignmentname'];
    $assignmentdescription = $_POST['assignmentdescription'];
    $assignmentdeadline = $_POST['assignmentdeadline'];
    $assignedto = $_POST['assignedto'];
    $marks = $_POST['marks'];

    $file_destination = NULL; // Optional file upload

    // Handle file upload if provided
    if (!empty($_FILES['assignmentfile']['name'])) {
        $file_name = $_FILES['assignmentfile']['name'];
        $file_tmp_name = $_FILES['assignmentfile']['tmp_name'];
        $file_error = $_FILES['assignmentfile']['error'];

        if ($file_error === 0) {
            // Generate a unique file name to prevent name collisions
            $new_file_name = uniqid('', true) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
            $file_destination = '../Student/uploads/' . $new_file_name;

            // Move the uploaded file to the destination folder
            move_uploaded_file($file_tmp_name, $file_destination);
        }
    }

    // Insert into database
    $query = "INSERT INTO assignments (assignmentname, assignmentdescription, assignmentfile, assignmentdeadline, assignedto, marks, uploadedby) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssiii", $assignmentname, $assignmentdescription, $file_destination, $assignmentdeadline, $assignedto, $marks, $staffid);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Assignment uploaded successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}

// Fetch batch names
$batchQuery = "SELECT batchid, batchcode FROM batches";
$batchResult = mysqli_query($conn, $batchQuery);

// Fetch assignments uploaded by the current staff
$assignmentQuery = "SELECT a.*, b.batchcode 
                    FROM assignments a
                    JOIN batches b ON a.assignedto = b.batchid
                    WHERE a.uploadedby = ?";
$stmt = $conn->prepare($assignmentQuery);
$stmt->bind_param("i", $staffid); // Bind the staff ID to the query
$stmt->execute();
$assignmentResult = $stmt->get_result();
?>

<!-- Upload Form -->
<div class="container mt-4">
    <h4 class="mb-3">Upload Assignment</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="assignmentname">Assignment Name</label>
            <input type="text" class="form-control mt-2" id="assignmentname" name="assignmentname" required>
        </div>

        <div class="form-group">
            <label for="assignmentdescription">Assignment Description</label>
            <textarea class="form-control mt-2" id="assignmentdescription" name="assignmentdescription" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="assignmentfile">Assignment File (Optional)</label>
            <input type="file" class="form-control-file mt-2" id="assignmentfile" name="assignmentfile">
        </div>

        <div class="form-group mt-3">
            <label for="assignmentdeadline">Assignment Deadline</label>
            <input type="date" class="form-control mt-2" id="assignmentdeadline" name="assignmentdeadline" required>
        </div>

        <div class="form-group mt-3">
            <label for="marks">Marks</label>
            <input type="number" class="form-control mt-2" id="marks" name="marks" required>
        </div>

        <div class="form-group mt-3">
            <label for="assignedto">Batch</label>
            <select class="w-100 p-1 mt-2" id="assignedto" name="assignedto" required>
                <option value="">Select Batch</option>
                <?php while ($row = mysqli_fetch_assoc($batchResult)) {
                    echo "<option value='" . $row['batchid'] . "'>" . $row['batchcode'] . "</option>";
                } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-dark float-end w-50 mt-3" name="submit">Upload Assignment</button>
    </form>
</div>

<!-- Display Assignments -->
<div class="container mt-5">
    <h4 class="mb-3">Assignments List</h4>
    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Marks</th>
                <th>File</th>
                <th>Batch Code</th>
                <th>Actions</th>
                <th>Submissions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($assignment = mysqli_fetch_assoc($assignmentResult)) { ?>
                <tr>
                    <td><?= $assignment['assignmentid'] ?></td>
                    <td><?= htmlspecialchars($assignment['assignmentname']) ?></td>
                    <td><?= htmlspecialchars($assignment['assignmentdescription']) ?></td>
                    <td><?= date('jS F Y', strtotime($assignment['assignmentdeadline'])) ?></td>
                    <td><?= htmlspecialchars($assignment['marks']) ?></td>
                    <td>
                        <?php if ($assignment['assignmentfile']) { ?>
                            <a href="<?= $assignment['assignmentfile'] ?>" target="_blank">View File</a>
                        <?php } else { ?>
                            <span class="text-muted">No Attachment</span>
                        <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($assignment['batchcode']) ?></td>
                    <td>
                        <a href="edit_assignment.php?id=<?= $assignment['assignmentid'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_assignment.php?id=<?= $assignment['assignmentid'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                    <td>
                        <a href="view_submissions.php?assignmentid=<?= $assignment['assignmentid'] ?>" class="btn btn-dark btn-sm">View Submissions</a>
                    </td>
                </tr>
            <?php } ?>

            <?php if (mysqli_num_rows($assignmentResult) == 0) { ?>
                <tr><td colspan="8">No assignments found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include('stafffooter.php');
?>
