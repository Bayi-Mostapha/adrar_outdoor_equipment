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
    <title>login</title>
</head>
<body>
    <div class="errors">
        <p class="error">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "GET"){
                    if(isset($_GET["error"])){
                        $error = $_GET["error"];
                        if(isset($error)){
                            if($error == "empty"){
                                echo "all fields must be filled";
                            } elseif($error == "invalid_email"){
                                echo "email not valid";
                            } elseif($error == "wrong_login"){
                                echo "wrong login information";
                            } elseif($error == "email_notexist"){
                                echo "an account with this email does not exist, <a href=\"sign-up.php\">sign up</a>";
                            }
                        }
                    }
                    if(isset($_GET["email"])){
                        $email = $_GET["email"];
                    }
                }
            ?>
        </p>
    </div>
    <form action="login.php" method="post" novalidate>
        <div>
            <label for="email">email:</label>
            <input type="email" name="email" id="email" value=<?php echo $email; ?>>
        </div>
        <div>
            <label for="password">password:</label>
            <input type="password" name="password" id="password">
        </div>
        <button type="submit">login</button>
    </form>
</body>
</html>