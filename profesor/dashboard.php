<?php
require_once("../includes/dbh.php");
require_once("../includes/profesor.php");
require_once("../includes/profesorcheck.php");
$qRazred = $conn->prepare("SELECT * FROM razred, profesor WHERE razred.razred_id = profesor.razred_id AND profesor.profesor_id = :id");
$qRazred->bindParam(':id', $profesor['profesor_id']);
$qRazred->execute();
if($qRazred->rowCount() > 0) 
{
    $razred = $qRazred->fetch(PDO::FETCH_ASSOC);
}
$qBrojCasova = $conn->prepare("SELECT COUNT(*) FROM cas
INNER JOIN profesor_predmet pp ON pp.profesor_predmet_id = cas.profesor_predmet_id
INNER JOIN profesor prof ON pp.profesor_id = prof.profesor_id WHERE prof.profesor_id = :id");
$qBrojCasova->bindParam(":id", $profesor['profesor_id']);
$qBrojCasova->execute();
$brojCasova = $qBrojCasova->fetchColumn();

$qBrojUcenika = $conn->prepare('SELECT COUNT(*) FROM ucenici
INNER JOIN razred ON razred.razred_id = ucenici.razred_id WHERE razred.razred_id = :id');
$qBrojUcenika->bindParam(':id', $razred['razred_id']);
$qBrojUcenika->execute();
$brojUcenika = $qBrojUcenika->fetchColumn();

$qBrojSvihIzostanaka = $conn->prepare('SELECT COUNT(*) FROM izostanci
INNER JOIN ucenici ON ucenici.ucenik_id = izostanci.ucenik_id
INNER JOIN razred ON razred.razred_id = ucenici.razred_id WHERE razred.razred_id = :id');
$qBrojSvihIzostanaka->bindParam(':id', $razred['razred_id']);
$qBrojSvihIzostanaka->execute();
$brojSvihIzostanaka = $qBrojSvihIzostanaka->fetchColumn();

$qIzostanciDanas = $conn->prepare('SELECT * FROM izostanci
INNER JOIN ucenici ON ucenici.ucenik_id = izostanci.ucenik_id
INNER JOIN razred ON razred.razred_id = ucenici.razred_id WHERE razred.razred_id = :id AND date(izostanci.vrijeme) = CURRENT_DATE');
$qIzostanciDanas->bindParam(':id', $razred['razred_id']);
$qIzostanciDanas->execute();
$izostanciDan = $qIzostanciDanas->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="dashboard.php"class="active"><i class="bi bi-house me-2"></i>Početna</a>
                <div class="dropdown-container">
                    <a href="#" class="dropdown-toggle"><i class="bi bi-book me-2"></i>Moj razred</a>
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
                    <?php if(isset($_SESSION["access_error"]))
                        {
                        echo $_SESSION['access_error'];
                        unset( $_SESSION['access_error'] );
                        }?>
                    <h2 class="mb-4">Početna</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-mortarboard card-icon mb-2"></i>
                            <h6>Časova danas</h6>
                            <h3><?php echo $brojCasova;?></h3>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-person-circle card-icon mb-2"></i>
                            <h6>Broj učenika u Vašem razredu</h6>
                            <h3><?php echo $brojUcenika;?></h3>
                        </div>
                        </div>
                        <div class="col-md-4">
                        <div class="card text-center p-3">
                            <i class="bi bi-exclamation-triangle card-icon mb-2"></i>
                            <h6>Ukupan broj izostanaka u Vašem razredu</h6>
                            <h3><?php echo $brojSvihIzostanaka;?></h3>
                        </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="mb-3">Nedavne aktivnosti</h5>
                            <ul class="mb-0">
                            <li>Nema aktivnosti</li>
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