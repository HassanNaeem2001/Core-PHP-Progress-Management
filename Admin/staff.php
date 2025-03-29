<?php
include('../connect.php');
include('headeradmin.php');

// Handle Form Submission
if(isset($_POST['btnregisterfaculty'])) {
    $staffname = $_POST['staffname'];
    $staffemail = $_POST['staffemail'];
    $staffpassword = $_POST['staffpassword'];
    $staffphone = $_POST['staffphone'];
    $staffdesignation = $_POST['staffdesignation'];
    $stafftimings = $_POST['stafftimings'];
    $date = $_POST['date'];

    $query = "INSERT INTO staff (staffname, staffemail, staffpassword, staffphone, staffdesignation, stafftimings, dateofjoining) 
              VALUES ('$staffname', '$staffemail', '$staffpassword', '$staffphone', '$staffdesignation', '$stafftimings', '$date')";

    if(mysqli_query($conn, $query)) {
        $success = "Staff Added Successfully!";
    } else {
        $error = "Error in Registration!";
    }
}
?>

<!-- Bootstrap Alert for Success/Error Messages -->
<div class="container mt-3">
    <?php if(isset($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if(isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
</div>

<!-- Staff Registration Form -->
<div class="container-fluid mt-3">
    <h4 class="m-4">Staff - Register</h4>
    <hr>
   <div class="d-flex justify-content-center align-items-center">
    <div class="w-75">
    <form action="" method="post">
        <input type="text" class="form-control w-100 mt-2" name="staffname" placeholder="Staff Name" required />
        <input type="email" class="form-control w-100 mt-2" name="staffemail" placeholder="Staff Email" required />
        <input type="text" class="form-control w-100 mt-2" name="staffpassword" placeholder="Staff Password" required />
        <input type="text" class="form-control w-100 mt-2" name="staffphone" placeholder="Staff Phone" required />
        
        <select name="staffdesignation" class="p-1 w-100 mt-2">
            <option value="" selected disabled>Select Staff Role</option>
            <option value="Center Manager">Center Manager</option>
            <option value="Center Academic Head">Center Academic Head</option>
            <option value="Admin">Admin</option>
            <option value="Faculty">Faculty</option>
            <option value="Counselor">Counselor</option>
            <option value="Receptionist">Receptionist</option>
            <option value="HR">HR</option>
            <option value="Marketing">Marketing</option>
            <option value="Accountant">Accountant</option>
            <option value="Other">Other</option>
        </select>

        <select name="stafftimings" class="p-1 w-100 mt-2">
            <option value="" selected disabled>Select Staff Timings</option>
            <option value="Full Time">Full Time</option>
            <option value="Part Time">Part Time</option>
            <option value="Intern">Intern</option>
        </select>

        <label for="date" class="mt-2"><b>Date of Joining</b></label>
        <input type="date" class="form-control w-100" name="date" required />

        <button type="submit" class="btn btn-dark mt-2 float-end w-50" name="btnregisterfaculty">Register Staff</button>
    </form>
    </div>
   </div>
</div>

<!-- View Staff -->
<div class="container-fluid mt-5">
    <h4 class="m-4">Staff - View</h4>
    <hr>
    <table id="staffTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Staff Email</th>
                <th>Staff Designation</th>
                <th>Staff Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM staff") or die(mysqli_error($conn));
            while($row = mysqli_fetch_array($q)) {
            ?>
            <tr>
                <td><?php echo $row['staffname']; ?></td>
                <td><?php echo $row['staffemail']; ?></td>
                <td><?php echo $row['staffdesignation']; ?></td>
                <td>
                    <?php if($row['status'] == 'active') { ?>
                        <p class="text-success">Active</p>
                    <?php } else { ?>
                        <p class="text-danger">Inactive</p>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include('footeradmin.php'); ?>

<!-- Bootstrap & DataTable Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#staffTable').DataTable(); // Initialize Bootstrap DataTable
});
</script>
