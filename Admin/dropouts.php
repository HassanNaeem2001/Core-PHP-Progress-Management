<?php
include('../connect.php');
include('headeradmin.php');

// Fetch dropout students with batchcode from batches table
$query = "SELECT s.studentname, b.batchcode, s.studentemail, s.studentphoneno, s.dropout_date, s.cc_date
          FROM student s
          JOIN batches b ON s.studentbatch = b.batchid
          WHERE s.studentstatus = 'dropout'
          ORDER BY s.dropout_date DESC";
$result = mysqli_query($conn, $query);
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div class="container mt-4">
    <h3>Dropout Students</h3>
    <table id="dropoutTable" class="display table table-bordered table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Batch Code</th>
                <th>Email</th>
                <th>StudentId</th>
                <th>Dropout Date</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['studentname'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['batchcode'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['studentemail'] ?? '') . "</td>
                        <td>" . htmlspecialchars($row['studentphoneno'] ?? '') . "</td>
                       <td>" . date('jS F Y', strtotime($row['dropout_date'])) . "</td>

                       
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No dropout students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <br>
    <button onclick="window.location.href='dashboard.php'" class="btn btn-warning mt-2 float-end">Go Back</button>

</div>

<!-- DataTables Initialization -->
<script>
    $(document).ready(function () {
        $('#dropoutTable').DataTable({
            "order": [[4, "desc"]] // Sort by Dropout Date
        });
    });
</script>

<?php include('footeradmin.php'); ?>
