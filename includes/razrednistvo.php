<?php 
$qRazred = $conn->prepare("SELECT * FROM razred, profesor WHERE razred.razred_id = profesor.razred_id AND profesor.profesor_id = :id");
$qRazred->bindParam(':id', $profesor['profesor_id']);
$qRazred->execute();
if($qRazred->rowCount() > 0) 
{
    $razred = $qRazred->fetch(PDO::FETCH_ASSOC);
}
else
{
    $_SESSION['access_error']='<div class="alert alert-danger" role="alert">
    Vi niste razrednik nijednom odjeljenju
    </div>';
    header("Location: dashboard.php");
    exit();
}
?>