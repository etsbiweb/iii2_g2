<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");

$qPrvaSmjena = $conn->prepare("SELECT * FROM cas
INNER JOIN profesor_predmet pp ON pp.profesor_predmet_id = cas.profesor_predmet_id 
INNER JOIN profesor p ON p.profesor_id = pp.profesor_id WHERE cas.smjena = 1 AND p.profesor_id = :id");
$qPrvaSmjena->bindParam(":id", $profesor['profesor_id']);
$qPrvaSmjena->execute();
$prvaSmjena = $qPrvaSmjena->fetchAll(PDO::FETCH_ASSOC);

$qDrugaSmjena = $conn->prepare("SELECT * FROM cas 
INNER JOIN profesor_predmet pp ON pp.profesor_predmet_id = cas.profesor_predmet_id 
INNER JOIN profesor p ON p.profesor_id = pp.profesor_id WHERE cas.smjena = 2 AND p.profesor_id = :id");
$qDrugaSmjena->bindParam(":id", $profesor['profesor_id']);
$qDrugaSmjena->execute();
$drugaSmjena = $qDrugaSmjena->fetchAll(PDO::FETCH_ASSOC);

function prikaziCas($id, $conn)
{
    $qPredmet = $conn->prepare("SELECT pred.ime_predmeta, pred.boja
    FROM cas 
    INNER JOIN profesor_predmet pp ON pp.profesor_predmet_id = cas.profesor_predmet_id 
    INNER JOIN predmet pred ON pred.predmet_id = pp.predmet_id 
    WHERE cas.cas_id = :id");
    $qPredmet->bindParam(":id", $id);
    $qPredmet->execute();
    $predmet = $qPredmet->fetch(PDO::FETCH_ASSOC);

    $qRazred = $conn->prepare("SELECT CONCAT(r.godina, r.odjeljene) AS razred, r.razred_id FROM cas INNER JOIN razred r ON r.razred_id = cas.razred_id WHERE cas.cas_id = :id");
    $qRazred->bindParam(":id", $id);
    $qRazred->execute();
    $razred = $qRazred->fetch(PDO::FETCH_ASSOC);

    if($predmet && $razred)
    {
        echo '<div class="cell subject-cell" style="background-color:'.$predmet['boja'].'">
        <a href="kreirajizostanak.php?cas='.$id.'" class="cas">'.$predmet['ime_predmeta'].' - '.$razred['razred'].'</a></div>';
    }
    else
    {
        echo '<div class="cell subject-cell"></div>'; // Prazna ćelija ako nema predmeta
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> 
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kartica.css">
    <link rel="stylesheet" href="../css/raspored.css">
    <script src="../scripts/app.js"></script>
</head>
<body>
<body>
<div class="container-fluid">
    <div class="row">
                <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Dobrodošli, <?php if($qProf->rowCount()>0): echo $profesor['ime_prezime']; endif; ?>!</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <div class="dropdown-container">
                    <a href="#" class="dropdown-toggle"><i class="bi bi-book me-2"></i>Moj razred</a>
                    <ul class="dropdown-menu">
                        <li class="has-submenu"><a href="listaucenika.php">Lista učenika</a></li>
                        <li class="has-submenu"><a href="rasporeducenika.php">Raspored časova</a></li>
                        <li class="has-submenu"><a href="noviizostanci.php">Novi izostanci</a></li>
                    </ul>
                </div>
                <a href="mojraspored.php" class="active"><i class="bi bi-calendar-week me-2"></i>Moj raspored</a>
                <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
                </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="naziv-razreda">Raspored Časova</div>
        <div class="main-wrapper">
        <div class="raspored-container">
            <div class="header-row">
                <div class="cell hour-label"></div>
                <div class="cell day-header">Ponedjeljak</div>
                <div class="cell day-header">Utorak</div>
                <div class="cell day-header">Srijeda</div>
                <div class="cell day-header">Četvrtak</div>
                <div class="cell day-header">Petak</div>
            </div>
            <?php for($i = 1; $i < 8; $i++)
            {
                echo '<div class="row-content">';
                echo '<div class="cell hour-label">'.$i.'. čas</div>';
                for ($j = 0; $j < 5; $j++)
                {
                    $dan = date('l', strtotime('next Monday + '.$j.' days'));
                    $odabraniCas = null;
                    if(isset($prvaSmjena))
                    {
                        foreach($prvaSmjena as $cas)
                        {
                            if($cas['dan'] == $dan && $cas['redni_broj'] == $i)
                            {
                                $odabraniCas = $cas;
                                break;
                            }
                        }
                    }
                    if($odabraniCas)
                    {
                        prikaziCas($odabraniCas['cas_id'], $conn);
                    }
                    if(!$odabraniCas)
                    {
                        echo '<div class="cell subject-cell"></div>';
                    }
                }
                echo '</div>';
            }?>
                
            <div class="smjena-container">
                DRUGA SMJENA
            </div>
            <?php for($i = 1; $i < 8; $i++)
            {
                echo '<div class="row-content">';
                echo '<div class="cell hour-label">'.$i.'. čas</div>';
                for ($j = 0; $j < 5; $j++)
                {
                    $dan = date('l', strtotime('next Monday + '.$j.' days'));
                    $odabraniCas = null;
                    if(isset($drugaSmjena))
                    {
                        foreach($drugaSmjena as $cas)
                        {
                            if($cas['dan'] == $dan && $cas['redni_broj'] == $i)
                            {
                                $odabraniCas = $cas;
                                break;
                            }
                        }
                    }
                    if($odabraniCas)
                    {
                        prikaziCas($odabraniCas['cas_id'], $conn);
                    }
                    if(!$odabraniCas)
                    {
                        echo '<div class="cell subject-cell"></div>';
                    }
                }
                echo '</div>';
            }?>
        </div>
    </div>
    <?php if(isset($_SESSION["izostanak_message"]))
                    {
                        echo $_SESSION['izostanak_message'];
                        unset( $_SESSION['izostanak_message'] ); 
                    }?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>