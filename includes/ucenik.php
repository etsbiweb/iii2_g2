<?php
$qUcenik = $conn->prepare("SELECT * FROM ucenici, users WHERE ucenici.user_id = users.user_id AND users.user_id = :id");
$qUcenik->bindParam(":id", $_SESSION['id']);
$qUcenik->execute();
$ucenik = $qUcenik->fetch(PDO::FETCH_ASSOC);
?>