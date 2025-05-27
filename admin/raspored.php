<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
require_once("../includes/admincheck.php");

$id = $_REQUEST['id'];
$qRazred = $conn->prepare("SELECT * FROM razred WHERE razred_id = :id");
$qRazred->bindParam(':id', $id);
$qRazred->execute();
$r = $qRazred->fetch(PDO::FETCH_ASSOC);

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
                <a href="#"> <i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
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
                        <li><a href="raspored.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                        <?php 
                        }  
                    ?>   
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-raspored">
                    <tbody>
                        <?php for($i = 1; $i < 6; $i++)
                        {
                            echo '<tr>';
                            echo '<td>';
                            for ($j = 1; $j <= 1; $j++)
                            {
                                                            // strtotime('next Sunday') će ti dati timestamp sljedeće nedjelje.
                                // Dodajemo $i dana na taj timestamp.
                                $timestamp = strtotime('next Sunday +' . $i . ' days');

                                // date('l', $timestamp) će ti dati engleski naziv dana (npr. "Sunday", "Monday")
                                $dan_engleski = date('l', $timestamp);

                                // Provjeravamo da li postoji prevod za dobijeni dan
                                if (isset($dani_prevod[$dan_engleski])) {
                                    echo $dani_prevod[$dan_engleski] . "<br>"; // Ispisujemo prevedeni dan i prekidamo liniju
                                } else {
                                    echo $dan_engleski . "<br>";
                                }
                            }
                        }?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>