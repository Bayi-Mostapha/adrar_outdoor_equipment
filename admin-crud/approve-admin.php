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
        $name = "";
        $email = "";
        $password = "";

        $sql = "SELECT * FROM admin_requests WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            header("Location: ../admin.php");
            exit();
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $name = $row["name"];
            $email = $row["email"];
            $password = $row["password"];
        } else {
            header("Location: ../admin.php");
            exit();
        }

        $sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("sss", $name, $email, $password);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            header("Location: ../admin.php?error=admin_not_approved");
            exit();
        }

        $sql = "DELETE FROM admin_requests WHERE id=?;";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            //a code that sends error to my email
            header("Location: ../admin.php");
            exit();
        }
        
        header("Location: ../admin.php?succes=admin_approved");
        exit();
    } else {
        header("Location: ../admin.php");
        exit();
    }