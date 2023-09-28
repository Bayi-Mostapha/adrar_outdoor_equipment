<?php
    $name = "";
    $email = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles\general.css">
    <link rel="stylesheet" href="styles\login-signup.css">
    <title>sign up</title>
</head>

<body>
    <form action="./sign-up-handler.php" method="post" novalidate>
        <h1>sign up</h1>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["error"])){
                    $error = $_GET["error"];
                    if($error == "empty"){
                        echo "<div class=\"errors\"><p class=\"error\">all fields must be filled</p></div>";
                    } elseif($error == "wrong_email"){
                        echo "<div class=\"errors\"><p class=\"error\">email not valid</p></div>";
                    } elseif($error == "password_short"){
                        echo "<div class=\"errors\"><p class=\"error\">password must contain at least 8 characters</p></div>";
                    } elseif($error == "passwords_not_match"){
                        echo "<div class=\"errors\"><p class=\"error\">passwords do not match</p></div>";
                    } elseif($error == "email_taken"){
                        echo "<div class=\"errors\"><p class=\"error\">email already taken</p></div>";
                    } else {
                        header("Location: sign-up.php");
                        exit();
                    }
                }
                if(isset($_GET["name"])){
                    $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_SPECIAL_CHARS);
                }
                if(isset($_GET["email"])){
                    $email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        ?>
        <div>
            <label for="name">name:</label>
            <input type="text" name="name" id="name" value=<?php echo $name; ?>>
        </div>
        <div>
            <label for="email">email:</label>
            <input type="email" name="email" id="email" value=<?php echo $email; ?>>
        </div>
        <div>
            <label for="password">password:</label>
            <input type="password" name="password" id="password">
        </div>
        <div>
            <label for="confirm_password">confirm password:</label>
            <input type="password" name="confirm_password" id="confirm_password">
        </div>
        <button type="submit" class="submit">sign up</button>
    </form>
</body>

</html>