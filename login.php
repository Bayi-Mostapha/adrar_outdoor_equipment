<?php 
    include_once("db-connection.php");
    $email = "";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $mysqli->real_escape_string($_POST["email"]);
        $password = $mysqli->real_escape_string($_POST["password"]);

        if(empty($email) || empty($password)){
            $url = "Location: login.php?error=empty";
            $url .= empty($email) ? "" : "&email=$email";
            header($url);
            exit();
        }

        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("s", $email);
        try{
            $stmt->execute();
        } catch(mysqli_sql_exception) {
            header("Location: login.php?error=invalid_email");
            exit();
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if($row){
            if (password_verify($password, $row["password"])){
                session_start();
                $_SESSION["id"] = $row["id"];
                $_SESSION["name"] = $row["name"];
                $_SESSION["is_admin"] = false;
                header("Location: home.php");
                exit();
            } else {
                header("Location: login.php?error=wrong_login&email=$email");
                exit();
            }
        } else {
            header("Location: login.php?error=email_notexist");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles\general.css">
    <link rel="stylesheet" href="styles\login-signup.css">
    <title>login</title>
</head>
<body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["error"])){
                $error = $_GET["error"];
                if($error == "empty"){
                    echo "<div class=\"errors\"><p class=\"error\">all fields must be filled</p></div>";
                } elseif($error == "invalid_email"){
                    echo "<div class=\"errors\"><p class=\"error\">email not valid</p></div>";
                } elseif($error == "wrong_login"){
                    echo "<div class=\"errors\"><p class=\"error\">wrong login information</p></div>";
                } elseif($error == "email_notexist"){
                    echo "<div class=\"errors\"><p class=\"error\">an account with this email does not exist, <a href=\"sign-up.php\">sign up</a></p></div>";
                } else {
                    header("Location: login.php");
                    exit();
                }
            }
            if(isset($_GET["email"])){
                $email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
    ?>
    <form action="login.php" method="post" novalidate>
        <h1>login</h1>
        <div class="form-row">
            <label for="email">email:</label>
            <input type="email" name="email" id="email" value=<?php echo $email; ?>>
        </div>
        <div class="form-row">
            <label for="password">password:</label>
            <input type="password" name="password" id="password">
        </div>
        <button type="submit" class="submit mb-btn">login</button>
    </form>
</body>
</html>