<?php
//Database Connection and Session Creation
require_once "../database/session.php";
require_once "../database/config.php";

//Check if the user is not logged in, then redirect the user to login page
if(!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: ../account/login.php");
  exit;
}

//Inserting Request into Table
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

  $PickupAddress = ltrim($_POST['inputAddress']);
    if(empty($PickupAddress)){
      $PickupAddress = $_SESSION["address"];
    }
  $Destination = ltrim($_POST['destination']);
  $date = ltrim($_POST['date']);
  $time = ltrim($_POST['time']);
  $datetime = $date." ".$time;
  $wheelchair = trim($_POST['wheelchairReq']);
  $sql = $db->prepare("INSERT INTO requests (idUser, pickupLocation, destination, date, WCRequest) VALUES (?, ?, ?, ?, ?);");
  $sql -> BIND_PARAM("isssi", $_SESSION['userid'], $PickupAddress, $Destination, $datetime, $wheelchair);
  $result = $sql-> execute();
  $sql->close();
  $_SESSION['DisplayPickup'] = $PickupAddress;
  $_SESSION['DisplayDate'] = date("m-d-y", strtotime($datetime));
  $_SESSION['DisplayTime'] = date("h:i A", strtotime($datetime));
  header("location: requestResponse.php");
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
    <link rel="stylesheet" href="../../css/styleRequests.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

  <!-- Disable Pickup Location Input if Home Address is Checked -->
    <title>KBNB KARS</title>
    <script type="text/javascript">
        function disableInput() {
          var chkYes = document.getElementById("DefaultToHomeID");
          var destinationInput = document.getElementById("inputID");
          destinationInput.disabled = !chkYes.checked ? false : true;
          if (!destinationInput.disabled) {
            destinationInput.focus();
          }
        }
    </script>

   


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
      <div class="container-fluid ">
        <div class="centering-input">
        <!-- Request Form -->
         <form method="post" class="row g-3" id="form1">
         <!-- Pickup -->
          <div class="row">
            <label for="inputAddress" class="form-label" >Pick-up Location</label>
            <input type="text" class="form-control" name="inputAddress" id="inputID" placeholder="Ex: '1234 Main St, City State Zip'" required>
          </div>
          <!-- Home Address Checkbox -->
          <?php if(null!=$_SESSION["address"]){
            echo "<div class=\"form-check ms-5\">
            <input class=\"form-check-input\" type=\"checkbox\" name=\"DefaultToHome\" id=\"DefaultToHomeID\" oninput=\"disableInput()\">                
            <label class=\"form-check-label\" for=\"DefaultToHome\" >Home Address</label>
          </div>";
          }?>
            <!-- Destination -->
          <div class="row mb-3 mt-4">
            <label for="destination" class="form-label">Destination</label>
            <input type="text" class="form-control" name="destination" id="destinationID" placeholder="Ex: '6789 Lamar Rd, City State Zip'" required>
          </div>
              <!-- Wheelchair Selection -->
          <div class="row mb-3 mt-3">
          <label for="wheelchairReq" class="form-label">Wheelchair Access</label>
          <select class="form-select" name="wheelchairReq" aria-label="Select Menu">
            <option selected value="0">Unnecessary</option>
            <option value="1">Preferred</option>
          </select>
          </div>
                <!-- Date/Time -->
          <div class="row g-3 mb-3">
            <div class="col align-self-center">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" placeholder="mm/dd/yyyy" required>
            </div>
            <div class="col align-self-center">
              <label for="" class="form-label">Time</label>
              <input type="time" class="form-control" name="time" placeholder="Ex: 12:30 pm" required>
              </div>
          </div>
            <!-- Submission -->
            <div class="col align-self-center">
                <div class="d-grid gap-2">
                    <button type="submit" name="submit" value="submit" class="btn btn-success" >Request</button>
                </div>
            </div>
        </form>
            <!-- Cancel -->
        <div class="row">
          <div class="col d-grid gap-2 mt-2">
            <a href="../../index.php" class="btn btn-danger" role="button" >Cancel</a>
          </div>
        </div>
        
      </div>
    </div>

           <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>