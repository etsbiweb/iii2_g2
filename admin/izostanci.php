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

    $qIzostanci = $conn->prepare('SELECT * FROM izostanci');
    $qIzostanci->execute();
    $izostanci = $qIzostanci->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['submit']))
    {
        $ime_prezime = $_POST['profesor_ime'];
        $email = $_POST['profesor_email'];
        $razrednistvo = $_POST['razrednistvo'];
        if(!empty($_POST['opredmeti'])): $odabraniPredmeti = $_POST['opredmeti']; endif;

        if(!empty($ime_prezime) && !empty($email))
        {
            $queryCheckEmail = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $queryCheckEmail->bindParam(":email", $email);
            $queryCheckEmail->execute();
            $emailExists = $queryCheckEmail->fetchColumn();

            if ($emailExists > 0) {
            $error_msg = "Unešeni email se već koristi.";
            }

            else
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
                
                if(isset($odabraniPredmeti)){
                    foreach($odabraniPredmeti as $op)
                    {
                        $qProfPred = $conn->prepare('INSERT INTO profesor_predmet (profesor_id, predmet_id) VALUES (:prof, :pred)');
                        $qProfPred->bindParam(':prof', $id);
                        $qProfPred->bindParam(':pred', $op);
                        $qProfPred->execute();  
                    }
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
            <a href="izostanci.php" class="active"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
            </nav>

            <main class="col-md-10 content">
                <h3 class="text-align-center">Svi izostanci</h3>
                <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Ime i prezime učenika</th>
                                <th class="text-center">Ime i prezime profesora</th>
                                <th class="text-center">Datum</th>
                                <th class="text-center">Vrijeme</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($izostanci as $izostanak)
                            {
                                $qUcenik = $conn->prepare('SELECT * FROM ucenici, izostanci WHERE ucenici.ucenik_id = izostanci.ucenik_id AND izostanci. izostanak_id = :id');
                                $qUcenik->bindParam(':id', $izostanak['izostanak_id']);
                                $qUcenik->execute();
                                $ucenik = $qUcenik->fetch(PDO::FETCH_ASSOC);
                                echo '<tr>';
                                echo '<td class="text-center">'.$ucenik['ime'].' '.$ucenik['prezime'].'</td>';
                                echo '<td class="text-center">'.$izostanak['ime_profesora'].'</td>';
                                echo '<td class="text-center">'.date('d. m. Y.', strtotime($izostanak['vrijeme'])).'</td>';
                                echo '<td class="text-center">'.date('H:i:s', strtotime($izostanak['vrijeme'])).'</td>';
                                echo '<td class="text-center">'.$izostanak['status_izostanka'].'</td>
                            </tr>';
                            }?>
                        </tbody>
                    </table>
                    </form>
                    <?php if(isset($message)): echo $message; endif; ?>
                </main>
            </div>
        </div>
    </body>