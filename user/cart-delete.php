<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);

        if(empty($id)){
            header("Location: cart.php");
            exit();
        }

        $sql = "DELETE FROM cart WHERE product_id=? AND user_id=$_SESSION[id];";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: cart.php?succes=remove");
        exit();
    }else{
        header("Location: cart.php");
        exit();
    }