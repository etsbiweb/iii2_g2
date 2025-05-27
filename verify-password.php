<?php
require_once("includes/dbh.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$token = $_GET['token'];

if (isset($_POST['submit'])) {
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);
    
    if (empty($password) || empty($confirm)) {
        $message = "Please fill in both password fields.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $querySelectToken = $conn->prepare("SELECT * FROM `users` WHERE `token` = :token");
        $querySelectToken->bindParam(":token", $token);
        $querySelectToken->execute();
        $user_row = $querySelectToken->fetch(PDO::FETCH_ASSOC);
        
        if ($user_row) {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $token_two = NULL;
            $queryUpdateUser = $conn->prepare("UPDATE `users` SET `password` = :hash_pw, `token` = :token WHERE `user_id` = :user_id");
            $queryUpdateUser->bindParam(":hash_pw", $hash_password);
            $queryUpdateUser->bindParam(":token", $token_two);
            $queryUpdateUser->bindParam(":user_id", $user_row['user_id']);
            $queryUpdateUser->execute();
            
            $_SESSION['login_error'] = '<div class="alert alert-success" role="alert">
            Uspješno ste verifikovali svoj password. 
            </div>';
            unset($_SESSION['logged']);
            unset($_SESSION['id']);
            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/verify-password.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="" method="post">
                <a href="https://etsbi.edu.ba/" class="icon" target="_blank">
                    <img src="imgs/dwa.png" alt="ETSBI" class="custom-icon">
                </a>
                <h1>Unesite lozinku</h1>
                <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
                <input type="password" name="password" id="password" placeholder="Password">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Potvrdite lozinku">
                <button type="submit" name="submit" id="submit">Reset Password</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Verifikacija lozinke</h1>
                    <p>Unesite svoju željenu lozinku</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>