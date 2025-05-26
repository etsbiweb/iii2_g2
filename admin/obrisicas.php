<?php
require_once('../includes/dbh.php');
require_once('../includes/admincheck.php');
$id = $_REQUEST['id'];
$qFind = $conn->prepare('SELECT * FROM cas WHERE cas_id = :id');
$qFind->bindParam(':id', $id);
$qFind->execute();
if($qFind->rowCount() > 0)
{
    $qObrisi = $conn->prepare('DELETE FROM cas WHERE cas_id = :id');
    $qObrisi->bindParam(':id', $id);
    $qObrisi->execute();
    $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
    Čas uspješno obrisan
    </div>';
    header('Location: dashboard.php');
    exit();
}
else
{
    $_SESSION['delete_msg'] = '<div class="alert alert-danger" role="alert">
    Čas s tim ID-em ne postoji 
    </div>';
    header('Location: dashboard.php');
    exit();
}

?>