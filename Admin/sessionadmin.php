<?php
session_start(); // Start the session
if(isset($_SESSION['adminloggedin']))
{
}
else
{
    header('Location:index.php');
    exit(); // It's a good practice to call exit after a redirect
}
?>