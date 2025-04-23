<?php
include('../connect.php'); // Database connection


// Check if the form is submitted
if (isset($_POST['submit'])) {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $job_description = mysqli_real_escape_string($conn, $_POST['job_description']);
    $apply_before = $_POST['apply_before'];

    // Insert job into the database
    $query = "INSERT INTO jobs (jobtitle, jobdescription, applybefore) VALUES ('$job_title', '$job_description', '$apply_before')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Job posted successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Delete job functionality
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM jobs WHERE jobid = $job_id");
    header("Location:postjobs.php"); // Redirect to refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Post Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmDelete(jobId) {
            if (confirm("Are you sure you want to delete this job?")) {
                window.location.href = "postjobs.php?delete=" + jobId;
            }
        }
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let tableRows = document.getElementById("jobsTable").getElementsByTagName("tr");

            for (let i = 1; i < tableRows.length; i++) {
                let row = tableRows[i];
                let cells = row.getElementsByTagName("td");
                let rowText = "";
                
                for (let j = 0; j < cells.length; j++) {
                    rowText += cells[j].textContent.toLowerCase() + " ";
                }

                row.style.display = rowText.includes(input) ? "" : "none";
            }
        }
    </script>
</head>
<body>
    <?php include('headeradmin.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4">Post a New Job</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input type="text" class="form-control" name="job_title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Job Description</label>
                <textarea class="form-control" name="job_description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Apply Before (Deadline)</label>
                <input type="date" class="form-control" name="apply_before" required>
            </div>
            <button type="submit" name="submit" class="btn btn-dark float-end w-50 m-2">Post Job</button>
        </form>
        <button onclick="window.location.href='dashboard.php'" class="btn btn-warning mt-2 float-end">Go Back</button>

        <div class="container mt-5">
        <h3 class="mb-4">Manage Jobs</h3>

        <!-- Search Input -->
        <input type="text" id="searchInput" class="form-control mb-3" onkeyup="searchTable()" placeholder="Search for jobs...">

        <table class="table table-dark table-striped table-bordered table-hover shadow-lg mt-3">
            <thead class="bg-dark text-white">
                <tr style="background-color:black">
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Apply Before</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="jobsTable">
                <?php
                include('../connect.php');
                $result = mysqli_query($conn, "SELECT * FROM jobs ORDER BY jobid DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['jobid']}</td>
                        <td>{$row['jobtitle']}</td>
                        <td>{$row['jobdescription']}</td>
                        <td>{$row['applybefore']}</td>
                        <td>
                            <button class='btn btn-danger' onclick='confirmDelete({$row['jobid']})'>Delete</button>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php include('footeradmin.php'); ?>
</body>
</html>
