<?php
    require_once("includes/dbh.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $token = $_REQUEST['token'];
    echo $token;

    if(isset($_POST['submit']))
    {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        if(!empty($password) && !empty($confirm))
        {
            if($password === $confirm)
            {
                $querySelectToken = $conn->prepare("SELECT * FROM `users` WHERE `token` = :token");
                $querySelectToken->bindParam(":token",$token);
                $querySelectToken->execute();
                $user_row = $querySelectToken->fetch(PDO::FETCH_ASSOC);
                
                $hash_password = password_hash($password,PASSWORD_DEFAULT);
                                
                $token_two = NULL;
                $queryUpdateUser = $conn->prepare("UPDATE `users` SET `password`= :hash_pw, `token`= :token WHERE `user_id` = :user_id");
                $queryUpdateUser->bindParam(":hash_pw",$hash_password);
                $queryUpdateUser->bindParam(":token",$token_two);
                $queryUpdateUser->bindParam(":user_id",$user_row['user_id']);
                $queryUpdateUser->execute();

                exit();
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>verify-profile</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="verify-password.php" method="post">
                <a href="https://etsbi.edu.ba/" class="icon" target="_blank">
                    <img src="imgs/dwa.png" alt="ETSBI" class="custom-icon">
                </a>
                <h1>Set password</h1>
                <div class="social-icons">
                    <a href="https://etsbi.edu.ba/" class="icon" target="_blank"><i class="fa-solid fa-graduation-cap"></i></a>
                    <a href="https://www.facebook.com/etsbi.bihac/?locale=hr_HR" class="icon" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/etsbi_/" class="icon" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span>or enter your password</span>
                <input type="passwrod" name="password" id="password" placeholder="Password">
                <input type="password" name="confirm_password" id="confirm_passwrod" placeholder="Confirm Password">
                <button type="submit" name="submit" id="submit">Send</button>
            </form>
        </div>
    
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your email and we will send you a link to reset your password!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

