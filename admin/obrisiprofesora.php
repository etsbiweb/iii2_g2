<?php 
require_once('../includes/dbh.php');
require_once('../includes/admincheck.php');
$id = $_REQUEST['id'];

$qFindAcc = $conn->prepare('SELECT users.user_id FROM users INNER JOIN profesor ON profesor.user_id = users.user_id AND profesor.profesor_id = :id');
$qFindAcc->bindParam(':id', $id);
$qFindAcc->execute();
$idAcc = $qFindAcc->fetchColumn();

$qFindPredmet = $conn->prepare('SELECT * FROM profesor_predmet pp WHERE pp.profesor_id = :id');
$qFindPredmet->bindParam(':id', $id);
$qFindPredmet->execute();
$predmeti = $qFindPredmet->fetchAll(PDO::FETCH_ASSOC);

if(isset($predmeti))
{
    foreach($predmeti as $predmet)
    {
        $qFindCasovi = $conn->prepare('SELECT * FROM cas WHERE cas.profesor_predmet_id = :id');
        $qFindCasovi->bindParam(':id', $predmet['profesor_predmet_id']);
        $qFindCasovi->execute();
        if($qFindCasovi->rowCount() > 0)
        {
            $qDelete = $conn->prepare('DELETE FROM cas WHERE cas.profesor_predmet_id = :id');
            $qDelete->bindParam(':id', $predmet['profesor_predmet_id']);
            $qDelete->execute();
        }
    }
}

$qObrisi = $conn->prepare('DELETE FROM profesor_predmet WHERE profesor_predmet.profesor_id = :id');
$qObrisi->bindParam(':id', $id);
$qObrisi->execute();

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