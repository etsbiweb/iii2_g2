<?php
$qProf = $conn->prepare("SELECT ime_prezime FROM profesor WHERE user_id = :id");
$qProf->bindParam(":id", $_SESSION['id']);
$qProf->execute();
$imePrezime = $qProf->fetchColumn();
 ?>