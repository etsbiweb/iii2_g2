<?php
$qProf = $conn->prepare("SELECT * FROM profesor WHERE user_id = :id");
$qProf->bindParam(":id", $_SESSION['id']);
$qProf->execute();
$profesor = $qProf->fetch(PDO::FETCH_ASSOC);
 ?>