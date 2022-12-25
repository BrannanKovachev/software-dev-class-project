<?php
//Database Connection and Session Creation
require_once "../database/session.php";
require_once "../database/config.php";

//Check if the user is not logged in, then redirect the user to login page
if(!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
  header("location: ../account/login.php");
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
    <link rel="stylesheet" href="../../css/styleDriver.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>KBNB KARS</title>

</head>
<body class="text-white bgFull" id="DriverBack">
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
          
    <!-- Conditionally Displaying Page Elements based upon Account Type -->
        <?php 
        if(isset($_SESSION["status"]) && $_SESSION["status"]>0){

          // Displaying Table of Completed Assignments
        echo "
        <div class=\"text-center\">
          <p class=\"h1\"><strong>Completed Assignments</strong></p>
          </div>
          <div class=\"table-responsive-sm\">
          <div class=\"table-responsive\" style=\"max-height: 550px; overflow: auto; width: 100%; overflow-x: hidden;\">
          <table class=\"table table-dark table-striped table-hover\">
            <thead>
              <tr>
                <th scope=\"col\">ID</th>
                <th scope=\"col\">Pickup</th>
                <th scope=\"col\">Destination</th>
                <th scope=\"col\">Date</th>
                <th scope=\"col\">Time</th>
                <th scope=\"col\">Wheelchair</th>
                <th scope=\"col\">Passenger</th>
                <th scope=\"col\">Distance</th>
                <th scope=\"col\">Time</th>
              </tr>
            </thead>
            <tbody>";

            // Retrieving Table Data from DB, and Displaying it
            $tempSessinUserID = $_SESSION["userid"]; 
            $stmt = $db->prepare("SELECT * FROM requests WHERE 
                                  idRequests IN                                   
                                  (SELECT idRequests FROM assignments WHERE idUser = $tempSessinUserID) AND fulfilled = 1");// CHANGE THE 5 to tempSessinUserID
            $stmt -> execute();
            $stmt ->store_result();
            $stmt->bind_result($Rid, $Uid, $pickup, $destination, $datetime, $WCR, $fulfilled);

            if($stmt){
              while($row = $stmt -> fetch()){
                      
                $func = $db->prepare("SELECT name FROM users where idusers = $Uid;");
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
                echo "</td>
                <td>$name</td>";
                $stmt2 = $db->prepare("SELECT distanceDriven, timeSpent FROM assignments WHERE (idRequests = $Rid);");
                $stmt2 -> execute();
                $stmt2 ->store_result();
                $stmt2->bind_result($distanceDriven, $timeSpent);
                $stmt2 -> fetch();
                $timeSpent = $timeSpent*60;
                echo"<td>$distanceDriven</td> <td>$timeSpent</td>";

              echo"</tr>";
              }
            }
            echo "</tbody>
          </table>
          </div>
        </div>";
        
        // Displaying Cancel/Return to the Last Page Buttons
        echo "<div class=\"row\">
          <div class=\"col pt-2\">
          <div class=\"d-grid gap-2\">
              <a href=\"driversHome.php\" class=\"btn btn-primary\" role=\"button\" >Return to Current Assignments</a>
              <a href=\"../../index.php\" class=\"btn btn-danger\" role=\"button\" >Cancel</a>
              </div>
              </div>
              </div>";
        }
      // Alternative Webpage display should a normal User somehow reach this page improperly
        else{
          echo "
          <div class=\"row mb-4\">
          <div class=\"text-center\">
            <p class=\"h1\"><strong>Sorry!</strong></p>
            <p class=\"h1\"><em><u>You do not have access to this page</u></em></p>
            <br></br>
            <p class=\"h2\"><strong>If you would like to become a driver, send your application to:</strong></p>
            <p class=\"h4\"><em>driverapplications@kbnbkars.com</em></p>
        </div>
      </div>
          ";
        }

        ?>
      </div>
    </div>

      <!-- Bootstrap Scripts --> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

</body>
</html>