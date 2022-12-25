
<?php
//Database Connection and Session Creation
require_once "../database/config.php";
require_once "../database/session.php";

// Verifies Information and Creates User Account in DB
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])){

    // Retrieves Inputs
    $fullname = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST["confirm_password"]);

    // Encrypts Password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prepares DB statements
    if($query = $db->prepare("SELECT * FROM users WHERE email =?")) {
        $error = '';
        $query->bind_param('s', $email);
        $query->execute();
        $query->store_result();

            // Verifies the email doesn't already have an account ELSE Alerts the user
            if($query->num_rows > 0){
                $error .= '<p class = "error">The email address is already registered!</p>';
                echo "<script type=\"text/javascript\"> alert(\"The email address is already registered!\") </script>";


            } else{

                // Verifies the Password is 6 characters long ELSE Alerts the user
                if(strlen($password) < 6){
                    $error .= '<p class="error">Password must have atleast 6 characters,</p>';
                    echo "<script type=\"text/javascript\"> 
                        alert(\"Password must have atleast 6 characters\"); </script>";


                }

                // Checks that the Confirmation Password Exists ELSE Alerts the user
                if(empty($confirm_password)){
                    $error .= '<p class="error"> Please enter confirm password.</p>';
                } else{

                    // Checks that the Confirmation Password matches the Password ELSE Alerts the user
                    if (empty($error) && ($password != $confirm_password)){
                        $error .= '<p class="error">Password did not match.</p>';
                        echo "<script type=\"text/javascript\"> alert(\"Password did not match.\") </script>";

                    }
                }

                // Creates User Account in DB
                if(empty($error)){
                    $insertQuery = $db->prepare("INSERT INTO users (name, email, password, homeAddress) VALUES (?, ?, ?, ?);");
                    $insertQuery->BIND_PARAM("ssss", $fullname, $email, $password_hash, $address);
                    $result = $insertQuery->execute();
                    if($result){
                        $error .= '<p class = "success">Your regsitration was successful!</p>';
                    } else{
                        echo "Error: " . $db->error;
                    }
                    $insertQuery->close();
                    header("location: login.php");
                    exit;
                }
            }
            $query->close();
        }
    mysqli_close($db);

}
?>



<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <title>Sign Up - KBNB Kars</title>

        <!-- Linking Styles -->
        <link rel = "stylesheet" href="../../css/styleLogin.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

        
    </head>

    <body>
        <div class="registerBox">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <h2>Register</h2>
                    <p>Please fill this form to create an account.</p>

                    <!-- Input Fields and Button to Create an Account -->
                    <form action="" method = "post">
                        <div class="form-group" id="register">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Home Address (optional)</label>
                            <input type="text" name="address" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label id="P1">Password (must be at least 6 characters long)</label>
                            <input type="password" name="password" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                        </div>
                        <p> Already have an account? <a href="login.php">Login here</a></p>
                    </form>
                </div>
            </div>
        </div>

                    <!-- Bootstrap Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

    </body>
</html>
