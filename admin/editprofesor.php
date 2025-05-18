    <?php
    require_once("../includes/dbh.php");
    require_once("../includes/admincheck.php");
    require_once("../includes/razredi.php");
    //require_once("../includes/log.php");

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $id = $_REQUEST['id'];
    $qFind = $conn->prepare('SELECT * FROM users INNER JOIN profesor ON profesor.user_id = users.user_id AND profesor.profesor_id = :id');
    $qFind->bindParam(":id", $id);
    $qFind->execute();
    if($qFind->rowCount() > 0)
    {
        $prof = $qFind->fetch(PDO::FETCH_ASSOC);
    }
    else
    {
        $_SESSION['delete_msg'] = '<div class="alert alert-danger" role="alert">
        Profesor nije pronađen 
        </div>';
        header('Location: dashboard.php');
        exit();
    }

    $qFind = $conn->prepare('SELECT * FROM profesor_predmet WHERE profesor_id = :id');
    $qFind->bindParam(':id', $id);
    $qFind->execute();
    $predaje = $qFind->fetchAll(PDO::FETCH_ASSOC);
    $predmetiKojePredaje = [];
    foreach ($predaje as $predmetPredaje)
    {
        $predmetiKojePredaje[] = $predmetPredaje['predmet_id'];
    }

    $qPredmeti = $conn->prepare('SELECT * FROM predmet');
    $qPredmeti->execute();
    $predmeti = $qPredmeti->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['submit']))
    {
        $ime_prezime = $_POST['profesor_ime'];
        $email = $_POST['profesor_email'];
        $razrednistvo = $_POST['razrednistvo'];
        $odabraniPredmeti = $_POST['predmeti'];

        if(!empty($email) && !empty($ime_prezime))
        {
            if(empty($razrednistvo))
            {
                $razrednistvo = null;
            }
            //Dodavanje novih veza
            foreach($odabraniPredmeti as $op)
            {
                if(!in_array($op, $predmetiKojePredaje))
                {
                    $qInsert = $conn->prepare("INSERT INTO profesor_predmet (profesor_id, predmet_id) VALUES (:prof, :op)");
                    $qInsert->bindParam(":prof", $id);
                    $qInsert->bindParam(":op", $op);
                    $qInsert->execute();
                }
            }
            //Brisanje starih ukoliko nisu selectane
            foreach($predmetiKojePredaje as $pkp)
            {
                if(!in_array($pkp, $odabraniPredmeti))
                {
                    $qDelete = $conn->prepare("DELETE FROM profesor_predmet WHERE profesor_id = :id AND predmet_id = :pkp");
                    $qDelete->bindParam(":id", $id);
                    $qDelete->bindParam(":pkp", $pkp);
                    $qDelete->execute();
                }
            }

            $qUpdate = $conn->prepare('UPDATE `profesor` SET `ime_prezime` = :ime, `razred_id` = :razred_id WHERE profesor_id = :id');
            $qUpdate->bindParam(':ime', $ime_prezime);
            $qUpdate->bindParam(':razred_id', $razrednistvo);
            $qUpdate->bindParam(':id', $id);
            $qUpdate->execute();

            $qUpdate = $conn->prepare('UPDATE `users` SET `email`= :email WHERE user_id = :id');
            $qUpdate->bindParam(':id', $prof['user_id']);
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
                <h3 class="text-align-center">Promjena podataka o profesoru:</h3>

                <form action="" method="post">
                    <div class="mt-3">
                        <label for="profesor_ime" class="form-label">Ime i Prezime:</label>
                        <input type="text" name="profesor_ime" id="profesor_ime" class="form-control" placeholder="Ime Prezime" value="<?php echo $prof['ime_prezime']; ?>" required>
                    </div>
                    <div class="mt-3">
                        <label for="profesor_email" class="form-label">Email:</label>
                        <input type="email" name="profesor_email" id="profesor_email" class="form-control" placeholder="Email" value="<?php echo $prof['email']; ?>" required>
                    </div>
                    <div class="mt-3">
                        <label for="razrednistvo" class="form-label">Razredništvo:</label>
                        <select name="razrednistvo" id="razrednistvo">
                            <option value="">Nema</option>
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
                        <select name="predmeti[]" id="predmeti" multiple>
                            <?php
                            foreach ($predmeti as $predmet) {
                                $selected = in_array($predmet['predmet_id'], $predmetiKojePredaje) ? 'selected' : '';
                                echo '<option value="'.$predmet['predmet_id'].'" '.$selected.'>'.$predmet['ime_predmeta'].'</option>';
                            }
                            ?>
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
    <script src="multiselect.js"></script>
</body>
</html>