<?php
require_once("../includes/dbh.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");

$casId = $_REQUEST['cas'];
$qcas = $conn->prepare('SELECT * FROM cas WHERE cas_id = :id');
$qcas->bindParam(':id', $casId);
$qcas->execute();
$cas = $qcas->fetch(PDO::FETCH_ASSOC);

$qUcenici = $conn->prepare('SELECT * FROM ucenici WHERE razred_id = :id ORDER BY prezime, ime');
$qUcenici->bindParam(':id', $cas['razred_id']);
$qUcenici->execute();
$ucenici = $qUcenici->fetchAll(PDO::FETCH_ASSOC);
$br = 0;
$pocetakSedmice = date('y-m-d', strtotime('Monday this week'));
$krajSedmice = date('y-m-d', strtotime('Sunday this week'));

function nadjiIzostanak($ucenikId, $cas, $conn, $pocetakSedmice, $krajSedmice)
{
    $qFind = $conn->prepare('SELECT * FROM izostanci WHERE ucenik_id = :ucenikId AND redni_broj_casa = :brojCasa AND date(vrijeme) BETWEEN :pocetak AND :kraj');
    $qFind->bindParam(':ucenikId', $ucenikId);
    $qFind->bindParam('brojCasa', $cas['redni_broj']);
    $qFind->bindParam(':pocetak', $pocetakSedmice);
    $qFind->bindParam(':kraj', $krajSedmice);
    $qFind->execute();
    if($qFind->rowCount() > 0)
    {
        $checked = 'checked';
    }
    else
    {
        $checked = '';
    }
    return $checked;
}

if($cas['dan'] != date('l',strtotime('next Sunday + '.date('w').' days')))
{
    $_SESSION['izostanak_message']= '<div class="alert alert-danger" role="alert">
    Ne možete upisivati izostanke za časove koji se ne održavaju danas!
    </div>';
    header('Location: mojraspored.php');
}
else
{
    if(isset($_POST['submit']))
    {
        $prisustva = $_POST['prisustvo'];
        foreach($prisustva as $ucenikId => $status)
        {
            $qFind = $conn->prepare('SELECT * FROM izostanci WHERE ucenik_id = :ucenikId AND redni_broj_casa = :brojCasa AND date(vrijeme) = CURRENT_DATE');
            $qFind->bindParam(':ucenikId', $ucenikId);
            $qFind->bindParam(':brojCasa', $cas['redni_broj']);
            $qFind->execute();
            if($status == 'odsutan')
            {
                if($qFind->rowCount() == 0)
                {
                    $qPredmet = $conn->prepare('SELECT predmet.ime_predmeta FROM predmet INNER JOIN profesor_predmet pp ON pp.predmet_id = predmet.predmet_id
                    INNER JOIN cas ON cas.profesor_predmet_id = pp.profesor_predmet_id WHERE cas.cas_id = :id');
                    $qPredmet->bindParam(':id', $cas['cas_id']);
                    $qPredmet->execute();
                    $predmet = $qPredmet->fetchColumn();

                    $qInsert = $conn->prepare('INSERT INTO `izostanci`(`ucenik_id`, `ime_predmeta`, `redni_broj_casa`, `ime_profesora`)
                    VALUES (:ucenikId, :ime_predmeta, :redni_broj, :ime_profesora)');
                    $qInsert->bindParam(':ucenikId', $ucenikId);
                    $qInsert->bindParam(':ime_predmeta', $predmet);
                    $qInsert->bindParam(':redni_broj', $cas['redni_broj']);
                    $qInsert->bindParam(':ime_profesora', $profesor['ime_prezime']);
                    $qInsert->execute();
                    $_SESSION['izostanak_message']= '<div class="alert alert-success" role="alert">
                    Izostanci uspješno upisani
                    </div>';
                }
            }
            else if ($status == 'prisutan')
            {
                if($qFind->rowCount() > 0)
                {
                    $qDelete = $conn->prepare('DELETE FROM izostanci WHERE ucenik_id = :ucenikId AND redni_broj_casa = :redniBroj AND date(vrijeme) = CURRENT_DATE');
                    $qDelete->bindParam(':ucenikId', $ucenikId);
                    $qDelete->bindParam(':redniBroj', $cas['redni_broj']);
                    $qDelete->execute();
                    $_SESSION['izostanak_message']= '<div class="alert alert-success" role="alert">
                    Izostanci uspješno upisani
                    </div>';
                }
            }
        }
        header('Location: mojraspored.php');
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/dashboard.css">
        <link rel="stylesheet" href="../css/kartica.css">
    </head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Dobrodošli, <?php if($qProf->rowCount()>0): echo $profesor['ime_prezime']; endif; ?>!</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <a href="#"><i class="bi bi-book me-2"></i>Moj razred</a>
                <a href="mojraspored.php" class="active"><i class="bi bi-calendar-week me-2"></i>Moj raspored</a>
                <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>    
                </nav>
                <main class="col-md-10 content">
                <?php 
                    if(isset($_SESSION["access_error"]))
                    {
                        echo $_SESSION['access_error'];
                        unset( $_SESSION['access_error'] );
                    }
                ?>
                <form method="post">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5.21vh" class="text-center">Br.</th>
                                <th class="text-center">Ime i prezime učenika</th>
                                <th class="text-center">Prisutan</th>
                                <th class="text-center">Odsutan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ucenici as $ucenik)
                            {
                                $checked = nadjiIzostanak($ucenik['ucenik_id'], $cas, $conn, $pocetakSedmice, $krajSedmice);
                                echo '<tr>';
                                $br ++;
                                echo '<td style:"width: 5.21vh" class="text-center">'.$br.'</td>';
                                echo '<td>'.$ucenik['ime'].' '.$ucenik['prezime'].'</td>';
                                echo' <td class="text-center"><input type="radio" name="prisustvo['.$ucenik['ucenik_id'].']" value="prisutan" checked></td>
                                <td class="text-center"><input type="radio" name="prisustvo['.$ucenik['ucenik_id'].']" value="odsutan"'.$checked.'></td>
                            </tr>';
                            }?>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" class="btn btn-primary" value="Spremi">
                </form>
                </main>
            </div>
        </div>
        
        <footer>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    </body>
</html>