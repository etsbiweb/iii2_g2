<?php

$qRazredi = $conn->prepare("SELECT * FROM razred GROUP BY godina");
$qRazredi->execute();
$razredi = $qRazredi->fetchAll(PDO::FETCH_ASSOC);

function dohvatiOdjeljenja($conn, $razred)
{
    $qOdjeljenja = $conn->prepare("SELECT * FROM razred WHERE godina = :razred ORDER BY odjeljene ASC");
    $qOdjeljenja->bindParam(':razred', $razred['godina']);
    $qOdjeljenja->execute();
    return $qOdjeljenja->fetchAll(PDO::FETCH_ASSOC);
}


 ?>