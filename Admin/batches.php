<?php
include('../connect.php');
include('headeradmin.php');

// Handle Form Submission
if (isset($_POST['btnaddbatch'])) {
    $batchcode = $_POST['batchcode'];
    $batchtimings = $_POST['batchtimings'];
    $batchdays = $_POST['batchdays'];
    $batchfaculty = $_POST['batchfaculty'];
    $batchtype = $_POST['batchtype'];
    $batchstartdate = $_POST['batchstartdate'];
    $batchsemester = $_POST['batchsemester']; // Added semester field

    $query = "INSERT INTO batches (batchcode, batchtimings, batchdays, batchinstructor, batchtype, batchstartdate, currentsem) 
              VALUES ('$batchcode', '$batchtimings', '$batchdays', '$batchfaculty', '$batchtype', '$batchstartdate', '$batchsemester')";

    if (mysqli_query($conn, $query)) {
        $success = "Batch Added Successfully!";
    } else {
        $error = "Error in Adding Batch!";
    }
}

// Fetch records for displaying in the table
$query = "SELECT * FROM batches INNER JOIN staff ON staff.staffid = batches.batchinstructor";
$result = mysqli_query($conn, $query);
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

<!-- Batch Registration Form -->
<div class="container-fluid">
    <h4 class="m-4">Batches - Add Batch</h4>
    <hr>
    <div class="d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form action="" method="post">
                <input type="text" name="batchcode" class="mt-2 form-control" placeholder="Enter Batch Code" required>
                
                <select name="batchtimings" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Timings</option>
                    <option value="9-11">9-11</option>
                    <option value="11-1">11-1</option>
                    <option value="1-3">1-3</option>
                    <option value="3-5">3-5</option>
                    <option value="5-7">5-7</option>
                    <option value="7-9">7-9</option>
                </select>

                <select name="batchdays" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Days</option>
                    <option value="T.T.S">T.T.S</option>
                    <option value="M.W.F">M.W.F</option>
                </select>

                <select name="batchfaculty" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Faculty</option>
                    <?php
                    $facultyQuery = mysqli_query($conn, "SELECT * FROM staff WHERE staffdesignation = 'Faculty'");
                    while ($row = mysqli_fetch_array($facultyQuery)) {
                        echo '<option value="'.$row['staffid'].'">'.$row['staffname'].'</option>';
                    }
                    ?>
                </select>

                <select name="batchtype" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Batch Type</option>
                    <option value="ACCP">ACCP</option>
                    <option value="STC">STC</option>
                    <option value="Other">Other</option>
                    <option value="Digital Marketing">Digital Marketing</option>
                </select>

                <label for="batchstartdate" class="mt-2"><b>Start Date</b></label>
                <input type="date" class="form-control mt-2" name="batchstartdate" required>

                <select name="batchsemester" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Semester</option>
                    <option value="CPISM">CPISM</option>
                    <option value="DISM">DISM</option>
                    <option value="HDSE |">HDSE |</option>
                    <option value="HDSE ||">HDSE ||</option>
                    <option value="ADSE |">ADSE |</option>
                    <option value="ADSE ||">ADSE ||</option>
                    <option value="Other">Other</option>
                </select>

                <button type="submit" class="btn btn-dark mt-2 w-50 float-end" name="btnaddbatch">Add Batch</button>
            </form>
        </div>
    </div>
</div>

<!-- Batch Records Table -->
<div class="container-fluid mt-5">
    <h4 class="m-4 pt-3">Batches - View Batches</h4>
    <hr>
    <table id="batchesTable" class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Batch Code</th>
                <th>Faculty</th>
                <th>Batch Type</th>
                <th>Batch Semester</th> <!-- Added Semester Column -->
                <th>Batch Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_array($result)) { ?>
                <tr>
                    <td><?php echo $row['batchcode']; ?></td>
                    <td><?php echo $row['staffname']; ?></td>
                    <td><?php echo $row['batchtype']; ?></td>
                    <td><?php echo $row['currentsem']; ?></td> <!-- Displaying Semester -->
                    <td>
                        <?php
                        if($row['batchstatus'] == 'active') {
                            echo '<p class="text-success">Active</p>';
                        } else {
                            echo '<p class="text-danger">Inactive</p>';
                        }
                   ?>
                    </td>
                  <?php
            }
                  ?>
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
    $('#batchesTable').DataTable({
        "paging": true, // Enables pagination
        "searching": true, // Enables search
        "ordering": true, // Enables sorting
        "info": true, // Shows info about entries
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]] // Records per page
    });
});
</script>
