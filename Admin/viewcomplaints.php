<?php
include('../connect.php');
include('headeradmin.php');

// Fetch complaints
$query = "SELECT * FROM student_complaints ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div class="container mt-4">
    <h3>View Complaints</h3>
    <table id="complaintsTable" class="display table table-bordered table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Batch</th>
                <th>Faculty</th>
                <th>Complaint Type</th>
                <th>Recieved On</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                        <td>" . htmlspecialchars($row['batch']) . "</td>
                        <td>" . htmlspecialchars($row['faculty']) . "</td>
                        <td>" . htmlspecialchars($row['complaint_type']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>" . htmlspecialchars($row['remarks']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No complaints found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <button onclick="window.location.href='dashboard.php'" class="btn btn-warning mt-3 float-end">Go Back</button>

</div>


<!-- DataTables Initialization -->
<script>
    $(document).ready(function () {
        $('#complaintsTable').DataTable({
            "order": [[4, "desc"]] // Sort by 'Created At' column
        });
    });
</script>

<?php
include('footeradmin.php');
?>
