<?php
session_start();
if(isset($_SESSION['staffsession']))
{
    
}
else
{
    header('Location:index.php');
}
?>