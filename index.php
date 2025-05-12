<?php 
    require_once "includes/dbh.php";
    session_start();
    if(!isset($_SESSION['logged'])){
        header("Location: login.php");
        exit();
    }

    if(isset($_SESSION["logged"]))
    {
        $qRole = $conn->prepare("SELECT pristup FROM users WHERE user_id = :id");
        $qRole->bindParam(':id', $_SESSION['id']);
        $qRole->execute();
        $role= $qRole->fetchColumn();
        header("Location: $role/dashboard.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title>Dashboard</title>
</head>
<body>
  <h2 class="text-align-center">Dashboard</h2>
</body>
</html>