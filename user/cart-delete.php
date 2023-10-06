<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);
        $user_id = $mysqli->real_escape_string($_SESSION["id"]);
        $color = $mysqli->real_escape_string($_GET["color"]);
        $quantity = $mysqli->real_escape_string($_GET["quantity"]);

        if(empty($id) || empty($user_id) || empty($color) || empty($quantity)){
            header("Location: cart.php");
            exit();
        }

        $sql = "DELETE FROM cart WHERE product_id=? AND user_id=? AND color=? AND quantity=?;";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("iiss", $id, $user_id, $color, $quantity);
        $stmt->execute();
        header("Location: cart.php?succes=remove");
        exit();
    }else{
        header("Location: cart.php");
        exit();
    }