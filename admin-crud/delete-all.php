<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }

    $categorie =  $mysqli->real_escape_string($_GET["categorie"]);

    $sql = "SELECT * FROM products WHERE categorie = ?";
    $img = $mysqli->stmt_init();
    if(!$img->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $img->bind_param("s", $categorie);
    $img->execute();
    $result = $img->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            $product = $row["product_img"];
            $path = "../uploads/$product";
            if(!unlink($path)){
                header("Location: ../admin.php?error=undeleted_img");
                exit();
            }

            $sql2 = "DELETE FROM products WHERE id = ?;";
            $stmt2 = $mysqli->stmt_init();
            if(!$stmt2->prepare($sql2)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt2->bind_param("i", $row["id"]);
            $stmt2->execute();

            $sql3 = "DELETE FROM cart WHERE product_id=?;";
            $stmt3 = $mysqli->stmt_init();
            if(!$stmt3->prepare($sql3)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt3->bind_param("i", $row["id"]);
            $stmt3->execute();

            $sql4 = "DELETE FROM colors WHERE product_id=?;";
            $stmt4 = $mysqli->stmt_init();
            if(!$stmt4->prepare($sql4)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt4->bind_param("i", $row["id"]);
            $stmt4->execute();
        }
    }

    header("Location: ../admin.php?succes=delete");
    exit();