<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);
        $sql = "DELETE FROM products WHERE id=?;";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: ../admin.php");
        exit();
    } else {
        header("Location: ../admin.php");
        exit();
    }