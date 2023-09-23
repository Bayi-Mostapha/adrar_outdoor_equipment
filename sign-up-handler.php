<?php
    include_once("db-connection.php");

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        if(empty($name)){
            header("Location: sign-up.html?error=empty_name");
            exit();
        }

        if(empty($email)){
            header("Location: sign-up.html?error=empty_email");
            exit();
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: sign-up.html?error=wrong_email");
            exit();
        }

        if(strlen($password) < 8){
            header("Location: sign-up.html?error=password_short");
            exit();
        }
        if($password !== $confirm_password){
            header("Location: sign-up.html?error=passwords_not_match");
            exit();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("sss", $name, $email, $hash);
        try{
            $stmt->execute();
        } catch(mysqli_sql_exception) {
            header("Location: sign-up.html?error=email_taken");
            exit();
        }

        header("Location: login.php?succes=signup");
        exit();
    } else {
        header("Location: sign-up.html");
        exit();
    }