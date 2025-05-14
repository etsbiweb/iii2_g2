<?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/log.php");
    require_once("../includes/razredi.php");

    
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kartica.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Admin panel</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <a href="prikaziprofesore.php" class="active"><i class="bi bi-person-badge me-2"></i>Profesori</a>
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
                <a href="#"><i class="bi bi-book me-2"></i>Predmeti</a>
                <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
                <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
                <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
            </nav>

            <main class="col-md-10 content">
                <h3 class="text-align-center">Lista profesora</h3>
                
                <div class="ucenici-grid mt-3">
                    <?php
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        
                        $profesorQuery = $conn->prepare("SELECT * FROM `profesor`");

                        $profesorQuery->execute();
                        $profesori = $profesorQuery->fetchAll(PDO::FETCH_ASSOC);
                        
                        if(!empty($profesori)){
                            foreach($profesori as $profesor){
                                echo '
                                    <div class="kartica-ucenik">
                                        <div class="ime-prezime-ucenik">
                                            '.$profesor['ime_prezime'].' 
                                        </div>
                                        <div class="gumbi-ucenik">
                                            <a class="gumb-izbrisi-ucenik" style="text-decoration: none;" href="obrisiprofesora.php?id='.$profesor['profesor_id'].'">Izbriši</a>
                                            <a class="gumb-uredi-ucenik" style="text-decoration: none;" href="editprofesor.php?id='.$profesor['profesor_id'].'">Uredi</a>
                                        </div>
                                    </div>
                                ';
                            }
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>
</body>