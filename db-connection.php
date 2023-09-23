<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "e-commerce project";

    $mysqli = null;

    try {
        $mysqli = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    } catch(mysqli_sql_exception) {
        die("could not connect to database!");
    }