<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $user_id = $mysqli->real_escape_string($_SESSION["id"]);
        $categorie = $mysqli->real_escape_string($_POST["categorie"]);
        $product_id = $mysqli->real_escape_string($_POST["product_id"]);
        $color = $mysqli->real_escape_string($_POST["color"]);
        $quantity = $mysqli->real_escape_string($_POST["quantity"]);

        if(empty($user_id)){
            header("Location: ../login.php");
            exit();
        }
        if(empty($categorie)){
            header("Location: ../home.php");
            exit();
        }
        if(empty($product_id)){
            header("Location: categorie-products.php?categorie=$categorie");
            exit();
        }
        if(empty($color) || empty($quantity)){
            header("Location: product.php?id=$product_id&error=empty");
            exit();
        }
        if (!filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT)) {
            header("Location: product.php?id=$product_id&error=invalid_quantity");
            exit();
        }

        $sql = "INSERT INTO cart (user_id, product_id, color, quantity) VALUES (?, ?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("iisi", $user_id, $product_id, $color, $quantity);
        
        try {
            $stmt->execute();
        } catch(mysqli_sql_exception) {
            header("Location: product.php?id=$product_id&error=product_in_cart");
            exit();
        }

        header("Location: product.php?id=$product_id&succes=add_to_cart");
        exit();
    } else {
        header("Location: ../home.php");
        exit();
    }