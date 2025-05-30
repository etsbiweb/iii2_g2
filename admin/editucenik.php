<?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/razredi.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $id = $_REQUEST['id'];
    $qFind = $conn->prepare('SELECT * FROM ucenici, users WHERE ucenici.ucenik_id = :id AND ucenici.user_id = users.user_id');
    $qFind->bindValue(':id', $id);
    $qFind->execute();
    $ucenik = $qFind->fetch(PDO::FETCH_ASSOC);
    
    if(isset($_POST['submit']))
    {
        $ime = $_POST['ime'];
        $prezime = $_POST['prezime'];
        $email = $_POST['student_email'];
        $jmbg = $_POST['student_jmbg'];

        if(!empty($ime) && !empty($email) && !empty($jmbg) && !empty($prezime))
        {
            $qUpdate = $conn->prepare('UPDATE `ucenici` SET `ime`= :ime, `prezime`= :prezime, `jmbg`= :jmbg WHERE ucenici.ucenik_id = :id');
            $qUpdate->bindParam(':ime', $ime);
            $qUpdate->bindParam(':prezime', $prezime);
            $qUpdate->bindParam(':jmbg', $jmbg);
            $qUpdate->bindParam(':id', $ucenik['ucenik_id']);
            $qUpdate->execute();

            $qUpdate = $conn->prepare('UPDATE `users` SET `email`= :email WHERE user_id = :id');
            $qUpdate->bindParam(':id', $ucenik['user_id']);
            $qUpdate->bindParam(':email', $email);
            $qUpdate->execute();

            $_SESSION['delete_msg'] = '<div class="alert alert-success my-3" role="alert">
            Novi podaci su uspješno sačuvani
            </div>';
            header('Location: dashboard.php');
            exit();
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
                    <a href="#" class="active dropdown-toggle"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
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
                    <a href="#" class="dropdown-toggle"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
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

                <h3 class="text-align-center">Dodaj ucenika</h3>
                <form action="" method="post">
                    <div class="d-flex flex-row gap-2">
                        <div class="mt-3">
                            <label for="ime" class="form-label">Ime: </label>
                            <input type="text" name="ime" id="ime" class="form-control" placeholder="Ime" value="<?php echo $ucenik['ime'] ?>" required>
                        </div>
                        <div class="mt-3">
                            <label for="prezime" class="form-label">Prezime: </label>
                            <input type="text" name="prezime" id="prezime" class="form-control" placeholder="Prezime" value="<?php echo $ucenik['prezime'] ?>" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="student_email" class="form-label">Email: </label>
                        <input type="email" name="student_email" id="student_email" class="form-control" placeholder="Email" value="<?php echo $ucenik['email'] ?>" required>
                    </div>
                    
                    <div class="mt-3">
                        <label for="student_jmbg" class="form-label">JMBG: </label>
                        <input type="number" name="student_jmbg" id="student_jmbg" class="form-control" placeholder="JMBG" value="<?php echo $ucenik['jmbg'] ?>" required>
                    </div>

                    <div class="mt-3">
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                <?php if(isset($message)): echo $message; endif; ?>
            </main>
        </div>
    </div>
</body>
