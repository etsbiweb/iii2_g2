<?php 
require_once('../includes/dbh.php');
require_once('../includes/admincheck.php');
$id = $_REQUEST['id'];

$qFind = $conn ->prepare('SELECT * FROM profesor_predmet WHERE predmet_id = :id');
$qFind->bindParam(':id',$id);
$qFind->execute();

if ($qFind->rowCount() > 0)
{
    $qDelete = $conn->prepare('DELETE FROM profesor_predmet WHERE predmet_id = :id');
    $qDelete->bindParam(':id',$id);
    $qDelete->execute();
}

$qFind = $conn->prepare('SELECT * FROM predmet WHERE predmet_id = :id');
$qFind->bindParam(':id',$id);
$qFind->execute();

if($qFind->rowCount() > 0)
{
    $qDelete = $conn->prepare('DELETE FROM predmet WHERE predmet_id = :id');
    $qDelete->bindParam(':id',$id);
    $qDelete->execute();
    $_SESSION['delete_msg'] = '<div class="alert alert-success my-3" role="alert">
    Predmet uspješno obrisan
    </div>';
}
else
{
    
    $_SESSION['delete_msg'] = '<div class="alert alert-danger my-3" role="alert">
    Predmet nije pronađen
    </div>';
}

//TREBA NAPRAVITI I ZA BRISANJE CASOVA GDJE SE PREDMET ODRZAVA MEDJUTIM TRENUTNA VERZIJA BAZE CE VJEROVATNO BITI MIJENJANJA PA CE SE TO RJESAVATI POSLIJE

header('Location: ../index.php');
exit();
?>