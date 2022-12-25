<?php
//Database Connection and Session Creation
require_once "../database/session.php";
require_once "../database/config.php";

//Check if the user is not logged in, then redirect the user to login page
if(!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: ../account/login.php");
  exit;
}

//Update User's Address in DB when Address is Input and Submitted
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitAddress'])) {
  
    $Address = ltrim($_POST['updateHome']);
  if(!empty($Address)){
    $userID = $_SESSION['userid'];
    $sql = $db->prepare("UPDATE users SET homeAddress = \"$Address\" WHERE idusers = $userID;");
    $result = $sql-> execute();
    $sql -> close();
    $_SESSION["address"] = $Address;
    header("location: accountSettings.php");
    exit;
  }
}

//Update User's Name in DB when Name is Input and Submitted
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitName'])) {
  $Name = ltrim($_POST['updateName']);
  if(!empty($Name)){
  $userID = $_SESSION['userid'];
  $sql = $db->prepare("UPDATE users SET name = \"$Name\" WHERE idusers = $userID;");
  $result = $sql-> execute();
  $sql -> close();
  $_SESSION["name"] = $Name;
  header("location: accountSettings.php");
  exit;
  }
}

//Update User's Password in DB when Name is Input and Submitted
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitPassword'])) {
  $UID = $_SESSION["userid"];
  $stmt = $db->prepare("SELECT password FROM users WHERE idusers = $UID;");
  $stmt -> execute();
  $stmt ->store_result();
  $stmt->bind_result($PasswordReference);
  $stmt->fetch();
  $OldPassword = trim($_POST['checkPassword']);
  $NewPassword1 = trim($_POST['newPassword1']);
  $NewPassword2 = trim($_POST['newPassword2']);

  // Verify the Current Password Field is Correct ELSE Alert the user
  if(password_verify($OldPassword, $PasswordReference)){

    // Verify the Password is long enough ELSE Alert the User
    if(!(strlen($NewPassword1) < 6)){

      // Verify the New Confirmation Password has Been entered and is Equivalent to the New Password ELSE Alert the user
      if(!empty($NewPassword2)){
        if($NewPassword1==$NewPassword2){

          // Update the User's Password in the DB 
          $password_hash = password_hash($NewPassword1, PASSWORD_BCRYPT);
          $sql = $db->prepare("UPDATE users SET password=? WHERE idusers=?;");
          $sql->bind_param("si", $password_hash, $UID);
          $result = $sql-> execute();
          $sql -> close();
        }
        else{
          echo "<script type=\"text/javascript\"> 
        alert(\"Your New Passwords Do Not Match.\"); </script>";
        }
      }
      else{
        echo "<script type=\"text/javascript\"> 
        alert(\"Please confirm your password.\"); </script>";
      }
    }
    else{
      echo "<script type=\"text/javascript\"> 
        alert(\"New Password must have at least 6 characters\"); </script>";
    }
  }
  else{
    echo "<script type=\"text/javascript\"> 
        alert(\"Incorrect Current Password\"); </script>";
  }
  header("location: accountSettings.php");
  exit;

}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Links to Styles -->
    <link rel="stylesheet" href="../../css/styleSettings.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>KBNB KARS</title>
</head>

  
  <body class="text-white bgFull" id="RequestBack">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark "> 
        <div class="container-fluid">
          <a class="navbar-brand" href="../../index.php">KBNB Kars</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" 
          aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link "  href="../../index.php">Home</a>
                <li class="nav-item dropdown ">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Request</a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li><a class="dropdown-item" href="../request/requestHome.php">New Request</a></li>
                      <li><a class="dropdown-item" href="../request/requestManagement.php">Manage Your Requests</a></li>
                      </ul>
                      </li>
               <!-- Conditional Displayment of Navbar Tabs: Drivers/Admin -->
                <?php
                  if(isset($_SESSION["status"]) && $_SESSION["status"]>=1){
                    echo "<a class=\"nav-link\" href=\"../driver/driversHome.php\">Drivers</a>";
                  }
                ?>
                <?php
                    if(isset($_SESSION["status"]) && $_SESSION["status"]===2){
                      echo "<li class=\"nav-item dropdown \">
                      <a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarDropdownMenuLink\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">Administration</a>
                      <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuLink\">
                      <li><a class=\"dropdown-item\" href=\"../admin/adminRequests.php\">Manage Requests</a></li>
                      <li><a class=\"dropdown-item\" href=\"../admin/adminFinances.php\">Manage Finances</a></li>
                      <li><a class=\"dropdown-item\" href=\"../admin/adminAccounts.php\">Manage Accounts</a></li>
                      </ul>
                      </li>";
                    }
                ?>
                
            </div>
                        <!-- Login/Logout/Register Buttons-->
             <form class="container-fluid d-flex justify-content-end">
                <div>
                  <?php 
                    if(isset($_SESSION["login"]) && $_SESSION["login"] === true){
                      echo "<a href=\"../account/accountSettings.php\"><button class=\"btn btn-sm btn-outline-secondary me-2\" type=\"button\" >Account Settings</button></a>";
                      echo "<a href=\"../database/logout.php\"><button class=\"btn btn-sm btn-outline-danger\" type=\"button\" >Logout</button></a>";
                    }
                    else{
                      echo "<a href = \"../account/login.php\"><button class=\"btn btn-outline-success me-2\" type=\"button\" >Login</button></a>
                            <a href=\"../account/register.php\"><button class=\"btn btn-sm btn-outline-secondary\" type=\"button\" >Register</button></a>";
                    }
                  ?>
                </div> 
              </form>
            </div> 
        </div>
  </nav>
      <div class="container-fluid">
        <div class="centering-input">
         <div class="d-grid">
          <div class="d-grid justify-content-center">
            <label for="inputAddress" class="form-label" ><h3>Update Your Info</h3></label>
          </div>

          <!-- Update Address Input and Button -->
          <form method="post" id="form1">
            <div class="d-grid justify-content-center gap-2">
              <h5>Address:</h5>
            </div>
              <div class="d-grid justify-content-center gap-2">
                <h5>
                  <?php echo $_SESSION['address'];?>
                </h5>
              </div>
            <input type="text" class="form-control mb-2" name="updateHome" placeholder="Ex: '1234 Main St, City State Zip'">
              <div class="col align-self-center">
                  <div class="d-grid gap-2">
                      <button type="submit" name="submitAddress" value="" class="btn btn-success" >Update</button>
                  </div>
              </div>
          </form>

          <!-- Update Name Input and Button -->
          <form method="post" id="form2">
              <div class="d-grid justify-content-center mt-2">
                <h5>Name:</h5>
              </div>
                <div class="d-grid justify-content-center">
                  <h5>
                    <?php echo $_SESSION['name'];?>
                  </h5>
                </div>
              
              <input type="text" class="form-control mb-2" name="updateName" placeholder="Ex: 'John Doe'">
                <div class="col align-self-center">
                    <div class="d-grid gap-2">
                        <button type="submit" name="submitName" value="" class="btn btn-success" >Update</button>
                    </div>
                </div>
          </form>

          <!-- Update Password Input and Button -->
          <form method="post" id="form3">
            <div class="d-grid justify-content-center mt-2">
                <h5>Update Password</h5>
            </div>
            <div class="d-grid justify-content-center mb-1">
                <b>Must be at least 6 character long</b>
            </div>
                  <input type="password" class="form-control mb-2" name="checkPassword" placeholder="Current Password">
                  <div class="row align-items-center mb-2">
                    <div class="col">
                      <input type="password" class="form-control" name="newPassword1" placeholder="New Password">
                    </div>
                    <div class="col">
                      <input type="password" class="form-control" name="newPassword2" placeholder="Confirm New Password">
                    </div>
                  </div>
                <div class="col align-self-center">
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" name="submitPassword" class="btn btn-success" >Update</button>
                </div>
            </form>
          </div>
        
        <!-- Return to Home Page Button -->
        <div class="row">
          <div class="col d-grid gap-2 mt-2">
            <a href="../../index.php" class="btn btn-danger" role="button" >Return to the Home Page</a>
          </div>
        </div>
      </div>
    </div>

                    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>