<?php
require_once("../includes/dbh.php");
require_once("../includes/razredi.php");
require_once("../includes/admincheck.php");
$id = $_REQUEST['r'];
$dan = $_REQUEST['dan'];
$br = $_REQUEST['br'];

$qProfPred = $conn->prepare('SELECT prof.ime_prezime, pred.ime_predmeta, pred.boja, pp.profesor_predmet_id FROM profesor_predmet pp
INNER JOIN profesor prof ON pp.profesor_id = prof.profesor_id INNER JOIN predmet pred ON pp.predmet_id = pred.predmet_id');
$qProfPred->execute();
$profPred = $qProfPred->fetchAll(PDO::FETCH_ASSOC);

$qRazred = $conn->prepare('SELECT * FROM razred WHERE razred_id = :id');
$qRazred->bindParam(':id', $id);
$qRazred->execute();
$r = $qRazred->fetch(PDO::FETCH_ASSOC);
if($r['godina'] == 'I' || $r['godina'] == 'III')
{
    $smjena = 1;
}
else
{
    $smjena = 2;
}

if(isset($_POST['submit']))
{
    $predmet = $_POST['predmet'];
    $qProf = $conn->prepare('SELECT profesor.profesor_id FROM profesor, profesor_predmet
    WHERE profesor.profesor_id = profesor_predmet.profesor_id AND profesor_predmet.profesor_predmet_id = :id');
    $qProf->bindParam(':id', $predmet);
    $qProf->execute();
    $profesor = $qProf->fetchColumn();

    $qZauzet = $conn->prepare('SELECT cas.redni_broj, cas.dan FROM profesor prof
    INNER JOIN profesor_predmet pp ON pp.profesor_id = prof.profesor_id
    INNER JOIN cas ON pp.profesor_predmet_id = cas.profesor_predmet_id WHERE prof.profesor_id = :id');
    $qZauzet->bindParam(':id', $profesor);
    $qZauzet->execute();
    $zauzet = $qZauzet->fetchAll(PDO::FETCH_ASSOC);
    foreach($zauzet as $z)
    {
        if($z['dan']  == $dan && $z['redni_broj'] == $br)
        {
            $_SESSION['delete_msg'] = '<div class="alert alert-danger" role="alert">
            Profesor je zauzet u to vrijeme!
            </div>';
            header('Location: dashboard.php');
            exit();
        }
    }
    $qDodaj = $conn->prepare('INSERT INTO `cas`(`razred_id`, `redni_broj`, `smjena`, `profesor_predmet_id`, `dan`) VALUES (:id, :br, :smjena, :predmet, :dan)');
    $qDodaj->bindParam(':id', $id);
    $qDodaj->bindParam(':br', $br);
    $qDodaj->bindParam(':smjena', $smjena);
    $qDodaj->bindParam(':predmet', $predmet);
    $qDodaj->bindParam(':dan', $dan);
    $qDodaj->execute();
    $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
    Čas uspješno kreiran 
    </div>';
    header('Location: dashboard.php');
    exit();
}

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
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
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
                    <a href="#" class="active dropdown-toggle"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
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
                <h3 class="text-align-center">Kreiranje časa</h3>
                <form action="" method="post">
                    <div class="mt-3">
                        <label for="predmet">Predmet:</label>
                        <select name="predmet" id="predmet">
                        <?php foreach($profPred as $info)
                        {
                            echo '<option value="'.$info['profesor_predmet_id'].'" style="background-color: '.$info['boja'].'">'.$info['ime_predmeta'].' - '.$info['ime_prezime'].'</option>';
                        } ?>
                        </select>
                    </div>
                    <div class="mt-3">
                        <input type="submit" name="submit" class="btn btn-primary" value="Spremi">
                    </div>
                </form>
                    </div>
                </form>
                <?php if(isset($message)): echo $message; endif; ?>
            </main>
        </div>
    </div>
</body>