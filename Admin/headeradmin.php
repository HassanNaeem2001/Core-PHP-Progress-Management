<?php
include('sessionadmin.php');
ob_start(); // Start output buffering to prevent header errors
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Admin SAR</title>
        <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <!-- Required meta tags -->
        <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">


        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body>

<nav
        class="navbar navbar-expand-sm navbar-dark bg-dark"
       >
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button
                class="navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId"
                aria-controls="collapsibleNavId"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php" aria-current="page"
                            >Home
                            <span class="visually-hidden">(current)</span></a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="postjobs.php">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="viewcomplaints.php">Complaints</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="dropdownId"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            >Academics</a
                        >
                        <div
                            class="dropdown-menu"
                            aria-labelledby="dropdownId"
                        >
                            <a class="dropdown-item" href="students.php"
                                >Students</a
                            >
                            <a class="dropdown-item" href="batches.php"
                                >Batches</a
                            >
                            <a class="dropdown-item" href="exams.php"
                                >Exams</a
                            >
                            <hr>
                            <a class="dropdown-item" href="progressreports.php"
                                >Add Student Progress</a
                            >
                            <a class="dropdown-item" href="fetchprogressreports.php"
                                >View Student Progress</a
                            >
                            <hr>
                            <a class="dropdown-item" href="monthlysar.php"
                                >Generate Monthly SAR</a
                            >
                            <a class="dropdown-item" href="quarterwise.php"
                                >Quarter Wise SAR</a
                            >
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="dropdownId"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            >Reminders</a
                        >
                        <div
                            class="dropdown-menu"
                            aria-labelledby="dropdownId"
                        >
                            <a class="dropdown-item" href="followups.php"
                                >Send Message</a
                            >
                            
                            
                        </div>
                    </li>
                    <li>
                    <form class="d-flex my-2 my-lg-0 ms-lg-4">
                    <input
                        class="form-control me-sm-2"
                        type="text"
                        placeholder="Search"
                    />
                    <button
                        class="btn btn-secondary my-2 my-sm-0"
                        type="submit"
                    >
                        Search
                    </button>
                </form>
                    </li>
                </ul>
                <form method="post" class="d-flex my-2 my-lg-0">
                <p class="text-white mx-4" style="position:relative;top:8px">Welcome Back , <?php echo $_SESSION['adminloggedin']?></p>
                <button class="btn btn-danger" type="submit" name="btnlogout">Logout</button>
                </form>
            </div>
        </div>
       </nav>
       <?php
if(isset($_POST['btnlogout']))
{
    session_destroy();
    header('location: index.php');
    exit();
}

?>