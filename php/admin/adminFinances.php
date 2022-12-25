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

//Updating Amount to be Paid for Driver
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  $AmountPaid = trim($_POST['inputAmount']);
  $userID = trim($_POST['inputID']);
  $sql = $db->prepare("UPDATE users SET amountUnpaid = (amountUnpaid - $AmountPaid) WHERE idusers = $userID;");
  $result = $sql-> execute();
  $sql -> close();
  header("location: adminFinances.php");
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
      <div class="container-fluid ">
        <div class="centering-input ">


            <div class="row mb-4 ">
                <div class="text-center col">
                  <p class="h1"><strong>Employee Payroll</strong></p>
                </div>
            </div>
            
            <!-- Employee Payroll Table -->
            <div style="max-height: 480px; overflow: auto; width: 100%; overflow-x: hidden;" class="mb-2">
            <table class="table table-dark table-striped table-hover col">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Name</th>
                  <th scope="col">Unpaid Wages</th>
                </tr>
              </thead>
              <tbody>

              <!-- Retrieving Payroll Values and Displaying them -->
              <?php
                 $stmt = $db->prepare("SELECT * FROM users WHERE userType = 1");
                 $stmt -> execute();
                 $stmt ->store_result();
                 $stmt->bind_result($id, $nme, $pword, $eml, $typ, $amtUnpd, $adrs);
                 
                 if($stmt){
                   while($row = $stmt -> fetch()){
                    $amtUnpd = round($amtUnpd, 2);
                    echo "
                    <tr>
                    <th scope=\"row\">$id</th>
                    <td>$nme</td>
                    <td>$$amtUnpd</td>
                    </tr>
                    ";
                   }
                 }
              ?>
              </tbody>
            </table>
          </div>

          <!-- Form for updating Employee Earnings -->
          <form action="" method="post">
            <div class="row">
              <div class="col">
                <div class="mb-3">
                  <label for="inputPassengers" class="form-label">Driver ID #</label>
                  <input type="number" class="form-control" name="inputID" placeholder="Ex: 23" required>
                </div>
              </div>
              <div class="col">
                <div class="mb-3">
                  <label for="inputPassengers" class="form-label">Amount Being Paid</label>
                  <input type="number" class="form-control" name="inputAmount" placeholder="Ex: 360.75" step=".01" required>
                </div>
              </div>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-success btn-lg mb-3" name="submit">Apply</button>
            </div>
          </form>

          <!-- Return to the Home Page Button -->
          <div class="row pt-2">
            <div class="col">
              <div class="d-grid gap-2">
                <a href="../../index.php" class="btn btn-danger" role="button" >Cancel</a>
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