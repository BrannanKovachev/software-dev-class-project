<?php
//Database Connection and Session Creation
require_once "../database/session.php";
require_once "../database/config.php";

//Check if the user is not logged in, then redirect the user to login page
if(!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: ../account/login.php");
  exit;
}

//Confirm the user is an Admin otherwise redirect them to redirect page
if(isset($_SESSION["status"]) && $_SESSION["status"]<2) {
  header("location: adminRedirect.php");
  exit;
}

// Updates User's Account Type in DB
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  $change = trim($_POST['AccountChange']);
  $usersID = trim($_POST['submit']);
  $sql = $db->prepare("UPDATE users SET userType=$change WHERE idusers=$usersID");
  $result = $sql-> execute();
  $sql -> close();
  header("location: adminAccounts.php");
  exit;
}

// Removes User's Account from DB
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['DeleteAcct'])) {
  echo "Wowzer";
  $UserID = trim($_POST['DeleteAcct']);
  echo $RequestID;
  $sql = $db->prepare("DELETE FROM users WHERE idusers = $UserID;");
  $result = $sql-> execute();
  $sql -> close();
  header("location: adminAccounts.php");
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
    <link rel="stylesheet" href="../../css/styleAdmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>KBNB KARS</title>

</head>
<body class="text-white bgFull" id="AdminBack">
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

            <div class="row mb-1 ">
                <div class="text-center col">
                  <p class="h1"><strong>Accounts</strong></p>
                </div>
            </div>
            
          <div style="max-height: 550px; overflow: auto; width: 100%; overflow-x: hidden;">

            <!-- Table of Accounts -->
            <table class="table table-dark table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Account Type</th>
                  <th scope="col"></th>
                  <th scope="col"></th>
                </tr>
              </thead>
            <tbody>

            <!-- Retrieving and Displaying Account Info from DB -->
              <?php 
                $stmt = $db->prepare("SELECT * FROM users");
                $stmt -> execute();
                $stmt ->store_result();
                $stmt->bind_result($id, $nme, $pword, $eml, $typ, $amtUnpd, $adrs);
                
                if($stmt){

                  while($row = $stmt -> fetch()){
                    echo"<tr>
                    <th scope=\"row\">";
                    if($id>0){
                      echo "$id</th>";}
                    echo "
                    <td>$nme</td>
                    <td>$eml</td>
                    <td>";
                      if($typ==0){
                        echo "User";
                      }
                      else if($typ==1){
                        echo "Driver";
                      }
                      else{
                        echo "Admin";
                      }
                    echo "</td>
                    <td>";

                    // Displaying Account Type, Dropdown Selection Menu, and Submission Button
                    if($id>0){
                      echo "<form action=\"\" method=\"post\">
                      <div class=\"row\">
                        <div class=\"container-fluid align-middle col\">
                        <select class=\"form-select\" name=\"AccountChange\" aria-label=\"Select Menu\">
                          <option selected>Manage Type</option>
                          <option value=\"0\">User</option>
                          <option value=\"1\">Driver</option>
                          <option value=\"2\">Admin</option>
                        </select>
                        </div>
                        <div class=\"col d-grid\">
                          <button type=\"submit\" name=\"submit\" value=\"$id\"class=\"btn btn-success\">Apply</button>
                        </div>
                    </div>
                  </form>";}

                  // Delete Account Button
                    echo "</td>";
                    if($id>0){
                      echo "<td> <form action=\"\" method=\"post\">
                                  <div class=\"col d-grid\">
                                      <button type=\"submit\" name=\"DeleteAcct\" value=\"$id\"class=\"btn btn-danger\">Delete</button>
                                  </div>
                            </form>";}
                            else{
                              echo " <td><h1> </h1>";
                            }
                  echo "</td>"; 
                  echo "</tr>";
                  }
                }
                   ?>
                 </tbody>
                </table>
              </div>


          <!-- Button Linking to Register Page -->
          <div class="row mt-2">
              <div class="text-center col">
                <p class="h2"><strong>Register New Account</strong></p>
              </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="d-grid gap-2">
                <a href="../account/register.php" class="btn btn-success btn-lg" role="button" >Register</a>
              </div>
            </div>
          </div>

        <!-- Return Home Button -->
          <div class="row pt-2">
            <div class="col">
              <div class="d-grid gap-2">
                <a href="../../index.php" class="btn btn-danger" role="button" >Exit</a>
              </div>
            </div>
          </div>
      </div>
    </div>

                    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>