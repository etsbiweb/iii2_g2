<?php
    $pass = "admin123#@";
    $hash_pass = password_hash($pass,  PASSWORD_DEFAULT);
    session_start();
    require_once("includes/dbh.php");
    if(isset($_SESSION['logged']))
    {
        header("Location: index.php?logged_in");
        exit();
    }

    if(isset($_POST['submit']))
    {
        $error_list = array();
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if(empty($email) || empty($password))
        {
            array_push($error_list,'Empty fields');
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            array_push($error_list,'Invalid email');
        }
        
        $query=$conn->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
        $query->bindParam(":email",$email);
        $query->execute();
        $row = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($row as $result)
        {
            echo $result['password'];
        }
        exit();



        if(count($error_list) == 0)
        {
            if(!empty($row))
            {
                foreach($row as $result)
                {
                    if(password_verify($password, $result["password"]))
                    {
                        $_SESSION['logged'] = 'yes';
                        $_SESSION['id'] = $result['id'];
                        header("Location: index.php?message=logged_successfully");
                        exit();
                    }
                    else
                    {
                        array_push( $error_list,"invalid password");
                        header("Location: login.php?message=invalid_password");
                        exit();
                    }
                }
                
            }
            else
            {
                header("Location: index.php?message=empty_row");
                exit();
            }
        }
        else{
            header("Location: index.php?message=has_errors");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <title>Login</title>
</head>
<body>
    <main class="main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <div class="login-box">
                        <div class="row justify-content-center">
                            <h6 style="font-family: OpenSans,sans-serif; text-align: center;">Login</h6>
                            <form action="login.php" method="post">
                                <?php
                                    foreach( $error_list as $error)
                                    {
                                        echo '
                                            <div class="alert alert-danger mt-1" role="alert">
                                                '.$error.'
                                            </div>
                                        ';
                                    }
                                ?>
                                <div class="mt-3">
                                    <label for="email" class="form-label" style="font-family: Inter,sans-serif; font-size: 13px;">Email:</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="mt-1">
                                    <label for="password" class="form-label" style="margin-top: 20px; font-family: Inter,sans-serif; font-size: 13px;">Password:</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="mt-3 d-flex justify-content-center align-content-center">
                                    <button type="submit" class="btn btn-primary" name="submit" style="margin-top: 20px; font-family: Inter,sans-serif;">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>