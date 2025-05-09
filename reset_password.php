<?php
require_once("includes/dbh.php");
$token = $_REQUEST['token'];
echo $token;

if(isset($_POST['submit'])){
    

    if(empty($token)){
        header("Location: login.php?message=token_empty");
        exit();
    }


    $tokenQuery = $conn->prepare("SELECT * FROM `password_resets` WHERE `token` = :token AND `created_at` > NOW() - INTERVAL 10 HOUR");
    $tokenQuery->bindValue(":token", $token);
    $tokenQuery->execute();

    $row = $tokenQuery->fetch(PDO::FETCH_ASSOC);
    
    if(!$row){
        header("Location: login.php?message=expired_token");
        exit();
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($password) || empty($confirm_password)){
        header('Location: reset_password?message=empty_fields&token='.$token.'');
        exit();
    }

    if($password != $confirm_password){
        header('Location: reset_password.php?message=passwords_dont_match&token='.$token.'');
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $updateQuery = $conn->prepare('UPDATE `users` SET `password` = :sifra WHERE `email` = :email');
    $updateQuery->bindValue(':sifra', $hash);
    $updateQuery->bindValue(':email', $row['email']);
    $updateQuery->execute();

    $deleteQuery = $conn->prepare("DELETE FROM`password_resets` WHERE `token` = :token");
    $deleteQuery->bindValue(":token",$token);
    $deleteQuery->execute();

    header('Location: login.php?message=password_reset_successfull');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>new-password</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="reset_password.php" method="post">
                <a href="https://etsbi.edu.ba/" class="icon" target="_blank">
                    <img src="imgs/dwa.png" alt="ETSBI" class="custom-icon">
                </a>
                <h1>Password Reset</h1>
                <div class="social-icons">
                    <a href="https://etsbi.edu.ba/" class="icon" target="_blank"><i class="fa-solid fa-graduation-cap"></i></a>
                    <a href="https://www.facebook.com/etsbi.bihac/?locale=hr_HR" class="icon" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/etsbi_/" class="icon" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span>Enter your new password</span>
                <input type="password" name="password" id="password" placeholder="Password">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <button type="submit" name="submit" id="submit">Submit</button>
            </form>
        </div>
    
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter and confirm your new password!</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>