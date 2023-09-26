<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $product_id = $mysqli->real_escape_string($_GET["product_id"]);
        $user_id = $_SESSION["id"];

        if(empty($product_id)){
            header("Location: ../home.php");
            exit();
        }

        $sql = "INSERT INTO cart (user_id, product_id) VALUES (?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ii", $user_id, $product_id);
        
        try {
            $stmt->execute();
        } catch(mysqli_sql_exception) {
            header("Location: ../home.php?error=product_in_cart");
            exit();
        }

        header("Location: ../home.php?succes=add_to_cart");
        exit();
    } else {
        header("Location: ../home.php");
        exit();
    }
?>