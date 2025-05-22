<?php
    require_once("includes/dbh.php");
    if(isset($_SESSION['logged']))
    {
        header("Location: index.php?logged_in");
        exit();
    }

    if(isset($_POST['submit']))
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query=$conn->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
        $query->bindParam(":email",$email);
        $query->execute();
        $row = $query->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($row))
        {
            foreach($row as $result)
            {
                if(password_verify($password, $result["password"]))
                {
                    $_SESSION['logged'] = 'yes';
                    $_SESSION['id'] = $result['user_id'];
                    
                    $roleFetch = $conn->prepare("SELECT pristup FROM users WHERE email = :email");
                    $roleFetch->bindParam(":email", $email);
                    $roleFetch->execute();
                    $role = $roleFetch->fetchColumn();
                    header("Location: $role/dashboard.php");
                    exit();
                }
                else
                {
                    $_SESSION['login_error']='<div class="alert alert-danger" role="alert">
                    Unijeli ste netačan password.
                    </div>';
                }
            }
        }
        else
        {
            $_SESSION['login_error']='<div class="alert alert-danger" role="alert">
            Account s datim E-mailom ne postoji.
            </div>';
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $_SESSION['login_error']='<div class="alert alert-danger" role="alert">
            Molimo Vas da unesete ispravan E-mail.
            </div>';
        }

        if(empty($email) || empty($password))
        {
            $_SESSION['login_error']='<div class="alert alert-danger" role="alert">
            Molimo Vas popunite sve podatke.
            </div>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>

<body>
    <?php
        if(isset($_SESSION["access_error"]))
        {
            echo $_SESSION['access_error'];
            unset( $_SESSION['access_error'] );
        }
        if(isset($_SESSION["login_error"]))
        {
            echo $_SESSION['login_error'];
            unset( $_SESSION['login_error'] );
        }
    ?>
   <div class="container" id="container">
        <div class="form-container sign-up">
            <form>
                <br></br>
                </a>
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="https://etsbi.edu.ba/" class="icon"><i class="fa-solid fa-graduation-cap"></i></a>
                    <a href="https://www.facebook.com/etsbi.bihac/?locale=hr_HR" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/etsbi_/" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span>or use your email for registeration</span>
                <input type="text" placeholder="Name">
                <input type="email" placeholder="Email" id="email">
                <input type="password" placeholder="Password" id="password">
                <button>Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form method="post">
                <a href="https://etsbi.edu.ba/" class="icon" target="_blank">
                    <img src="imgs/dwa.png" alt="ETSBI" class="custom-icon">
                    
                </a>
                <h1>Prijavite se</h1>
                <div class="social-icons">
                    
                    <a href="https://etsbi.edu.ba/" class="icon"><i class="fa-solid fa-graduation-cap"></i></a>
                    <a href="https://www.facebook.com/etsbi.bihac/?locale=hr_HR" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/etsbi_/" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>

                <span>upotrebite svoj email i lozinku za pristup.</span>
                <input type="email" placeholder="Email" name="email" id="email">
                <input type="password" placeholder="Lozinka" name="password" id="password">
                <a href="forgot-password.php">Zaboravili ste lozinku?</a>
                <button type="submit" name="submit" id="submit">Prijavite se</button>
                
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Zaštićen pristup!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                
                    <h1>Zaštićen pristup!</h1>

                    <p>Samo ovlašteni korisnici mogu pristupiti ovom sistemu.</p>
                    
                </div>
            </div>
        </div>
    </div>

</body>

</html>