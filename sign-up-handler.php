<?php
    include_once("db-connection.php");

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $mysqli->real_escape_string($_POST["name"]);
        $email = $mysqli->real_escape_string($_POST["email"]);
        $password = $mysqli->real_escape_string($_POST["password"]);
        $confirm_password = $mysqli->real_escape_string($_POST["confirm_password"]);

        if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
            $url="Location: sign-up.php?error=empty";
            $url .= empty($name) ? "" : "&name=$name";
            $url .= empty($email) ? "" : "&email=$email";
            header($url);
            exit();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: sign-up.php?error=wrong_email&name=$name");
            exit();
        }

        if(strlen($password) < 8){
            header("Location: sign-up.php?error=password_short&name=$name&email=$email");
            exit();
        }
        if($password !== $confirm_password){
            header("Location: sign-up.php?error=passwords_not_match&name=$name&email=$email");
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
            header("Location: sign-up.php?error=email_taken&name=$name");
            exit();
        }

        header("Location: login.php?succes=signup");
        exit();
    } else {
        header("Location: sign-up.php");
        exit();
    }