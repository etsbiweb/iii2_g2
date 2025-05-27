<?php
require_once("../includes/dbh.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");
require_once("../includes/razrednistvo.php");

$qUcenici = $conn->prepare("SELECT * FROM ucenici WHERE razred_id = :id");
$qUcenici->bindParam(":id", $razred['razred_id']);
$qUcenici->execute();
$ucenici = $qUcenici->fetchAll(PDO::FETCH_ASSOC);
$br = 0;
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/dashboard.css">
        <link rel="stylesheet" href="../css/kartica.css">
        <link rel="stylesheet" href="../css/izostanci.css">
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
                <div class="naziv-razreda">Vaš razred</div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5.21vh" class="text-center">Br.</th>
                                <th class="text-center">Ime i prezime učenika</th>
                                <th class="text-center">Opravdani</th>
                                <th class="text-center">Neopravdani</th>
                                <th class="text-center">Ukupno izostanaka</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ucenici as $ucenik)
                            {
                                echo '<tr>';
                                $br ++;
                                echo '<td style:"width: 5.21vh" class="text-center">'.$br.'</td>';
                                echo '<td class="text-center">'.$ucenik['ime'].' '.$ucenik['prezime'].'</td>';
                                echo '<td class="text-center">'.$ucenik['opravdani'].'</td>';
                                echo '<td class="text-center">'.$ucenik['neopravdani'].'</td>';
                                echo '<td class="text-center">'.$ucenik['neopravdani']+$ucenik['opravdani'].'</td>
                            </tr>';
                            }?>
                        </tbody>
                    </table>
                </main>
            </div>
        </div>
        <footer>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    </body>
</html>