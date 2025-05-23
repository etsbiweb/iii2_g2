<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
require_once("../includes/admincheck.php");

$id = $_REQUEST['id'];
if(!isset($id))
{
    header('Location: dashboard.php');
}
$qFindRaz = $conn->prepare("SELECT * FROM razred WHERE razred_id = :id");
$qFindRaz->bindParam(":id", $id);
$qFindRaz->execute();
$r = $qFindRaz->fetch(PDO::FETCH_ASSOC);

$qCasovi = $conn->prepare("SELECT * FROM cas WHERE cas.razred_id = :id");
$qCasovi->bindParam(":id", $id);
$qCasovi->execute();
$casovi = $qCasovi->fetchAll(PDO::FETCH_ASSOC);

function dohvatiPredmet($id, $conn)
{
    $qPredmet = $conn->prepare("
        SELECT pred.ime_predmeta, pred.boja 
        FROM cas 
        INNER JOIN profesor_predmet pp ON pp.profesor_predmet_id = cas.profesor_predmet_id 
        INNER JOIN predmet pred ON pred.predmet_id = pp.predmet_id 
        WHERE cas.cas_id = :id
    ");
    $qPredmet->bindParam(":id", $id);
    $qPredmet->execute();
    $predmet = $qPredmet->fetch(PDO::FETCH_ASSOC);

    $qProfesor = $conn->prepare("
        SELECT prof.ime_prezime 
        FROM profesor_predmet pp
        INNER JOIN profesor prof ON prof.profesor_id = pp.profesor_id
        INNER JOIN cas c ON c.profesor_predmet_id = pp.profesor_predmet_id
        WHERE c.cas_id = :id
    ");
    $qProfesor->bindParam(":id", $id);
    $qProfesor->execute();
    $profesor = $qProfesor->fetch(PDO::FETCH_ASSOC);

    if($profesor && $predmet)
    {
        echo '<div class="cell subject-cell" style="background-color:'.$predmet['boja'].'">
        <a href="editcas.php?id='.$id.'">'.$predmet['ime_predmeta'].' - '.$profesor['ime_prezime'].'</a></div>';
    }
    else
    {
        echo '<div class="cell subject-cell"> Nepoznat Predmet </div>';
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
</head>
<body>
<body>
<div class="container-fluid">
    <div class="row">
    <nav class="col-md-2 sidebar">
            <h5 class="px-3 fs-3 my-3">Admin panel</h5>
            <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="prikaziprofesore.php"><i class="bi bi-person-badge me-2"></i>Profesori</a>
            <div class="dropdown-container">
                <a href="#"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($razredi as $razred)
                    { ?>
                        <li class="has-submenu">
                        <a href="#"><?php echo $razred['godina']; ?></a>
                        <ul class="dropdown-submenu">
                        <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>       
                        <?php
                        foreach ($odjeljenja as $odjeljenje)
                        { ?>
                        <li><a href="prikaziucenike.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                        <?php 
                        }  
                    ?>
                               
                        </ul>
                        </li>
                    <?php 
                    } ?>
                </ul>
            </div>
            <a href="prikazipredmete.php"><i class="bi bi-book me-2"></i>Predmeti</a>
            <div class="dropdown-container">
                <a href="#" class="active"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($razredi as $razred)
                    { ?>
                        <li class="has-submenu">
                        <a href="#"><?php echo $razred['godina']; ?></a>
                        <ul class="dropdown-submenu">
                        <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>       
                        <?php
                        foreach ($odjeljenja as $odjeljenje)
                        { ?>
                        <li><a href="prikaziraspored.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                        <?php 
                        } ?>   
                        </ul>
                        </li>
                    <?php 
                    } ?>
                </ul>
            </div>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="naziv-razreda"><?php echo $r['godina']; echo $r['odjeljene'];?></div>
            <div class="main-wrapper">
        <div class="raspored-container">
            <div class="header-row">
                <div class="cell hour-label"></div>
                <?php
                for($i = 0; $i < 5; $i++)
                {
                    $dan = date('l',  strtotime('next Monday + '.$i.' days'));
                    $prevodDana = [
                        "Monday" => 'Ponedjeljak',
                        "Tuesday" => "Utorak",
                        "Wednesday" => "Srijeda",
                        "Thursday" => "Četvrtak",
                        "Friday" => "Petak"
                    ];
                 ?>
                <div class="cell day-header"><?php echo $prevodDana[$dan]; ?></div>
                <?php 
                }?>
            </div>

            <?php for($i = 1; $i < 8; $i++)
            {
                echo '<div class="row-content">';
                echo '<div class="cell hour-label">'.$i.'. čas</div>';                
                for($j = 0; $j < 5; $j++)
                {
                    $dan = date('l', strtotime('next Monday +'.$j.' days'));
                    $odabraniCas = null;
                    if(isset($casovi))
                    {               
                        foreach($casovi as $cas)
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
                        dohvatiPredmet($odabraniCas['cas_id'], $conn);
                    }
                    if(!$odabraniCas)
                    {
                        echo '<div class="cell subject-cell dodaj-cas"><a href="kreirajcas.php?r='.$id.'&dan='.$dan.'&br='.$i.'" class="dodaj-cas">+</a></div>';
                    }
                    
                }
                echo '</div>';
            }
                
            ?>
            </div>  
        </div>
    </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>