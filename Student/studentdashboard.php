<?php
include 'sessionstudent.php';
include('../connect.php');
ob_start();
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Student Dashboard</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../style.css" />
        <!-- Required meta tags -->
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
            <a class="navbar-brand" href="#">
                <img src="../Images/aptlogo.png" height="50px" alt="">
            </a>
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
                        <a class="nav-link active" href="#" aria-current="page"
                            >Home
                            <span class="visually-hidden">(current)</span></a
                        >
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
                            <a class="dropdown-item" href="#"
                                >Over All Progress</a
                            >
                            <a class="dropdown-item" href="#"
                                >Jobs</a
                            >
                            <a class="dropdown-item" href="#"
                                >Attendance</a
                            >
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Feedback</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">Complaints</a>
                    </li>
                    
                </ul>
                <form class="d-flex my-2 my-lg-0" method="post">
                <button type="submit" name="btnlogout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
       </nav>
       
       <div class="bgstudent d-flex justify-content-center align-items-center">
       <div>
       <h3 class="text-white headingfontstudent text-center">Student Dashboard</h3>
       <p class="text-white text-center" style="font-weight:bold"><?php echo $_SESSION['studentname']?></p>
       </div> 
       </div>
       <div class="container-fluid mt-4 text-light min-vh-25 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Table for Large Screens -->
                <div class="d-none d-lg-block">
                    <table class="table table-dark table-striped border border-light shadow-lg">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Enrollment No</th>
                                <th>Batch</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Faculty</th>
                                <th>Current Semester</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
$query = mysqli_query($conn, "
    SELECT 
        batches.batchcode, 
        batches.currentsem, 
        staff.staffname 
    FROM batches 
    LEFT JOIN staff ON batches.batchinstructor = staff.staffid
    WHERE batches.batchid = ".$_SESSION['studentbatch']
);
$data = mysqli_fetch_assoc($query);
?>

<tr>
    <td><?php echo $_SESSION['studentname']; ?></td>
    <td><?php echo $_SESSION['enrollmentno']; ?></td>
    <td><?php echo $data['batchcode']; ?></td>
    <td><?php echo $_SESSION['studentemail']; ?></td>
    <td><?php echo $_SESSION['studentphoneno']; ?></td>
    <td><?php echo $data['staffname']; ?></td>
    <td><?php echo $data['currentsem']; ?></td>
</tr>

                        </tbody>
                    </table>
                </div>

                <!-- Card Layout for Small Screens -->
                <div class="d-lg-none">
                    <div class="card bg-black text-light shadow-lg border-light">
                        <div class="card-body text-center">
                            <h2 class="fw-bold">Welcome, <?php echo $_SESSION['studentname']; ?>! 🎓</h2>
                            <hr class="border-light">
                            <p class="fs-5"><strong>Enrollment No:</strong> <?php echo $_SESSION['enrollmentno']; ?></p>
                            <p class="fs-5"><strong>Batch:</strong> 
                                <?php echo $batchcode['batchcode']; ?>
                            </p>
                            <p class="fs-5"><strong>Email:</strong> <?php echo $_SESSION['studentemail']; ?></p>
                            <p class="fs-5"><strong>Phone:</strong> <?php echo $_SESSION['studentphoneno']; ?></p>
                            <a href="logout.php" class="btn btn-outline-light w-100 mt-3">Logout</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
<?php
if(isset($_POST['btnlogout']))
{
    session_destroy();
    header('Location:../index.php');
}
?>