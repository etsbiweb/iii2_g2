<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");

$qUcenici = $conn->prepare("SELECT count(*) FROM ucenici;");
$qUcenici->execute();
$ucenici = $qUcenici->fetchColumn();

$qProfesori = $conn->prepare("SELECT count(*) FROM profesor;");
$qProfesori->execute();
$profesori = $qProfesori->fetchColumn();

$qIzostanci = $conn->prepare("SELECT count(*) FROM izostanci WHERE DATE(vrijeme) = CURDATE()");
$qIzostanci->execute();
$izostanci = $qIzostanci->fetchColumn();

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
            <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="#"><i class="bi bi-person-badge me-2"></i>Profesori</a>
            <div class="dropdown-container">
                <a href="#"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                <ul class="dropdown-menu">
                    <?php foreach ($razredi as $razred): ?>
                        <li class="has-submenu">
                            <a href="#"><?php echo $razred['godina']; ?></a>
                            <ul class="dropdown-submenu">
                                <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>
                                <?php if (!empty($odjeljenja)): ?>
                                    <?php foreach ($odjeljenja as $odjeljenje): ?>
                                        <li><a href="prikaziucenike.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a href="#">Nema odjeljenja</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="#"><i class="bi bi-book me-2"></i>Predmeti</a>
            <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
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
                        <h6>Izostanci danas</h6>
                        <h3><?php echo $izostanci; ?></h3>
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
                        <h5 class="mb-3">Raspored za danas</h5>
                        <ul class="list-unstyled mb-0">
                            <li>08:00 – 08:45 <strong>Matematika V–1</strong></li>
                            <li>08:55 – 09:40 <strong>Engleski jezik IV–3</strong></li>
                            <li>09:50 – 10:35 <strong>Hemija III–2</strong></li>
                            <li>10:45 – 11:30 <strong>Istorija VII–4</strong></li>
                            <li>11:40 – 12:25 <strong>Fizika VI–1</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>