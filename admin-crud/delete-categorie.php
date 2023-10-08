<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);
        $categorie_name = "";

        $stmt = $mysqli->stmt_init();
        $sql = "SELECT * FROM categories WHERE id = ?";
        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            header("Location: ../admin.php?error=unknown");
            exit();
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $categorie_name = $row["categorie_name"];
        } else {
            header("Location: ../admin.php");
            exit();
        }

        $sql = "SELECT * FROM products WHERE categorie = ?";
        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("s", $categorie_name);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            header("Location: ../admin.php?error=unknown");
            exit();
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: ../admin.php?error=products_in_categorie");
            exit();
        } else {
            $sql_img = "SELECT * FROM categories WHERE id = ?";
            $img_stmt = $mysqli->stmt_init();
            if(!$img_stmt->prepare($sql_img)){
                die("SQL error: " . $mysqli->error);
            }
            $img_stmt->bind_param("i", $id);
            try{
                $stmt->execute();
            }catch(mysqli_sql_exception){
                header("Location: ../admin.php?error=unknown");
                exit();
            }
            $img_result = $img_stmt->get_result();
            if ($img_result->num_rows > 0) {
                $img_row = $img_result->fetch_assoc();
                $categorie_img = $img_row["image"];
            }
            $path = "../uploads/$categorie_img";
            if(!unlink($path)){
                header("Location: ../admin.php?error=undeleted_img");
                exit();
            }

            $sql = "DELETE FROM categories WHERE id=?;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("i", $id);
            try{
                $stmt->execute();
            }catch(mysqli_sql_exception){
                header("Location: ../admin.php?error=unknown");
                exit();
            }
        }

        header("Location: ../admin.php?succes=delete-categorie");
        exit();
    } else {
        header("Location: ../admin.php");
        exit();
    }