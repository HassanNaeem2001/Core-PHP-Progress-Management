<?php
include('../connect.php');
include('headeradmin.php');

// Get batchid from URL
if (isset($_GET['batchid'])) {
    $batchid = $_GET['batchid'];

    // Fetch the batch details for the specific batchid
    $query = "SELECT * FROM batches WHERE batchid = '$batchid'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // If no batch found, redirect
    if (!$row) {
        header("Location: batches.php");
        exit();
    }
} else {
    header("Location: batches.php");
    exit();
}

// Handle Batch Update
if (isset($_POST['btnupdatebatch'])) {
    $batchcode = $_POST['batchcode'];
    $batchtimings = $_POST['batchtimings'];
    $batchdays = $_POST['batchdays'];
    $batchfaculty = $_POST['batchfaculty'];
    $batchtype = $_POST['batchtype'];
    $batchstartdate = $_POST['batchstartdate'];
    $batchsemester = $_POST['batchsemester'];

    // Update query
    $updateQuery = "UPDATE batches SET 
                    batchcode = '$batchcode', 
                    batchtimings = '$batchtimings', 
                    batchdays = '$batchdays', 
                    batchinstructor = '$batchfaculty', 
                    batchtype = '$batchtype', 
                    batchstartdate = '$batchstartdate', 
                    currentsem = '$batchsemester' 
                    WHERE batchid = '$batchid'";

    if (mysqli_query($conn, $updateQuery)) {
        $success = "Batch updated successfully!";
    } else {
        $error = "Error in updating batch!";
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

<!-- Update Batch Form -->
<div class="container-fluid">
    <h4 class="m-4">Batches - Update Batch</h4>
    <hr>
    <div class="d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form action="" method="post">
                <input type="text" name="batchcode" class="mt-2 form-control" value="<?php echo $row['batchcode']; ?>" placeholder="Enter Batch Code" required>

                <select name="batchtimings" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Timings</option>
                    <option value="9-11" <?php echo ($row['batchtimings'] == '9-11') ? 'selected' : ''; ?>>9-11</option>
                    <option value="11-1" <?php echo ($row['batchtimings'] == '11-1') ? 'selected' : ''; ?>>11-1</option>
                    <option value="1-3" <?php echo ($row['batchtimings'] == '1-3') ? 'selected' : ''; ?>>1-3</option>
                    <option value="3-5" <?php echo ($row['batchtimings'] == '3-5') ? 'selected' : ''; ?>>3-5</option>
                    <option value="5-7" <?php echo ($row['batchtimings'] == '5-7') ? 'selected' : ''; ?>>5-7</option>
                    <option value="7-9" <?php echo ($row['batchtimings'] == '7-9') ? 'selected' : ''; ?>>7-9</option>
                </select>

                <select name="batchdays" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Days</option>
                    <option value="T.T.S" <?php echo ($row['batchdays'] == 'T.T.S') ? 'selected' : ''; ?>>T.T.S</option>
                    <option value="M.W.F" <?php echo ($row['batchdays'] == 'M.W.F') ? 'selected' : ''; ?>>M.W.F</option>
                </select>

                <select name="batchfaculty" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Faculty</option>
                    <?php
                    $facultyQuery = mysqli_query($conn, "SELECT * FROM staff WHERE staffdesignation = 'Faculty'");
                    while ($facultyRow = mysqli_fetch_array($facultyQuery)) {
                        $selected = ($row['batchinstructor'] == $facultyRow['staffid']) ? 'selected' : '';
                        echo '<option value="' . $facultyRow['staffid'] . '" ' . $selected . '>' . $facultyRow['staffname'] . '</option>';
                    }
                    ?>
                </select>

                <select name="batchtype" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Batch Type</option>
                    <option value="ACCP" <?php echo ($row['batchtype'] == 'ACCP') ? 'selected' : ''; ?>>ACCP</option>
                    <option value="STC" <?php echo ($row['batchtype'] == 'STC') ? 'selected' : ''; ?>>STC</option>
                    <option value="Other" <?php echo ($row['batchtype'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    <option value="Digital Marketing" <?php echo ($row['batchtype'] == 'Digital Marketing') ? 'selected' : ''; ?>>Digital Marketing</option>
                </select>

                <label for="batchstartdate" class="mt-2"><b>Start Date</b></label>
                <input type="date" class="form-control mt-2" name="batchstartdate" value="<?php echo $row['batchstartdate']; ?>" required>

                <select name="batchsemester" class="mt-2 w-100 p-1" required>
                    <option value="" selected disabled>Select Batch Semester</option>
                    <option value="CPISM" <?php echo ($row['currentsem'] == 'CPISM') ? 'selected' : ''; ?>>CPISM</option>
                    <option value="DISM" <?php echo ($row['currentsem'] == 'DISM') ? 'selected' : ''; ?>>DISM</option>
                    <option value="HDSE |" <?php echo ($row['currentsem'] == 'HDSE |') ? 'selected' : ''; ?>>HDSE |</option>
                    <option value="HDSE ||" <?php echo ($row['currentsem'] == 'HDSE ||') ? 'selected' : ''; ?>>HDSE ||</option>
                    <option value="ADSE |" <?php echo ($row['currentsem'] == 'ADSE |') ? 'selected' : ''; ?>>ADSE |</option>
                    <option value="ADSE ||" <?php echo ($row['currentsem'] == 'ADSE ||') ? 'selected' : ''; ?>>ADSE ||</option>
                    <option value="Other" <?php echo ($row['currentsem'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>

                <button type="submit" class="btn btn-dark mt-2 w-50 float-end" name="btnupdatebatch">Update Batch</button>
            </form>
        </div>
    </div>
</div>

<?php include('footeradmin.php'); ?>
