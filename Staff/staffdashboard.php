<?php
include('../connect.php');
include('staffheader.php');


$staff_id = $_SESSION['staff_id'];

// Fetch staff details
$query = "SELECT * FROM staff WHERE staffid = '$staff_id' LIMIT 1";
$result = mysqli_query($conn, $query);
$staff = mysqli_fetch_assoc($result);

// Update staff details
if (isset($_POST['update_staff'])) {
    $staffname = mysqli_real_escape_string($conn, $_POST['staffname']);
    $staffemail = mysqli_real_escape_string($conn, $_POST['staffemail']);
    $staffdesignation = mysqli_real_escape_string($conn, $_POST['staffdesignation']);
    $stafftimings = mysqli_real_escape_string($conn, $_POST['stafftimings']);
    $staffphone = mysqli_real_escape_string($conn, $_POST['staffphone']);

    $updateQuery = "UPDATE staff SET 
                    staffname = '$staffname',
                    staffemail = '$staffemail',
                    staffdesignation = '$staffdesignation',
                    stafftimings = '$stafftimings',
                    staffphone = '$staffphone'
                    WHERE staffid = '$staff_id'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Details updated successfully!'); window.location.href='staffdashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating details!');</script>";
    }
}

?>

<div class="container mt-5">
    <h2 class="text-center">Staff Dashboard</h2>
    <p class="text-center">Welcome, <strong><?php echo $staff['staffname']; ?></strong></p>

    <div class="card shadow-lg p-4 mt-4">
        <h4 class="text-center mb-3">Staff Details</h4>
        <table class="table table-bordered">
            <tr><th>Staff Name</th><td><?php echo $staff['staffname']; ?></td></tr>
            <tr><th>Email</th><td><?php echo $staff['staffemail']; ?></td></tr>
            <tr><th>Designation</th><td><?php echo $staff['staffdesignation']; ?></td></tr>
            <tr><th>Timings</th><td><?php echo $staff['stafftimings']; ?></td></tr>
            <tr><th>Phone</th><td><?php echo $staff['staffphone']; ?></td></tr>
            <tr><th>Date of Joining</th><td><?php echo $staff['dateofjoining']; ?></td></tr>
            <tr><th>Date of Resignation</th><td><?php echo !empty($staff['dateofresignation']) ? $staff['dateofresignation'] : 'Still Working'; ?></td></tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-<?php echo ($staff['status'] == 'Active') ? 'success' : 'danger'; ?>">
                        <?php echo $staff['status']; ?>
                    </span>
                </td>
            </tr>
        </table>

        <!-- Edit Button -->
        <div class="text-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">Edit Details</button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Your Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Staff Name</label>
                        <input type="text" name="staffname" class="form-control" value="<?php echo $staff['staffname']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="staffemail" class="form-control" value="<?php echo $staff['staffemail']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text" name="staffdesignation" class="form-control" value="<?php echo $staff['staffdesignation']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timings</label>
                        <input type="text" name="stafftimings" class="form-control" value="<?php echo $staff['stafftimings']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="staffphone" class="form-control" value="<?php echo $staff['staffphone']; ?>" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="update_staff" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('stafffooter.php'); ?>
