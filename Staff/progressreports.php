<?php
include('../connect.php');
include('staffheader.php');

// Fetch batches for dropdown
$batchQuery = mysqli_query($conn, "SELECT * FROM batches");

// Fetch students of selected batch
$students = [];
if (isset($_POST['batchfilter'])) {
    $batchid = $_POST['batchfilter'];
    $studentsQuery = mysqli_query($conn, "SELECT * FROM student WHERE studentbatch = '$batchid'");
    while ($row = mysqli_fetch_assoc($studentsQuery)) {
        $students[] = $row;
    }
}

// Handle bulk progress submission
if (isset($_POST['btnaddprogress'])) {
    foreach ($_POST['studentid'] as $key => $studentid) {
        $assignmentmarks = $_POST['assignmentmarks'][$key];
        $quizmarksinternal = $_POST['quizmarksinternal'][$key];
        $practical = $_POST['practical'][$key];
        $modular = $_POST['modular'][$key];
        $classes_conducted = $_POST['classes_conducted'][$key]; // New Field
        $classes_held = $_POST['classes_held'][$key]; // New Field
        $dateofprogress = $_POST['dateofprogress'];
        $remarks = $_POST['remarks'][$key];

        $query = "INSERT INTO studentprogress 
                  (studentid, assignmentmarks, quizmarksinternal, practical, modular, classes_conducted, classes_held, dateofprogress, remarks) 
                  VALUES 
                  ('$studentid', '$assignmentmarks', '$quizmarksinternal', '$practical', '$modular', '$classes_conducted', '$classes_held', '$dateofprogress', '$remarks')";
        mysqli_query($conn, $query);
    }

    $success = "Progress Saved Successfully!";
}
?>

<!-- Bootstrap Alert for Success -->
<div class="container mt-3">
    <?php if(isset($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>
</div>

<!-- Batch Selection Form -->
<div class="container-fluid mt-4">
    <h4>Select Batch to Add Progress</h4>
    <hr>
    <form method="post">
        <div class="row">
            <div class="col-md-8">
                <select name="batchfilter" class="form-control" required>
                    <option value="" selected disabled>Select Batch</option>
                    <?php while ($row = mysqli_fetch_array($batchQuery)) {
                        echo '<option value="'.$row['batchid'].'">'.$row['batchcode'].'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100">Show Students</button>
            </div>
        </div>
    </form>
</div>

<!-- Student Progress Form -->
<?php if (!empty($students)) { ?>
<div class="container-fluid mt-4">
    <h4>Enter Student Progress</h4>
    <hr>
    <form action="" method="post">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Student Name</th>
                    <th>Assignment (100)</th>
                    <th>Quiz (100)</th>
                    <th>Practical (20)</th>
                    <th>Modular (20)</th>
                    <th>Classes Attended</th>
                    <th>Classes Held</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student) { ?>
                    <tr>
                        <td>
                            <?php echo $student['studentname']; ?>
                            <input type="hidden" name="studentid[]" value="<?php echo $student['studentid']; ?>">
                        </td>
                        <td><input type="number" name="assignmentmarks[]" class="form-control" required></td>
                        <td><input type="number" name="quizmarksinternal[]" class="form-control" required></td>
                        <td><input type="number" name="practical[]" class="form-control"></td>
                        <td><input type="number" name="modular[]" class="form-control"></td>
                        <td><input type="number" name="classes_conducted[]" class="form-control" required></td>
                        <td><input type="number" name="classes_held[]" class="form-control" required></td>
                        <td><input type="text" name="remarks[]" class="form-control" placeholder="Enter remarks"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <label for="dateofprogress" class="mt-2"><b>Date of Progress</b></label>
        <input type="date" class="form-control mt-2" name="dateofprogress" required>

        <button type="submit" class="btn btn-dark mt-3 w-100" name="btnaddprogress">Save Progress</button>
    </form>
</div>
<?php } ?>

<?php include('stafffooter.php'); ?>
