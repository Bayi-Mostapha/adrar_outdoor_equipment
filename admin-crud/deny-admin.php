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
        
        $sql = "DELETE FROM admin_requests WHERE id=?;";
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
        
        header("Location: ../admin.php?succes=admin_denied");
        exit();
    } else {
        header("Location: ../admin.php");
        exit();
    }