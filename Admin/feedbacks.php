<?php
include('../connect.php');
include('headeradmin.php');

$selected_batch = $_GET['batch'] ?? '';
$selected_month = $_GET['month'] ?? '';
$selected_year = $_GET['year'] ?? '';
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

            <!-- Year Dropdown -->
            <div class="col-md-4">
                <label for="year">Select Year:</label>
                <select name="year" id="year" class="form-control" required>
                    <option value="">-- Choose Year --</option>
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= 2020; $y--) {
                        $selected = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
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

    <?php if ($selected_batch && $selected_month && $selected_year): ?>
        <?php
        $month = str_pad($selected_month, 2, '0', STR_PAD_LEFT);

        // Fetch all students from selected batch
   $students_query = "SELECT * FROM student WHERE studentbatch = '$selected_batch'";




        $students_result = mysqli_query($conn, $students_query);
        $all_students = [];
        while ($student = mysqli_fetch_assoc($students_result)) {
            $all_students[$student['studentid']] = $student;
        }

        // Fetch feedback for selected month/year
        $feedback_query = "
            SELECT * FROM feedback f
            JOIN student s ON f.feedbackgivenby = s.studentid
            WHERE f.feedbackofbatch = '$selected_batch'
            AND MONTH(f.feedbackmonth) = '$month'
            AND YEAR(f.feedbackmonth) = '$selected_year'
        ";
        $feedback_result = mysqli_query($conn, $feedback_query);

        $feedback_rows = [];
        $students_with_feedback = [];

        while ($row = mysqli_fetch_assoc($feedback_result)) {
            $feedback_rows[] = $row;
            $students_with_feedback[] = $row['studentid'];
        }

        $students_without_feedback = array_diff(array_keys($all_students), $students_with_feedback);
        ?>

        <!-- Feedback Table -->
        <h5 class="mt-4">Students Who Gave Feedback</h5>
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
                                <td><?= $row['enrollmentno'] ?></td>
                                <td><?= $row['studentname'] ?></td>
                                <td><?= $row['remarks'] ?></td>
                                <td><?= date('d F Y', strtotime($row['feedbackmonth'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No feedback found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Students Who Didn't Submit Feedback -->
        <h5 class="mt-4 text-danger">Students Who Did NOT Submit Feedback</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students_without_feedback)): ?>
                        <?php foreach ($students_without_feedback as $studentid): ?>
                            <tr>
                                <td><?=$all_students[$studentid]['enrollmentno'] ?? '-' ?></td>
                                <td><?= $all_students[$studentid]['studentname'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center text-success">All students have submitted feedback.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- GPA Calculations -->
        <?php if (!empty($feedback_rows)): ?>
            <?php
            $parameters = [
                'punctuality'      => 'Punctuality',
                'technicalstaff'   => 'Technical Support / Staff',
                'coursecoverage'   => 'Course Coverage',
                'doubtsclarity'    => 'Doubts Clearing',
                'exams'            => 'Exams & Assignments',
                'bookutilization'  => 'Book Utilization'
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
