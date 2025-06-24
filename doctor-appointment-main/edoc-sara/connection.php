<?php

    $database= new mysqli("localhost","root","","edoc_db");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>