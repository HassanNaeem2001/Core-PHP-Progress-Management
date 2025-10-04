<?php
include('../connect.php');
include('staffheader.php');


?>
<div class="container">
    <h3 class="mt-3">Create Attendance Code For</h3>
    <hr>
    <form action="" method="post">
        <div class="row">
            <div class="col-12">
                <label for="batchfilter" class="form-label">Select Batch</label>
                <select name="batchfilter" class="form-control" required>
                    <option value="" selected disabled>Select Batch</option>
                    <?php 
                    $batchQuery = mysqli_query($conn, "SELECT * FROM batches");
                    while ($row = mysqli_fetch_array($batchQuery)) {
                        echo '<option value="'.$row['batchid'].'">'.$row['batchcode'].'</option>';
                    } ?>
                </select>
            </div>
          
        </div>
        <button type="submit" name="btncreateattendance" class="btn btn-primary mt-3">Create Attendance Code</button>    
    </form>
</div>
<?php include('stafffooter.php');

if(isset($_POST['btncreateattendance']))
{
    $staffid = $_SESSION['staff_id'];

    $batchid = $_POST['batchfilter'];
    $attendancecode = strtoupper(bin2hex(random_bytes(3))); // Generate a random 6-character code

    echo "<script>alert('Attendance Code Created: $attendancecode');</script>";

    //Insert into attendance table
    $insertQuery = "INSERT INTO attendancecode (batchid, attendancecode, datecreated) VALUES ('$batchid', '$attendancecode', NOW())";
    if(mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Attendance Code Created: $attendancecode');</script>";
    } else {
        echo "<script>alert('Error creating attendance code.');</script>";
    }
}
?>