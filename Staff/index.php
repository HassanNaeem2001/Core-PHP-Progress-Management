<?php
session_start();
include('../connect.php');

if (isset($_POST['btnlogin'])) {
    $usernameOrEmail = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check login with email OR name
    $query = "SELECT * FROM staff WHERE staffemail='$usernameOrEmail' OR staffname='$usernameOrEmail' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $staff = mysqli_fetch_assoc($result);

        // Verify Password (If stored as hashed, otherwise use direct comparison)
        if ($password == $staff['staffpassword']) { // Replace with password_verify() if using hashed passwords
            // Store session variables
            $_SESSION['staff_id'] = $staff['staffid'];
            $_SESSION['staff_name'] = $staff['staffname'];
            $_SESSION['staff_email'] = $staff['staffemail'];
            $_SESSION['staffsession'] = $staff['staffid'];

            // Redirect to staff dashboard
            header("Location: staffdashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No staff found with this email or name!";
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Staff Portal</title>
        <link rel="stylesheet" href="../style.css">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="d-flex justify-content-center align-items-center maindiv">
            <div class="w-75 mt-5 innerdiv">
                <center>
                    <img src="../Images/aptlogo.png" height="150px" alt="">
                </center>
                <h1 class="text-center" style=" font-family: 'Oswald', sans-serif;">Staff Portal</h1>
                <p class="text-center disclaimer">This portal can be used by <b>Scheme 33</b> staff only</p>
                <hr>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                <?php } ?>

                <form action="" method="post">
                    <input type="text" class="form-control w-100" name="username" placeholder="Email or Staff Name" required />
                    <br>
                    <input type="password" class="form-control w-100" name="password" placeholder="Password" required />
                    <br>
                    <button type="submit" class="w-100 btn btn-dark" name="btnlogin">Login</button>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    </body>
</html>
