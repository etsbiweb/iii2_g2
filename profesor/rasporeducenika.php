<?php
require_once("../includes/dbh.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");
require_once("../includes/razrednistvo.php");

$qCasovi = $conn->prepare("SELECT * FROM cas, razred WHERE cas.razred_id = razred.razred_id AND razred.razred_id = :id");
$qCasovi->bindParam(":id", $razred['razred_id']);
$qCasovi->execute();
$casovi = $qCasovi->fetchAll(PDO::FETCH_ASSOC);

function prikaziCas($id, $conn)
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
        <a class="cas">'.$predmet['ime_predmeta'].' - '.$profesor['ime_prezime'].'</a></div>';
    }
    else
    {
        echo '<div class="cell subject-cell"> Nepoznat Predmet </div>';
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
        <link rel="stylesheet" href="../css/raspored.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Dobrodošli, <?php if($qProf->rowCount()>0): echo $profesor['ime_prezime']; endif; ?>!</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <div class="dropdown-container">
                    <a href="#" class="active"><i class="bi bi-book me-2"></i>Moj razred</a>
                    <ul class="dropdown-menu">
                        <li class="has-submenu"><a href="listaucenika.php">Lista učenika</a></li>
                        <li class="has-submenu"><a href="rasporeducenika.php">Raspored časova</a></li>
                        <li class="has-submenu"><a href="noviizostanci.php">Novi izostanci</a></li>
                    </ul>
                </div>
                <a href="mojraspored.php"><i class="bi bi-calendar-week me-2"></i>Moj raspored</a>
                <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
                </nav>
                <main class="col-md-10 content">
                    <div class="naziv-razreda">Raspored Vašeg razreda</div>
                    <div class="main-wrapper">
                    <?php 
                        if(isset($_SESSION["access_error"]))
                        {
                            echo $_SESSION['access_error'];
                            unset( $_SESSION['access_error'] ); 
                        }
                        ?>
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
                            <div class="cell day-header"><?php echo $prevodDana[$dan]?></div>
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
                </main>
            </div>
        </div>
        <footer>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    </body>
</html>