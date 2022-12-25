<?php
// Link your Database Configuration File Here OR Define your Database Configuration here

            //require_once("/filepath/DBConfig.php");

            // define('DBSERVER', '');
            // define('DBUSERNAME', '');
            // define('DBPASSWORD', '');
            // define('DBNAME', '');

            // /*connect to MYSQL DB*/

            // $db = new mysqli(DBSERVER, DBUSERNAME, DBPASSWORD, DBNAME);

            //require_once("/home/bekovach/DBConfig.php");

            define('DBSERVER', 'localhost');
            define('DBUSERNAME', 'root');
            define('DBPASSWORD', '');
            define('DBNAME', 'bekovach');

            /*connect to MYSQL DB*/

            $db = new mysqli(DBSERVER, DBUSERNAME, DBPASSWORD, DBNAME);

?>