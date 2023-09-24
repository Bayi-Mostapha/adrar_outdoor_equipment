<?php
    session_start();
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    session_unset();
    session_destroy();
    header("Location: ../admin-login.php");
    exit();