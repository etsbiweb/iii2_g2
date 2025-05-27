<?php
require_once("../includes/dbh.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");
require_once("../includes/razrednistvo.php");

$qUcenici = $conn->prepare('SELECT * FROM ucenici WHERE razred_id = :id');
$qUcenici->bindParam(':id', $razred['razred_id']);
$qUcenici->execute();
$ucenici = $qUcenici->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['submit']))
{
    $opravdaniCount=[];
    $neopravdaniCount=[];
    $status = $_POST['status'];
    foreach($status as $izostanakId => $value)
    {
        $ucenik = $_POST['ucenikId'][$izostanakId];
        $qUpdateIz = $conn->prepare('UPDATE `izostanci` SET `status_izostanka`=:st WHERE izostanak_id = :id');
        $qUpdateIz->bindParam(':id', $izostanakId);
        $qUpdateIz->bindParam(':st', $value);
        $qUpdateIz->execute();

        $opravdaniCount = [];
        $neopravdaniCount = [];

        if($value == 'Opravdan')
        {
            if(!isset($opravdaniCount[$ucenik]))
            {
                $opravdaniCount[$ucenik] = 0;
            }
            $opravdaniCount[$ucenik]++;
        }
        else if($value == 'Neopravdan')
        {
            if(!isset($neopravdaniCount[$ucenik]))
            {
                $neopravdaniCount[$ucenik] = 0;
            }
            $neopravdaniCount[$ucenik]++;
        }

        foreach($opravdaniCount as $ucenikId => $count)
        {
            $qSelect = $conn->prepare('SELECT opravdani FROM ucenici WHERE ucenik_id = :id');
            $qSelect->bindParam(':id', $ucenikId);
            $qSelect->execute();
            $trenutno = $qSelect->fetchColumn();
            $novo = $trenutno + $count;
            $qUpdateUcenik = $conn->prepare('UPDATE ucenici SET opravdani = :novo WHERE ucenik_id = :id');
            $qUpdateUcenik->bindParam(':novo', $novo);
            $qUpdateUcenik->bindParam(':id', $ucenikId);
            $qUpdateUcenik->execute();
            $novo = $trenutno + $count;
        }
        foreach($neopravdaniCount as $ucenikId => $count)
        {
            echo var_dump($neopravdaniCount);
            $qSelect = $conn->prepare('SELECT neopravdani FROM ucenici WHERE ucenik_id = :id');
            $qSelect->bindParam(':id', $ucenikId);
            $qSelect->execute();
            $trenutno = $qSelect->fetchColumn();
            $novo = $trenutno + $count;
            $qUpdateUcenik = $conn->prepare('UPDATE ucenici SET neopravdani = :novo WHERE ucenik_id = :id');
            $qUpdateUcenik->bindParam(':novo', $novo);
            $qUpdateUcenik->bindParam(':id', $ucenikId);
            $qUpdateUcenik->execute();
        }
        
        $_SESSION['izostanak_message']= '<div class="alert alert-success" role="alert">
        Podaci su uspješno sačuvani.
        </div>';
    }
    header('Location: dashboard.php');
    exit();
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
        <script src="../scripts/app.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Dobrodošli, <?php if($qProf->rowCount()>0): echo $profesor['ime_prezime']; endif; ?>!</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <div class="dropdown-container">
                    <a href="#" class="active dropdown-toggle"><i class="bi bi-book me-2"></i>Moj razred</a>
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
                                <th class="text-center">Ime i prezime učenika</th>
                                <th class="text-center">Ime i prezime profesora</th>
                                <th class="text-center">Dan</th>
                                <th class="text-center">Čas</th>
                                <th class="text-center">Opravdan</th>
                                <th class="text-center">Neopravdan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ucenici as $ucenik)
                            {
                                $qIzostanci = $conn->prepare('SELECT * FROM izostanci WHERE ucenik_id = :id AND status_izostanka IS NULL');
                                $qIzostanci->bindParam(':id', $ucenik['ucenik_id']);
                                $qIzostanci->execute();
                                $izostanci = $qIzostanci->fetchAll(PDO::FETCH_ASSOC);
                                foreach($izostanci as $iz)
                                {
                                    //echo date('d. m. Y.', strtotime($iz['vrijeme']));
                                    $dan = date('l', strtotime(date('Y-m-d', strtotime($iz['vrijeme']))));
                                    $prevodDana = [
                                    "Monday" => 'Ponedjeljak',
                                    "Tuesday" => "Utorak",
                                    "Wednesday" => "Srijeda",
                                    "Thursday" => "Četvrtak",
                                    "Friday" => "Petak"
                                    ];
                                    echo '<tr>';
                                    echo '<td class="text-center">'.$ucenik['ucenik_id'].' '.$ucenik['ime'].' '.$ucenik['prezime'].'</td>';
                                    echo '<td class="text-center">'.$iz['ime_profesora'].'</td>';
                                    echo '<td class="text-center">'.$prevodDana[$dan].'</td>';
                                    echo '<td class="text-center">'.$iz['redni_broj_casa'].'</td>';
                                    echo' <td class="text-center"><input type="radio" name="status['.$iz['izostanak_id'].']" value="Opravdan"></td>
                                    <td class="text-center"><input type="radio" name="status['.$iz['izostanak_id'].']" value="Neopravdan"></td>
                                    <input type="hidden" name="ucenikId['.$iz['izostanak_id'].']" value="'.$ucenik['ucenik_id'].'">
                                    </tr>';
                                }
                            }?>
                        </tbody>
                    </table>
                    <input type="submit" name="submit" value="Spremi" class="btn btn-primary">
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