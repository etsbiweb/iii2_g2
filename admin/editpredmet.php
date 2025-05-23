<?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/razredi.php");
    require_once("../includes/send-mail.php");
    //require_once("../includes/log.php");

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $id=$_REQUEST['id'];
    $qSelect = $conn->prepare('SELECT * FROM predmet WHERE predmet_id = :id');
    $qSelect->bindParam(':id', $id);
    $qSelect->execute();
    $predmet = $qSelect->fetch(PDO::FETCH_ASSOC);

    if(isset($_POST['submit']))
    {
        $naziv_predmeta = $_POST['naziv_predmeta'];
        $boja = $_POST['boja'];

        if(!empty($naziv_predmeta) && !empty($boja))
        {
            $qUpdate = $conn->prepare("UPDATE predmet SET ime_predmeta = :naziv, boja = :boja WHERE predmet_id = :id");
            $qUpdate->bindParam(":naziv", $naziv_predmeta);
            $qUpdate->bindParam(":boja",$boja);
            $qUpdate->bindParam(':id',$id);
            $qUpdate->execute();            

            $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
            Novi podaci su uspješno sačuvani.
            </div>';
            header('Location: dashboard.php');
        }
        else
        {
            $message = '<div class="alert alert-danger my-3" role="alert">
            Molimo popunite sve podatke
            </div>';
        }
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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar">
                <h5 class="px-3 fs-3 my-3">Admin panel</h5>
                <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
                <a href="prikaziprofesore.php" class="active"><i class="bi bi-person-badge me-2"></i>Profesori</a>
                <div class="dropdown-container">
                    <a href="#"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                    <ul class="dropdown-menu">
                        <?php
                        foreach ($razredi as $razred)
                        { ?>
                            <li class="has-submenu">
                            <a href="#"><?php echo $razred['godina']; ?></a>
                            <ul class="dropdown-submenu">
                            <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>       
                            <?php
                            foreach ($odjeljenja as $odjeljenje)
                            {
                            ?>
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
                <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($razredi as $razred)
                    { ?>
                        <li class="has-submenu">
                        <a href="#"><?php echo $razred['godina']; ?></a>
                        <ul class="dropdown-submenu">
                        <?php $odjeljenja = dohvatiOdjeljenja($conn, $razred); ?>       
                        <?php
                        foreach ($odjeljenja as $odjeljenje)
                        { ?>
                        <li><a href="prikaziraspored.php?id=<?php echo $odjeljenje['razred_id'];?>"><?php echo $odjeljenje['godina']; echo $odjeljenje['odjeljene']; ?></a></li>
                        <?php 
                        } ?>   
                        </ul>
                        </li>
                    <?php 
                    } ?>
                </ul>
            </div>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
            </nav>

            <main class="col-md-10 content">
                <h3 class="text-align-center">Dodavanje predmeta</h3>
                <form action="" method="post">
                    <div class="mt-3">
                        <label for="naziv_predmeta" class="form-label">Naziv predmeta:</label>
                        <input type="text" name="naziv_predmeta" id="naziv_predmeta" class="form-control" placeholder="Naziv" value="<?php echo $predmet['ime_predmeta'];?>" required>
                    </div>
                    <div class="mt-3">
                        <label for="boja" class="form-label mx-1">Boja:</label>
                        <input type="color" name="boja" id="boja" value="<?php echo $predmet['boja'];?>">
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" name="submit" id="submit">Submit</button>
                    </div>
                </form>
                <?php if(isset($message)): echo $message; endif; ?>
            </main>
        </div>
    </div>
</body>