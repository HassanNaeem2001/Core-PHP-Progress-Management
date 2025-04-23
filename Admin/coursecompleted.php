<?php
include('../connect.php');
include('headeradmin.php');

// Fetch Course Completed students
$query = "SELECT student.*, batches.batchcode 
          FROM student 
          INNER JOIN batches ON student.studentbatch = batches.batchid 
          WHERE studentstatus = 'Course Com' 
          ORDER BY cc_date DESC";
$result = mysqli_query($conn, $query);
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div class="container mt-4">
    <h3>Course Completed Students</h3>
    <table id="ccTable" class="display table table-bordered table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Batch</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course Completed Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['studentname']) . "</td>
                        <td>" . htmlspecialchars($row['batchcode']) . "</td>
                        <td>" . htmlspecialchars($row['studentemail']) . "</td>
                        <td>" . htmlspecialchars($row['studentphoneno']) . "</td>
                       <td>" . date('jS F Y', strtotime($row['cc_date'])) . "</td>

                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No course completed students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- DataTables Initialization -->
<script>
    $(document).ready(function () {
        $('#ccTable').DataTable({
            "order": [[4, "desc"]] // Sort by Course Completed Date
        });
    });
</script>

<?php include('footeradmin.php'); ?>
