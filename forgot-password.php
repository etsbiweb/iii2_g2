<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once("includes/send-mail.php");
    require_once("includes/dbh.php");

    if(isset($_POST['submit']))
    {
        $email = $_POST['email'];

        if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $checkAccount = $conn->prepare("SELECT * FROM `users` WHERE `email` = :acc_email");
            $checkAccount->bindParam(":acc_email", $email);
            $checkAccount->execute();
            if($checkAccount->rowCount()==0){
                header("Location: login.php?message=unknown_mail");
                exit();
            }
            
            $token = bin2hex(random_bytes(32));
            $createTokenQuery = $conn->prepare('INSERT INTO `password_resets`(`email`, `token`) VALUES (:email,:token)');
            $createTokenQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $createTokenQuery->bindParam(':token', $token, PDO::PARAM_STR);
            $createTokenQuery->execute();

            

            $userMessage = 'This is a message from elearning team, you requested a password reset
            http://localhost/iii2_g2/reset_password.php?token='.$token.' expires in 1 hour.
            ';

            $userMessage = trim($userMessage);

            $send_email = new Mail();
            $send_email->SendMail($email,'elearning@gmail.com','Password reset',$userMessage);

        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/forgot-password.css">
    <title>forgot-password</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="forgot-password.php" method="post">
                <a href="https://etsbi.edu.ba/" class="icon" target="_blank">
                    <img src="imgs/dwa.png" alt="ETSBI" class="custom-icon">
                </a>
                <h1>Zaboravljena lozinka</h1>
                <span>Unesite svoju email adresu</span>
                <input type="email" name="email" id="email" placeholder="Email">
                <button type="submit" name="submit" id="submit">Spremi</button>
            </form>
        </div>
    
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Zaboravljena lozinka</h1>
                    <p>Kako bi resetovali svoju lozinku, potrebno je unijeti email adresu.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>