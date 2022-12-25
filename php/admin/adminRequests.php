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

// Assign Chosen Request to Driver in DB
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  $DrivID = $_POST['submit'];
  $sql = $db->prepare("INSERT INTO assignments (idRequests, idUser) VALUES (?, ?);");
  $sql -> BIND_PARAM("ii", $_GET['ReqID'], $DrivID);
  $result = $sql-> execute();
  $sql->close();
  header("location: adminRequests.php");
  exit;
}

// Removes Requests fromt the DB
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['DeleteReq'])) {
  $RequestID = $_GET['ReqID'];
  $sql = $db->prepare("DELETE FROM requests WHERE idRequests = $RequestID;");
  $result = $sql-> execute();
  $sql -> close();
  header("location: adminRequests.php");
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
          
<!-- Conditionally displays Content based on whether the page has a GET value for a request ID. This is given whenever a
            Request is chosen. That code is in the large ELSE below-->
<?php
  if(isset($_GET['ReqID'])){
      echo "<div class=\"text-center col\">
      <p class=\"h1\"><strong>Drivers</strong></p>
      </div>
      <div class=\"table-responsive\" style=\"max-height: 550px; min-height: 200px; overflow: auto; width: 100%; overflow-x: hidden;\">
      <table class=\"table table-dark table-striped table-hover col\">
      <thead>
        <tr>
          <th scope=\"col\">ID</th>
          <th scope=\"col\">Driver</th>
          <th scope=\"col\">Current Assignments</th>
          <th scope=\"col\"></th>
        </tr>
      </thead>
      <tbody>";

      // Retrieves Data about the Request and the Drivers who can be assigned to it. Then displays this Information in a table.
      $tempReqID = $_GET['ReqID'];
      $stmt = $db->prepare("SELECT * FROM users WHERE (userType =1) AND idusers NOT IN
                                (SELECT idUser FROM assignments WHERE idRequests IN
                                (SELECT idRequests FROM requests WHERE date IN
                                (SELECT date FROM requests WHERE idRequests = $tempReqID)));");
      $stmt -> execute();
      $stmt ->store_result();
      $stmt->bind_result($id, $nme, $pword, $eml, $typ, $amtUnpd, $adrs);
      if($stmt){
        while($row = $stmt -> fetch()){
          echo "<tr>
          <th scope=\"row\">$id</th>
          <td>$nme</td>
          <td>
          <div class=\"dropdown container-flex\">
          
          <button class=\"btn btn-secondary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton2\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">View Assignments</button>
          <ul class=\"dropdown-menu dropdown-menu-dark\" aria-labelledby=\"dropdownForTimes\">";

          // Filters out Drivers who have an assignment at the same exact time as the request
          $newstmt = $db->prepare(" SELECT date FROM requests where (idRequests IN (SELECT idRequests from assignments where assignments.idUser=$id)) AND (fulfilled = 0)");
          $newstmt -> execute();
          $newstmt ->store_result();
          $newstmt->bind_result($time);
          if($newstmt){
            while($thing = $newstmt -> fetch()){
              $newtime =date("m-d-y h:i A", strtotime($time));
              echo "<li><a class=\"dropdown-item active\" >$newtime</a></li>";
            }
          }
          echo "</ul>
                </div>
              </td>
          <td> <form action=\"\" method=\"post\"><div class=\"d-grid gap-2\"><button type=\"submit\" name=\"submit\" value=\"$id\"class=\"btn btn-success\">Assign</button></div></form></td>
          </tr>";}
          echo "</tbody> </table>";
      }
  }

  // Displays Table of Open Request
  else{
    echo "<div class=\"text-center col mb-3\">
          <p class=\"h1\"><strong>Requests</strong></p>
          </div>
    <div class=\"table-responsive\" style=\"max-height: 550px; min-height: 200px; overflow: auto; width: 100%; overflow-x: hidden;\">
    <table class=\"table table-dark table-striped table-hover col\" >
      <thead>
        <tr>
          <th scope=\"col\">ID</th>
          <th scope=\"col\">Pickup</th>
          <th scope=\"col\">Destination</th>
          <th scope=\"col\">Date</th>
          <th scope=\"col\">Time</th>
          <th scope=\"col\">Wheelchair</th>
          <th scope=\"col\">Passenger</th>
          <th scope=\"col\"></th>
        </tr>
      </thead>
      <tbody>";

      // Retrieves and Displays Open Requests from DB
      $stmt = $db->prepare("SELECT *
                          FROM requests
                          WHERE (fulfilled=0)
                          AND (idRequests NOT IN (Select idRequests FROM assignments));");
      $stmt -> execute();
      $stmt ->store_result();
      $stmt->bind_result($Rid, $Uid, $pickup, $destination, $datetime, $WCR, $fulfilled);
      if($stmt){
        while($row = $stmt -> fetch()){
                
          $func = $db->prepare("SELECT name FROM users where idusers=$Uid;");
          $func -> execute();
          $func ->store_result();
          $func ->bind_result($name);
          $func -> fetch();

          $newdate =date("m-d-y", strtotime($datetime));
          $newtime =date("h:i A", strtotime($datetime));

          echo "<tr>
          <th scope=\"row\">$Rid</th>
          <td>$pickup</td>
          <td>$destination</td>
          <td>$newdate</td>
          <td>$newtime</td>
          <td>";
          if($WCR==0){
            echo "No";
          }
          else{
            echo "Yes";
          }

          // Button to Choose Request to Manage. GETs value for the page, which causes a visual update
          echo "</td>
          <td>$Uid: $name</td>
          <td><form action=\"adminRequests.php\" method=\"GET\">
              <div class=\"d-grid gap-2\">
              <button type=\"submit\" name=\"ReqID\" value=\"$Rid\"class=\"btn btn-success\">Manage</button>
              </div>
          </form></td>
        </tr>";
        }
      }
  }
      echo "</tbody>
      </table>";?>
        </div>

          <div class="row pt-4">
            <div class="col">
              <div class="d-grid gap-2">

              <!-- Conditionally Displays Cancel and Delete Request Button based on whether a GET value has been given or not -->
                <?php 
                  if(isset($_GET['ReqID'])){
                    echo "
                          <form action=\"\" method=\"POST\">
                          <div class=\"d-grid gap-2 container\">
                          <button type=\"submit\" name=\"DeleteReq\" value=\"\"class=\"btn btn-warning btn-lg mb-4\">Delete Request</button>
                          </div>
                          </form>
                    <a href=\"adminRequests.php\" class=\"btn btn-danger\" role=\"button\" >Cancel</a>";
                  }
                  else{
                    echo "<a href=\"adminAssignedRequests.php\" class=\"btn btn-primary\" role=\"button\" >View Currently Assigned Requests</a>";
                    echo "<a href=\"adminHistoricalRequests.php\" class=\"btn btn-secondary\" role=\"button\" >View Historical Requests</a>";
                    echo "<a href=\"../../index.php\" class=\"btn btn-danger\" role=\"button\" >Cancel</a>";
                  }
                ?>
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