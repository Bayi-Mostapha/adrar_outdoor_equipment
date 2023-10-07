<?php
    include_once("db-connection.php");

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = strtolower($mysqli->real_escape_string($_POST["name"]));
        $email = strtolower($mysqli->real_escape_string($_POST["email"]));
        $password = $mysqli->real_escape_string($_POST["password"]);
        $confirm_password = $mysqli->real_escape_string($_POST["confirm_password"]);

        if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
            $url="Location: admin-sign-up.php?error=empty";
            $url .= empty($name) ? "" : "&name=$name";
            $url .= empty($email) ? "" : "&email=$email";
            header($url);
            exit();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: admin-sign-up.php?error=wrong_email&name=$name");
            exit();
        }

        $sql = "SELECT email FROM admins WHERE email=?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if($row){
            header("Location: admin-sign-up.php?error=email_taken&name=$name");
            exit();
        }

        if(strlen($password) < 8){
            header("Location: admin-sign-up.php?error=password_short&name=$name&email=$email");
            exit();
        }
        if($password !== $confirm_password){
            header("Location: admin-sign-up.php?error=passwords_not_match&name=$name&email=$email");
            exit();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO admin_requests (name, email, password) VALUES (?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("sss", $name, $email, $hash);
        try{
            $stmt->execute();
        } catch(mysqli_sql_exception) {
            header("Location: admin-sign-up.php?error=email_taken&name=$name");
            exit();
        }

        header("Location: admin-login.php?succes=signup");
        exit();
    } else {
        header("Location: admin-sign-up.php");
        exit();
    }