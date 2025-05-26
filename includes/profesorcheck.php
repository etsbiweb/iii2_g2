<?php
include_once("dbh.php");
if(isset($_SESSION['logged']))
{
   $qCheck = $conn->prepare("SELECT pristup FROM users WHERE user_id = :id");
   $qCheck->bindParam(":id", $_SESSION['id']);
   $qCheck->execute();
   $role = $qCheck->fetchColumn();
   
   if($role != 'admin' && $role !='profesor')
   {
      $_SESSION['access_error']='<div class="alert alert-danger" role="alert">
      Nemate ovlaštenje za pristup ovoj stranici
      </div>';
      header("Location: ../login.php");
      exit();
   }
}
else
{
   $_SESSION['access_error']='<div class="alert alert-danger" role="alert">
   Morate biti ulogovani da biste pristupili ovoj stranici
   </div>';
   header("Location: ../login.php");
   exit();
}?>