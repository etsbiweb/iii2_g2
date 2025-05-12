<?php
include_once("dbh.php");
session_start();
if(isset($_SESSION['logged']))
{
   $qCheck = $conn->prepare("SELECT pristup FROM users WHERE user_id = :id");
   $qCheck->bindParam(":id", $_SESSION['id']);
   $qCheck->execute();
   $role = $qCheck->fetchColumn();
   
   if($role != 'admin')
   {
      $_SESSION['access_error']='<div class="alert alert-danger" role="alert">
      Nemate ovlastenje za pristup ovoj stranici '.$_SESSION['id'].'
      </div>';
      header("Location: ../index.php");
      exit();
   }
}
else
{
   header("Location: ../index.php");
   exit();
}
 ?>