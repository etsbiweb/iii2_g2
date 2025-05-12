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

$qFind = $conn->prepare('SELECT * FROM users INNER JOIN ucenici ON ucenici.user_id = users.user_id AND ucenici.ucenik_id = :id');
$qFind->bindParam(':id', $id);
$qFind->execute();
$qAccount = $qFind->fetchAll(PDO::FETCH_ASSOC);

if($qFind->rowCount() > 0)
{
$qObrisi = $conn->prepare('DELETE FROM users WHERE user_id = :id');
$qObrisi->bindParam(':id', $qAccount['id']);
$qObrisi->execute();
}

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
header("Location: ../index.php");
}
else
{
$_SESSION['delete_msg']='<div class="alert alert-danger" role="alert">
Učenik nije pronađen
</div>';
header("Location: ../index.php");
}
?>