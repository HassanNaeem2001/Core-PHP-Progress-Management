<?php
session_start();
include('connect.php'); // Include database connection

$error = '';

if (isset($_POST['btnlogin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5(mysqli_real_escape_string($conn, $_POST['password'])); // Hash input with MD5

    // Fetch student details based on email
    $query = mysqli_query($conn, "SELECT * FROM student WHERE studentemail='$email' OR enrollmentno='$email' LIMIT 1");

    if (mysqli_num_rows($query) == 1) {
        $student = mysqli_fetch_assoc($query);

        // Verify password and check status
        if ($student['studentpassword'] === $password) {
            if ($student['studentstatus'] === 'Active') {
                // Start session and store student data
                $_SESSION['studentloggedin'] = true;
                $_SESSION['studentid'] = $student['studentid'];
                $_SESSION['studentname'] = $student['studentname'];
                $_SESSION['enrollmentno'] = $student['enrollmentno'];
                $_SESSION['studentemail'] = $student['studentemail'];
                $_SESSION['studentbatch'] = $student['studentbatch'];
                $_SESSION['studentphoneno'] = $student['studentphoneno'];

                // Redirect to dashboard
                header("Location: Student/studentdashboard.php");
                exit();
            } else {
                $error = "Your account is not active. Contact administration.";
            }
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "No student found with this email!";
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Student Portal</title>
        <link rel="stylesheet" href="style.css">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    </head>

    <body>
        <div class="d-flex justify-content-center align-items-center maindiv">
            <div class="w-75 mt-5 innerdiv">
                <center><img src="Images/aptlogo.png" height="150px" alt=""></center>
                <h1 class="text-center" style="font-family: 'Oswald', sans-serif;">Student Portal</h1>
                <p class="text-center disclaimer">This portal can be used by <b>Scheme 33</b> students only</p>
                <hr>
                <?php if (!empty($error)) { echo '<div class="alert alert-danger text-center">'.$error.'</div>'; } ?>
                <form action="" method="post">
                    <input type="text" class="form-control w-100" name="username" placeholder="Student ID or Email" required />
                    <br>
                    <input type="password" class="form-control w-100" name="password" placeholder="Password" required />
                    <br>
                    <button type="submit" class="w-100 btn btn-dark" name="btnlogin">Login</button>
                </form>
            </div>
        </div>
    </body>
</html>
