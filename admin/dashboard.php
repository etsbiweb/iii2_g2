<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
include_once("../includes/admincheck.php");

$qUcenici = $conn->prepare("SELECT count(*) FROM ucenici;");
$qUcenici->execute();
$ucenici = $qUcenici->fetchColumn();

$qProfesori = $conn->prepare("SELECT count(*) FROM profesor;");
$qProfesori->execute();
$profesori = $qProfesori->fetchColumn();

$qIzostanciMj = $conn->prepare("SELECT count(*) FROM izostanci WHERE MONTH(vrijeme) = MONTH(CURDATE())");
$qIzostanciMj->execute();
$izostanciMj = $qIzostanciMj->fetchColumn();

$qIzostanciDan = $conn->prepare("SELECT * FROM izostanci WHERE date(vrijeme) = CURRENT_DATE");
$qIzostanciDan->execute();
$izostanciDan = $qIzostanciDan->fetchAll(PDO::FETCH_ASSOC);

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
    <script src="../scripts/app.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar">
            <h5 class="px-3 fs-3 my-3">Admin panel</h5>
            <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="prikaziprofesore.php"><i class="bi bi-person-badge me-2"></i>Profesori</a>
            <div class="dropdown-container">
                <a href="#" class="dropdown-toggle"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($razredi as $razred) { ?>
                        <li class="has-submenu">
                            <a href="#" class="dropdown-toggle"><?php echo $razred['godina']; ?></a>
                            <ul class="dropdown-submenu">
                            <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>      
                            <?php
                            foreach ($odjeljenja as $odjeljenje) { ?>
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
                <a href="#" class="dropdown-toggle"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($razredi as $razred) { ?>
                        <li class="has-submenu">
                            <a href="#" class="dropdown-toggle"><?php echo $razred['godina']; ?></a>
                            <ul class="dropdown-submenu">
                            <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>      
                            <?php
                            foreach ($odjeljenja as $odjeljenje) { ?>
                                <li><a href="prikaziraspored.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                            <?php 
                            } ?>  
                            </ul>
                        </li>
                    <?php 
                    } ?>
                </ul>
            </div>
            <a href="izostanci.php"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
        </nav>

        <main class="col-md-10 content">
            <h2 class="mb-4">Početna</h2>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card text-center p-3">
                        <i class="bi bi-mortarboard card-icon mb-2"></i>
                        <h6>Učenici</h6>
                        <h3><?php echo $ucenici; ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center p-3">
                        <i class="bi bi-person-circle card-icon mb-2"></i>
                        <h6>Profesori</h6>
                        <h3><?php echo $profesori; ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center p-3">
                        <i class="bi bi-exclamation-triangle card-icon mb-2"></i>
                        <h6>Izostanci ovaj mjesec</h6>
                        <h3><?php echo $izostanciMj; ?></h3>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="mb-3">Nedavne aktivnosti:</h5>
                        <ul class="mb-0">
                            <?php
                            $queryLog = $conn->prepare("SELECT * FROM `log` ORDER BY `log_id` DESC");
                            $queryLog->execute();
                            $logs = $queryLog->fetchAll(PDO::FETCH_ASSOC);

                            if(!empty($logs))
                            {
                                foreach($logs as $log)
                                {
                                    echo '
                                        <li>'.$log['log_text'].'</li>
                                    ';
                                }
                            }
                            else{
                                echo 'Nema aktivnosti';
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="mb-3">Izostanci danas</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($izostanciDan as $iz)
                            {
                                $qSelectUcenik = $conn->prepare("SELECT * FROM ucenici INNER JOIN izostanci iz ON iz.ucenik_id = ucenici.ucenik_id
                                WHERE iz.izostanak_id = :id");
                                $qSelectUcenik->bindParam(':id', $iz['izostanak_id']);
                                $qSelectUcenik->execute();
                                $ucenik = $qSelectUcenik->fetch(PDO::FETCH_ASSOC);
                                echo '<li>'.$ucenik["ime"].' '.$ucenik["prezime"].'
                                <strong>'.date('H:i:s', strtotime($iz['vrijeme'])).'</strong> Profesor: '.$iz['ime_profesora'].'</li>';
                            }?>
                        </ul>
                    </div>
                </div>
                <?php
                if(isset($_SESSION['delete_msg']))
                {
                    echo $_SESSION['delete_msg'];
                    unset($_SESSION['delete_msg']);
                }
                 ?>
            </div>
        </main>
    </div>
</div>

</body>
</html>