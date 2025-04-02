<?php
include('../connect.php');
include('staffheader.php');

if (isset($_GET['id'])) {
    $progressid = $_GET['id'];

    // Fetch the specific progress record
    $query = mysqli_query($conn, "SELECT * FROM studentprogress WHERE progressno = '$progressid'");
    $record = mysqli_fetch_assoc($query);
}

if (isset($_POST['update'])) {
    $progressid = $_POST['progressid'];
    $assignmentmarks = $_POST['assignmentmarks'];
    $quizmarksinternal = $_POST['quizmarksinternal'];
    $practical = $_POST['practical'];
    $modular = $_POST['modular'];
    $remarks = $_POST['remarks'];

    // Update the database
    mysqli_query($conn, "
        UPDATE studentprogress 
        SET assignmentmarks='$assignmentmarks', quizmarksinternal='$quizmarksinternal', 
            practical='$practical', modular='$modular', remarks='$remarks' 
        WHERE progressno='$progressid'
    ");

    echo "<script>alert('Progress Updated Successfully'); window.location.href='fetchprogressreports.php';</script>";
}
?>

<div class="container mt-4">
    <h4>Edit Student Progress</h4>
    <hr>
    <form method="post">
        <input type="hidden" name="progressid" value="<?php echo $record['progressno']; ?>">
        
        <div class="mb-3">
            <label>Assignment Marks</label>
            <input type="number" name="assignmentmarks" class="form-control" value="<?php echo $record['assignmentmarks']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Quiz Marks</label>
            <input type="number" name="quizmarksinternal" class="form-control" value="<?php echo $record['quizmarksinternal']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Practical Marks</label>
            <input type="number" name="practical" class="form-control" value="<?php echo $record['practical']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Modular Marks</label>
            <input type="number" name="modular" class="form-control" value="<?php echo $record['modular']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control"><?php echo $record['remarks']; ?></textarea>
        </div>

        <button type="submit" name="update" class="btn btn-dark">Update Progress</button>
    </form>
</div>

<?php include('stafffooter.php'); ?>
