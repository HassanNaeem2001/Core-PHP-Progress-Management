<?php
include('staffsessionlogic.php');
?>
<!doctype html>
<html lang="en">
    <head>
        <title></title>
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
        <header>

            <!-- place navbar here -->
             <nav
                class="navbar navbar-expand-sm navbar-dark bg-dark"
             >
                <a class="navbar-brand ms-2 " href="staffdashboard.php">Staff Portal</a>
                <button
                    class="navbar-toggler d-lg-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavId"
                    aria-controls="collapsibleNavId"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                ></button>
                <div class="collapse navbar-collapse" id="collapsibleNavId">
                    <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active " href="staffdashboard.php" aria-current="page"
                                >Home <span class="visually-hidden"></span></a
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
                                >Students</a
                            >
                            <div class="dropdown-menu" aria-labelledby="dropdownId">
                                <a class="dropdown-item" href="progressreports.php">Add Progress</a>
                                <a class="dropdown-item" href="fetchprogressreports.php">View Progress</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="uploadmaterial.php" class="nav-link">Upload Material</a>
                        </li>
                        <li class="nav-item">
                            <a href="uploadassignment.php" class="nav-link">Upload Assignment</a>
                        </li>
                    </ul>
                    <form class="d-flex my-2 my-lg-0" method="post">
                       
                        <button name="btnlogout" class="btn btn-danger my-2 my-sm-0" type="submit">
                            Logout
                        </button>
                    </form>
                </div>
             </nav>
             
        </header>
        <main>


        </main>
        <?php
        if(isset($_POST['btnlogout']))
        {
            session_destroy();
            header('index.php');
        }
        
        ?>
       