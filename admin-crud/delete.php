<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);

        $sql_img = "SELECT * FROM products WHERE id = ?";
        $img_stmt = $mysqli->stmt_init();
        if(!$img_stmt->prepare($sql_img)){
            die("SQL error: " . $mysqli->error);
        }
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        if ($img_result->num_rows > 0) {
            $img_row = $img_result->fetch_assoc();
            $product_img = $img_row["product_img"];
        }
        $path = "../uploads/$product_img";
        if(!unlink($path)){
            header("Location: ../admin.php?error=undeleted_img");
            exit();
        }

        $sql = "DELETE FROM products WHERE id=?;";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: ../admin.php?succes=delete");
        exit();
    } else {
        header("Location: ../admin.php");
        exit();
    }