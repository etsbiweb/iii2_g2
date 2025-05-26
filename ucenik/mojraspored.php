<?php
require_once("../includes/dbh.php");
require_once("../includes/ucenik.php");

$qCasovi = $conn->prepare("SELECT * FROM cas INNER JOIN razred ON razred.razred_id = cas.razred_id WHERE razred.razred_id = :id");
$qCasovi->bindParam(":id", $ucenik['razred_id']);
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
        <div class="col-md-2 sidebar">
            <h5 class="px-3 fs-3 my-3">Dobrodošao/la, <?php echo $ucenik['ime'].' '.$ucenik['prezime']; ?>!</h5>
            <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="#"><i class="bi bi-book me-2"></i>Moji predmeti</a>
            <a href="mojraspored.php"class="active"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
        </div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="naziv-razreda"><?php //echo $r['godina']; echo $r['odjeljene'];?>I1</div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>