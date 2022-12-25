<?php
//Start the session
session_start();

// If the session was started correctly, the user is brought to the Home Page
if (isset($_SESSION["userid"]) && $_SESSION["userid"] === true){ 
    header("location: index.php");
    exit;
}

// Times out the Session after an hour
if(isset($_SESSION["userid"])) 
{
    if(time()-$_SESSION["login_time_stamp"] >3600)  
    {
        session_unset();
        session_destroy();
        header("Location:login.php");
    }
}
?>