<?php
session_start();
if(isset($_SESSION['studentloggedin']))
{

}
else
{
    header('Location: ../index.php');
}
?>