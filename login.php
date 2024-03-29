<?php 
    include_once("db-connection.php");
    $email = "";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = strtolower($mysqli->real_escape_string($_POST["email"]));
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
            header("Location: login.php?error=unknown");
            exit();
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if($row){
            if (password_verify($password, $row["password"])){
                session_start();
                session_regenerate_id(true);
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
    <form action="login.php" method="post" novalidate>
        <h1>login</h1>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["error"])){
                    $error = $_GET["error"];
                    if($error == "empty"){
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">all fields must be filled</p>
                        </div>";
                    } elseif($error == "invalid_email"){
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">email not valid</p>
                        </div>";
                    } elseif($error == "wrong_login"){
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">wrong login information</p>
                        </div>";
                    } elseif($error == "email_notexist"){
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">an account with this email does not exist, <a href=\"sign-up.php\">sign up</a></p>
                        </div>";
                    } elseif($error == "unknown") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">unknown error, please try again later</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } else {
                        header("Location: login.php");
                        exit();
                    }
                } elseif(isset($_GET["succes"])){
                    $succes = $_GET["succes"];
                    if($succes == "signup"){
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">signed up succesfully</p>
                        </div>";
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
        <div class="form-row">
            <label for="email">email:</label>
            <input type="email" name="email" id="email" value=<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>>
        </div>
        <div class="form-row">
            <label for="password">password:</label>
            <input type="password" name="password" id="password">
        </div>
        <button type="submit" class="submit mb-btn">login</button>
        <p class="login-signup-link">don't have an account? <a href="sign-up.php">sign up</a></p>
    </form>
    <?php include "componants/icons.php"; ?>
    <script src="js/general.js"></script>
</body>
</html>