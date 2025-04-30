<?php
include('../connect.php');
include('headeradmin.php');

if (isset($_POST['submit'])) {
    $studentid = $_POST['student'];
    $batchid = $_POST['batch'];

    // Get batchcode from batchid
    $batch = $conn->prepare("SELECT batchcode FROM batches WHERE batchid = ?");
    $batch->bind_param("i", $batchid);
    $batch->execute();
    $batch_result = $batch->get_result();
    if ($batch_result->num_rows > 0) {
        $batchcode = $batch_result->fetch_assoc()['batchcode'];

        // Insert record
        $stmt = $conn->prepare("INSERT INTO student_of_the_month (studentid, batchcode) VALUES (?, ?)");
$stmt->bind_param("ii", $studentid, $batchid); // batchid is the foreign key

        $stmt->execute();
        echo '<script>alert("Student has been awarded as SOM")</script>';
    }
}
if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $conn->query("DELETE FROM student_of_the_month WHERE id = $delete_id");
}

?>

<div class="container mt-4">
    <h3>Student of the Month</h3>
    <form method="POST" class="row mb-4">
        <div class="col-md-4">
            <label>Batch</label>
            <select name="batch" id="batch" class="form-control" required>
                <option value="">Select Batch</option>
                <?php
                $batches = $conn->query("SELECT batchid, batchcode FROM batches");
                while ($b = $batches->fetch_assoc()) {
                    echo "<option value='{$b['batchid']}'>{$b['batchcode']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Student</label>
            <select name="student" id="student" class="form-control" required>
                <option value="">Select Student</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>&nbsp;</label>
            <button type="submit" name="submit" class="btn btn-dark w-100 float-end">Award Student</button>
        </div>
    </form>

    <div class="row">
        <div class="col-6">
        <h4>Last 10 Students of the Month</h4>
        </div> 
        <div class="col-6">
        <a href="download_som_excel.php" class="btn btn-warning mb-3 float-end ">Download Excel</a>

        </div> 
    </div>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Batch</th>
                <th>Faculty</th>
                <th>Awarded At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = "SELECT som.id, s.studentid, s.studentname, b.batchcode, st.staffname, 
            DATE_FORMAT(som.awarded_at, '%M %Y') AS awarded_month
     FROM student_of_the_month som
     JOIN studentprogresssystem.student s ON som.studentid = s.studentid
     JOIN studentprogresssystem.batches b ON som.batchcode = b.batchid
     JOIN studentprogresssystem.staff st ON b.batchinstructor = st.staffid
     ORDER BY som.awarded_at DESC
     LIMIT 10";


            $result = $conn->query($q);
            while ($r = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$r['studentid']}</td>
                    <td>{$r['studentname']}</td>
                    <td>{$r['batchcode']}</td>
                    <td>{$r['staffname']}</td>
                   <td>{$r['awarded_month']}</td>
                   <td>
                <form method='POST' onsubmit='return confirm(\"Are you sure?\")'>
                    <input type='hidden' name='delete_id' value='{$r['id']}'>
                    <button type='submit' name='delete' class='btn btn-sm btn-danger'>Delete</button>
                </form>
            </td>

                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#batch').change(function () {
    var batchid = $(this).val();
    $.post('get_students_by_batch.php', { batchid: batchid }, function (data) {
        $('#student').html(data);
    });
});
</script>

<?php include('footeradmin.php'); ?>
