<?php 
    require_once "includes/dbh.php";
    session_start();
    if(!isset($_SESSION['logged'])){
        header("Location: login.php?message=logintoaccess");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Index</title>
</head>
<body>
    
</body>
</html>