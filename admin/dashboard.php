<?php
require_once("../includes/dbh.php");
$qUcenici = $conn->prepare("SELECT count(*) FROM ucenici;");
$qUcenici->execute();
$ucenici = $qUcenici->fetchColumn();

$qProfesori = $conn->prepare("SELECT count(*) FROM profesor;");
$qProfesori->execute();
$profesori = $qProfesori->fetchColumn();

$qIzostanci = $conn->prepare("SELECT count(*) FROM izostanci WHERE DATE(vrijeme) = CURDATE()");
$qIzostanci->execute();
$izostanci = $qIzostanci->fetchColumn();
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

  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 sidebar">
      <h5 class="px-3 fs-3 my-3">Admin panel</h5>
      <a href="dashboard.php" class="active"><i class="bi bi-house me-2"></i>Početna</a>
      <a href="#"><i class="bi bi-person me-2"></i>Učenici</a>
      <a href="#"><i class="bi bi-person-badge me-2"></i>Profesori</a>
      <div class="dropdown-container">
                <a href="#"><i class="bi bi-grid-3x3-gap me-2"></i>Razredi</a>
                <div class="dropdown-menu">
                    <a href="prikaziucenike.php">I</a>
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

    <!-- Main content -->
    <main class="col-md-10 content">
      <h2 class="mb-4">Početna</h2>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-mortarboard card-icon mb-2"></i>
            <h6>Učenici</h6>
            <h3><?php echo $ucenici; ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-person-circle card-icon mb-2"></i>
            <h6>Profesori</h6>
            <h3><?php echo $profesori; ?></h3>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-exclamation-triangle card-icon mb-2"></i>
            <h6>Izostanci danas</h6>
            <h3><?php echo $izostanci; ?></h3>
          </div>
        </div>
      </div>
       
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card p-3">
            <h5 class="mb-3">Nedavne aktivnosti</h5>
            <ul class="mb-0">
              <li>Dodano 5 učenika u razred II–1</li>
              <li>Izmenjen raspored za razred III–2</li>
              <li>Dodat profesor Matija Perić</li>
              <li>Uklonjena učenica Ana Jurić</li>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card p-3">
            <h5 class="mb-3">Raspored za danas</h5>
            <ul class="list-unstyled mb-0">
              <li>08:00 – 08:45 <strong>Matematika V–1</strong></li>
              <li>08:55 – 09:40 <strong>Engleski jezik IV–3</strong></li>
              <li>09:50 – 10:35 <strong>Hemija III–2</strong></li>
              <li>10:45 – 11:30 <strong>Istorija VII–4</strong></li>
              <li>11:40 – 12:25 <strong>Fizika VI–1</strong></li>
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
