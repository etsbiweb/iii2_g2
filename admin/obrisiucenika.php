<?php

require_once("../includes/dbh.php");
require_once("../includes/admincheck.php");


$id = $_REQUEST['id'];
$qFind = $conn->prepare('SELECT * FROM izostanci WHERE ucenik_id = :id');
$qFind->bindParam(':id', $id);
$qFind->execute();

if($qFind->rowCount() > 0)
{
    $qObrisi = $conn->prepare('DELETE FROM izostanci WHERE ucenik_id = :id');
    $qObrisi->bindParam(':id', $id);
    $qObrisi->execute();
}

$qFindAcc = $conn->prepare('SELECT users.user_id FROM users INNER JOIN ucenici ON ucenici.user_id = users.user_id AND ucenici.ucenik_id = :id');
$qFindAcc->bindParam(':id', $id);
$qFindAcc->execute();
$qIdAcc = $qFindAcc->fetchColumn();  

$qFind = $conn->prepare('SELECT * FROM ucenici WHERE ucenik_id = :id');
$qFind->bindParam(':id', $id);
$qFind->execute();

if($qFind->rowCount() > 0)
{
    $qObrisi = $conn->prepare('DELETE FROM ucenici WHERE ucenik_id = :id');
    $qObrisi->bindParam(':id', $id);
    $qObrisi->execute();

    $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
    Učenik uspješno obrisan 
    </div>';
}
else
{
    $_SESSION['delete_msg']='<div class="alert alert-danger" role="alert">
    Učenik nije pronađen 
    </div>';
}

if(isset($qFindAcc))
{
    $qObrisi = $conn->prepare('DELETE FROM users WHERE user_id = :id');
    $qObrisi->bindParam(':id', $qIdAcc);
    $qObrisi->execute();
    header("Location: ../index.php");
    exit();
}
?>