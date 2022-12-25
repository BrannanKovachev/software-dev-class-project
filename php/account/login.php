<?php
//Database Connection and Session Creation
require_once "../database/config.php";
require_once "../database/session.php";

$error = '';
   
   if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //validate if email is empty
    if(empty($email)){
        $error .= '<p class="error">Please enter email.</p>';
    }

    //validate if password is empty
    if(empty($password)){
        $error .= '<p class="error">Please enter your password.</p>';
    }
    
    // Retrieves User's relevant Information and sets Session Variables
    if(empty($error)){
        if($query = $db->prepare("SELECT * FROM users WHERE email = ?")) {
            $query->bind_param('s', $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows > 0) {
                $query->bind_result($id, $nme, $pword, $eml, $typ, $amtUnpd, $adrs);
                $query->fetch();

                if(password_verify($password, $pword)){
                    $_SESSION["userid"] = $id;
                    $_SESSION["name"] = $nme;
                    $_SESSION["email"] = $eml;
                    $_SESSION["status"] = $typ;
                    $_SESSION["address"] = $adrs;
                    $_SESSION["login"] = true;
                    $_SESSION["login_time_stamp"] = time();  
                    session_regenerate_id();

                    //Redirect the user to the welcome page
                    header("location: ../../index.php");
                    exit;
                }else{
                    $error .= '<p class="error">The password is not valid.</p>';
                }
            }else{
                $error .= '<p class="error">No User exists with that email address.</p>';
            }
        }
        $query->close();
    }
    //Close Connection
    mysqli_close($db);

   }
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>

        <!-- Link Styles -->
        <link rel = "stylesheet" href="../../css/styleLogin.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

    </head>
    <body>

    <!-- Input and Buttons for Login -->
        <div class="loginbox">
            <div class="avatarbox">
                <div class="col-md-12">
                    <h2>Login Form</h2>
                    <p style="margin-bottom: 10px;">Please fill in your email and password.</p>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" name ="submit" class="btn btn-primary" value="Submit">
                        </div>
                        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    </form>
                </div>
            </div>
        </div>

                    <!-- Bootstrap Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
    </body>
</html>