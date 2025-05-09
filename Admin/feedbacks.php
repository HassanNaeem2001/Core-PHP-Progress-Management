<?php
include('../connect.php');
include('headeradmin.php');

$selected_batch = $_GET['batch'] ?? '';
$selected_month = $_GET['month'] ?? '';
?>

<div class="container-fluid">
    <h3 class="mt-4">Feedbacks</h3>
    <hr>

    <!-- Filter Form -->
    <form method="GET" class="mb-4">
        <div class="row">
            <!-- Batch Dropdown -->
            <div class="col-md-4">
                <label for="batch">Select Batch:</label>
                <select name="batch" id="batch" class="form-control" required>
                    <option value="">-- Choose Batch --</option>
                    <?php
                    $batch_result = mysqli_query($conn, "SELECT * FROM batches");
                    while ($batch = mysqli_fetch_assoc($batch_result)) {
                        $selected = ($batch['batchid'] == $selected_batch) ? 'selected' : '';
                        echo "<option value='{$batch['batchid']}' $selected>{$batch['batchcode']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Month Dropdown -->
            <div class="col-md-4">
                <label for="month">Select Month:</label>
                <select name="month" id="month" class="form-control" required>
                    <option value="">-- Choose Month --</option>
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $month_name = date('F', mktime(0, 0, 0, $m, 1));
                        $selected = ($m == $selected_month) ? 'selected' : '';
                        echo "<option value='$m' $selected>$month_name</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="col-md-4 mt-4">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
            </div>
        </div>
    </form>

    <?php if ($selected_batch && $selected_month): ?>
        <?php
        $month = str_pad($selected_month, 2, '0', STR_PAD_LEFT);
        $query = "
            SELECT * 
            FROM feedback f
            JOIN student s ON f.feedbackgivenby = s.studentid
            WHERE f.feedbackofbatch = '$selected_batch'
            AND MONTH(f.feedbackmonth) = '$month'
        ";
        $result = mysqli_query($conn, $query);
        $feedback_rows = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $feedback_rows[] = $row;
            }
        }
        ?>

        <!-- Feedback Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Remarks</th>
                        <th>Feedback Month</th>
                      
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($feedback_rows)): ?>
                        <?php foreach ($feedback_rows as $row): ?>
                            <tr>
                                <td><?= $row['studentid'] ?></td>
                                <td><?= $row['studentname'] ?></td>
                                <td><?= $row['remarks'] ?></td>
                                <td><?= date('d F Y', strtotime($row['feedbackmonth'])) ?></td>
                               
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No feedback found for selected batch and month.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- GPA Calculations -->
        <?php if (!empty($feedback_rows)): ?>
            <?php
            $parameters = [
                'punctuality'        => 'Punctuality',
                'technicalstaff'     => 'Technical Support / Staff',
                'coursecoverage'     => 'Course Coverage',
                'doubtsclarity'      => 'Doubts Clearing',
                'exams'   => 'Exams & Assignments',
                'bookutilization'    => 'Book Utilization'
            ];
            echo "<hr><h5 style='text-align:center;text-decoration:underline'>Monthly GPA Scores</h5>";
            foreach ($parameters as $key => $label) {
                $scoreCount = [4 => 0, 3 => 0, 2 => 0, 1 => 0];
                $total = 0;
                $gpa = 0;

                foreach ($feedback_rows as $row) {
                    $score = (int)($row[$key] ?? 0);
                    if (isset($scoreCount[$score])) {
                        $scoreCount[$score]++;
                        $total++;
                    }
                }

                foreach ($scoreCount as $score => $count) {
                    $gpa += $score * $count;
                }

                $finalGPA = ($total > 0) ? round($gpa / $total, 2) : 0;
                echo "<p><strong>$label GPA:</strong> $finalGPA / 4</p><hr>";
            }
            ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include('footeradmin.php'); ?>
