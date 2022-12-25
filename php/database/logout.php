<?php
//Start the session
session_start();

//Destroy the Session
if(session_destroy()){
    //redirect to the login page
    header("Location: ../../index.php");
    exit;
}
?>