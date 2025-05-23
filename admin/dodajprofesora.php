<?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/razredi.php");
    require_once("../includes/send-mail.php");
    //require_once("../includes/log.php");

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $qPredmeti = $conn->prepare('SELECT * FROM predmet');
    $qPredmeti->execute();
    $predmeti = $qPredmeti->fetchAll(PDO::FETCH_ASSOC);


    if(isset($_POST['submit']))
    {
        $ime_prezime = $_POST['profesor_ime'];
        $email = $_POST['profesor_email'];
        $razrednistvo = $_POST['razrednistvo'];
        $odabraniPredmeti = $_POST['opredmeti'];

        if(!empty($ime_prezime) && !empty($email))
        {            
            if(empty($razrednistvo))
            {
                $razrednistvo = null;
            }
            $token = bin2hex(random_bytes(32));
            $pristup='profesor';
            $qUser = $conn->prepare('INSERT INTO users (`email`, `pristup`, `token`) VALUES (:email, :pristup, :token)');
            $qUser->bindParam(':email', $email);
            $qUser->bindParam(':pristup', $pristup);
            $qUser->bindParam(':token', $token);
            $qUser->execute();

            $qUser=$conn->prepare('SELECT user_id FROM users WHERE email = :email');
            $qUser->bindParam(':email', $email);
            $qUser->execute();
            $id = $qUser->fetchColumn();

            $qProfesor = $conn->prepare("INSERT INTO `profesor`(`user_id`, `ime_prezime`, `razred_id`) VALUES (:user_id, :ime_prezime, :razred_id);");
            $qProfesor->bindParam(":user_id", $id);
            $qProfesor->bindParam(":ime_prezime", $ime_prezime);
            $qProfesor->bindParam(':razred_id', $razrednistvo);
            $qProfesor->execute();

            $qProfesor =$conn->prepare('SELECT profesor_id FROM profesor WHERE ime_prezime = :ime');
            $qProfesor->bindParam(':ime', $ime_prezime);
            $qProfesor->execute();
            $id = $qProfesor->fetchColumn();
            
            foreach($odabraniPredmeti as $op)
            {
                $qProfPred = $conn->prepare('INSERT INTO profesor_predmet (profesor_id, predmet_id) VALUES (:prof, :pred)');
                $qProfPred->bindParam(':prof', $id);
                $qProfPred->bindParam(':pred', $op);
                $qProfPred->execute();  
            }

            $email_class = new Mail();
            $user_message = 'Click this link to verify your profile, enter your desired password and confirm it then you can log in to the our site.
            http://localhost/iii2_g2/verify-password.php?token='.$token;
            $email_class->SendMail($email,'elearning@gmail.com','Verify Profile',$user_message);
            
            $_SESSION['delete_msg'] = '<div class="alert alert-success" role="alert">
            Profesor uspješno dodan
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
    <script src="../js/multiselect.js"></script>
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
                <h3 class="text-align-center">Dodavanje profesora</h3>
                <form action="" method="post">
                    <div class="mt-3">
                        <label for="profesor_ime" class="form-label">Ime i Prezime: <?php //echo $odjeljenja['razred_id'];?></label>
                        <input type="text" name="profesor_ime" id="profesor_ime" class="form-control" placeholder="Ime i Prezime" required>
                    </div>
                    <div class="mt-3">
                        <label for="profesor_email" class="form-label">Email:</label>
                        <input type="email" name="profesor_email" id="profesor_email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mt-3">
                        <label for="razrednistvo" class="form-label">Razredništvo:</label>
                        <select name="razrednistvo" id="razrednistvo">
                            <option value="" selected>Nema</option>
                            <?php
                            foreach ($razredi as $razred)
                            {
                                $odjeljenja = dohvatiOdjeljenja($conn, $razred);
                                foreach ($odjeljenja as $odjeljenje)
                                {
                                    echo '<option value="'.$odjeljenje['razred_id'].'">'.$razred['godina'].$odjeljenje['odjeljene'].'</option>';
                                }
                            }
                            ?>
                        </select>
                        </div>
                        <div class="mt-3">
                        <label for="predmeti">Predmeti:</label>
                        </div>
                        <div class="mt-3">
                            <select name="opredmeti[]" id="opredmeti" multiple>
                                <?php foreach ($predmeti as $predmet)
                                {
                                    echo '<option value="'.$predmet['predmet_id'].'">'.$predmet['ime_predmeta'].'</option>';
                                } ?>
                            </select>
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