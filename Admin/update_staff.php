<?php
include('../connect.php');
include('headeradmin.php');

// Get staffid from URL
if (isset($_GET['staffid'])) {
    $staffid = $_GET['staffid'];

    // Fetch the staff details for the specific staffid
    $query = "SELECT * FROM staff WHERE staffid = '$staffid'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // If no staff found, redirect
    if (!$row) {
        header("Location: staff.php");
        exit();
    }
} else {
    header("Location: staff.php");
    exit();
}

// Handle Staff Update
if (isset($_POST['btnupdatestaff'])) {
    $staffname = $_POST['staffname'];
    $staffemail = $_POST['staffemail'];
    $staffdesignation = $_POST['staffdesignation'];
    $status = $_POST['status'];

    // Update query
    $updateQuery = "UPDATE staff SET 
                    staffname = '$staffname', 
                    staffemail = '$staffemail', 
                    staffdesignation = '$staffdesignation', 
                    status = '$status' 
                    WHERE staffid = '$staffid'";

    if (mysqli_query($conn, $updateQuery)) {
        $success = "Staff updated successfully!";
    } else {
        $error = "Error in updating staff!";
    }
}
?>

<!-- Bootstrap Alert for Success/Error Messages -->
<div class="container mt-3">
    <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
</div>

<!-- Update Staff Form -->
<div class="container-fluid">
    <h4 class="m-4">Staff - Update Staff</h4>
    <hr>
    <div class="d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form action="" method="post">
                <input type="text" name="staffname" class="mt-2 form-control" value="<?php echo $row['staffname']; ?>" placeholder="Enter Staff Name" required>

                <input type="email" name="staffemail" class="mt-2 form-control" value="<?php echo $row['staffemail']; ?>" placeholder="Enter Staff Email" required>

                <input type="text" name="staffdesignation" class="mt-2 form-control" value="<?php echo $row['staffdesignation']; ?>" placeholder="Enter Staff Designation" required>

                <select name="status" class="mt-2 w-100 p-1" required>
                    <option value="active" <?php echo ($row['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($row['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <button type="submit" class="btn btn-dark mt-2 w-50 float-end" name="btnupdatestaff">Update Staff</button>
            </form>
        </div>
    </div>
</div>

<?php include('footeradmin.php'); ?>
