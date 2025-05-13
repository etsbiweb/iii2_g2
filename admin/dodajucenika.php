<?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/send-mail.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $razred_id = $_REQUEST['id'];
    if(isset($_POST['submit'])){
        $ime = $_POST['student_name'];
        $email = $_POST['student_email'];
        $jmbg = $_POST['student_jmbg'];

        if(!empty($razred_id) && !empty($ime) && !empty($email) && !empty($jmbg)){
            $token = bin2hex(random_bytes(32));
            $queryUserInsert = $conn->prepare("INSERT INTO `users`(`email`, `pristup`, `token`) VALUES (:email,:pristup,:token)");
            $queryUserInsert->bindParam(":email",$email);
            $pristupUcenika="ucenik";
            $queryUserInsert->bindParam(":pristup",$pristupUcenika);
            $queryUserInsert->bindParam(":token",$token);
            $queryUserInsert->execute();

            $queryUserInsert = $conn->prepare("SELECT user_id FROM `users` WHERE `token` = :token");
            $queryUserInsert->bindParam(":token",$token);
            $queryUserInsert->execute();
            $user_row = $queryUserInsert->fetch(PDO::FETCH_ASSOC);



            $queryUcenikInsert = $conn->prepare("INSERT INTO `ucenici`(`user_id`, `ime_prezime`, `jmbg`, `razred_id`, `opravdani`, `neopravdani`) VALUES (:user_id,:ime,:jmbg,:razred_id,:opravdani,:neopravdani)");

            $izostanciDef = 0;

            $queryUcenikInsert->bindParam(":user_id",$user_row['user_id']);
            $queryUcenikInsert->bindParam(":ime",$ime);
            $queryUcenikInsert->bindParam(":jmbg",$jmbg);
            $queryUcenikInsert->bindParam(":razred_id",$razred_id);
            $queryUcenikInsert->bindParam(":opravdani",$izostanciDef);
            $queryUcenikInsert->bindParam(":neopravdani",$izostanciDef);
            $queryUcenikInsert->execute();

            $email_class = new Mail();

            $user_message = 'Click this link to verify your profile, enter your desired password and confirm it then you can log in to the our site.
                http://localhost/iii2_g2/verify-password.php?token='.$token.'
            
            ';
            $email_class->SendMail($email,'elearning@gmail.com','Verify Profile',$user_message);
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
                <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Početna</a>
                <a href="#"><i class="bi bi-person-badge me-2"></i>Profesori</a>
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
                            { ?>
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
                <a href="#"><i class="bi bi-book me-2"></i>Predmeti</a>
                <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
                <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
                <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
            </nav>
        


            <main class="col-md-10 content">

                <h3 class="text-align-center">Dodaj ucenika</h3>
                <form action="dodajucenika.php?id=<?php echo $razred_id; ?>" method="post">
                    <div class="mt-3">
                        <label for="student_name" class="form-label">Name: </label>
                        <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Full Name" required>
                    </div>

                    <div class="mt-3">
                        <label for="student_email" class="form-label">Email: </label>
                        <input type="email" name="student_email" id="student_email" class="form-control" placeholder="Email" required>
                    </div>
                    
                    <div class="mt-3">
                        <label for="student_jmbg" class="form-label">JMBG: </label>
                        <input type="number" name="student_jmbg" id="student_jmbg" class="form-control" placeholder="JMBG" required>
                    </div>

                    <div class="mt-3">
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
