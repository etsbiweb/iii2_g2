<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
require_once("../includes/admincheck.php");

$id = $_REQUEST['id'];

$qFindRaz = $conn->prepare("SELECT godina, odjeljene FROM razred WHERE razred_id = :id");
$qFindRaz->bindParam(":id", $id);
$qFindRaz->execute();
$qRaz = $qFindRaz->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="#" class="active"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
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
            <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php
            foreach($qRaz as $r)
            {
                 
            ?>
            <div class="naziv-razreda"><?php echo $r['godina']; echo $r['odjeljene'];?></div>
            <?php 
            } ?>
            <div class="ucenici-grid mt-3">
                <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    
                  
                    $ucenikQuery = $conn->prepare("SELECT * FROM `ucenici` WHERE razred_id = :razred_id");
                    $ucenikQuery->bindParam(":razred_id", $id);
                    $ucenikQuery->execute();
                    $ucenici = $ucenikQuery->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(!empty($ucenici)){
                        foreach($ucenici as $ucenik)
                        {
                            echo '
                                <div class="kartica-ucenik">
                                    <div class="ime-prezime-ucenik">
                                        '.$ucenik['ucenik_id'].' 
                                        '.$ucenik['ime_prezime'].'<br>
                                        Opravdani: '.$ucenik['opravdani'].' <br>
                                        Neopravdani: '.$ucenik['neopravdani'].' <br>
                                    </div>
                                    <div class="gumbi-ucenik">
                                        <a class="gumb-izbrisi-ucenik" style="text-decoration: none;" href="obrisiucenika.php?id='.$ucenik['ucenik_id'].'">Izbriši</a>
                                        <a class="gumb-uredi-ucenik" style="text-decoration: none;" href="editucenik.php?id='.$ucenik['ucenik_id'].'">Uredi</a>
                                    </div>
                                </div>
                            ';       
                        }
                    }
                ?>
                <div class="kartica-ucenik dodaj">
                    <a href="dodajucenika.php?id=<?php echo $id;?>" class="dodaj">+</a>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>