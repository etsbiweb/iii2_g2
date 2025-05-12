<?php
session_start();
require_once("../includes/dbh.php");
$qProf = $conn->prepare("SELECT ime_prezime FROM profesor WHERE profesor_id = :id");
$qProf->bindParam(":id", $_SESSION['id']);
$qProf->execute();
$imePrezime = $qProf->fetchColumn();
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
        <header>
            <nav class="col-md-2 sidebar">
            <h5 class="px-3 fs-3 my-3">Dobrodošao/la, <?php echo $imePrezime; ?>!</h5>
            <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="#"><i class="bi bi-book me-2"></i>Moj razred</a>
            <a href="#"><i class="bi bi-calendar-week me-2"></i>Moj raspored</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>    
            </nav>
        </header>
        <main>
            <?php 
                if(isset($_SESSION["access_error"]))
                {
                    echo $_SESSION['access_error'];
                    unset( $_SESSION['access_error'] ); 
                }
            ?>
        </main>
        <footer>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    </body>
</html>