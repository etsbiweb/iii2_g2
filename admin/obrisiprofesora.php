<?php 
require_once('../includes/dbh.php');
require_once('../includes/admincheck.php');
$id = $_REQUEST['id'];

$qFindAcc = $conn->prepare('SELECT users.user_id FROM users INNER JOIN profesor ON profesor.user_id = users.user_id AND profesor.profesor_id = :id');
$qFindAcc->bindParam(':id', $id);
$qFindAcc->execute();
$idAcc = $qFindAcc->fetchColumn();

$qFind = $conn->prepare('SELECT * FROM profesor WHERE profesor_id = :id');
$qFind->bindParam(':id', $id);
$qFind->execute();

if($qFind->rowCount() > 0)
{
    $qObrisi = $conn->prepare('DELETE FROM profesor WHERE profesor_id = :id');
    $qObrisi->bindParam(':id', $id);
    $qObrisi->execute();

    $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
    Profesor uspješno obrisan 
    </div>';
}
else
{
    $_SESSION['delete_msg'] = '<div class="alert alert-danger" role="alert">
    Profesor nije pronađen 
    </div>';
}
if(isset($idAcc))
{
    $qObrisi = $conn->prepare('DELETE FROM users WHERE user_id = :id');
    $qObrisi->bindParam(':id', $idAcc);
    $qObrisi ->execute();
}
header('Location: ../index.php');
exit();
?>