<?php
    session_start();
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }
    session_unset();
    session_destroy();
    header("Location: ../home.php");
    exit();