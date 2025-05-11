<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> 
    <link rel="stylesheet" href="../css/kartica.css">
</head>
<body>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar">
            <h5 class="px-3 fs-3 my-3">Admin panel</h5>
            <a href="dashboard.php"><i class="bi bi-house me-2"></i>Početna</a>
            <a href="#"><i class="bi bi-person me-2"></i>Učenici</a>
            <a href="#"><i class="bi bi-person-badge me-2"></i>Profesori</a>
            <div class="dropdown-container">
                <a href="razredi.php" class="active"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                <div class="dropdown-menu">
                    <a href="#">I</a>
                    <a href="#">II</a>
                    <a href="#">III</a>
                    <a href="#">IV</a>
                </div>
            </div>
            <a href="#"><i class="bi bi-book me-2"></i>Predmeti</a>
            <a href="#"><i class="bi bi-calendar-week me-2"></i>Raspored časova</a>
            <a href="#"><i class="bi bi-bar-chart me-2"></i>Izostanci</a>
            <a href="../logout.php"><i class="bi bi-person me-2"></i>Log out</a>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="ucenici-grid">
                <?php
                $ucenici = [
                    ['ime' => 'Petar', 'prezime' => 'Petrović'],
                    ['ime' => 'Ana', 'prezime' => 'Anić'],
                    ['ime' => 'Marko', 'prezime' => 'Marković'],
                    ['ime' => 'Ivana', 'prezime' => 'Ivanić'],
                    ['ime' => 'Luka', 'prezime' => 'Lukić'],
                    ['ime' => 'Mia', 'prezime' => 'Mijić'],
                    ['ime' => 'Filip', 'prezime' => 'Filipović'],
                    ['ime' => 'Ema', 'prezime' => 'Emić'],
                    ['ime' => 'Jakov', 'prezime' => 'Jakovljević'],
                ];

                foreach ($ucenici as $ucenik) {
                    echo '<div class="kartica-ucenik">';
                    echo '<div class="ime-prezime-ucenik">' . $ucenik['ime'] . ' ' . $ucenik['prezime'] . '</div>';
                    echo '<div class="gumbi-ucenik">';
                    echo '<button class="gumb-izbrisi-ucenik">Izbriši</button>';
                    echo '<button class="gumb-uredi-ucenik">Uredi</button>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>