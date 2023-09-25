<?php
    $name = "";
    $email = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign up</title>
</head>

<body>
    <div class="errors">
        <p class="error">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "GET"){
                    if(isset($_GET["error"])){
                        $error = $_GET["error"];
                        if($error == "empty"){
                            echo "all fields must be filled";
                        } elseif($error == "wrong_email"){
                            echo "email not valid";
                        } elseif($error == "password_short"){
                            echo "password must contain at least 8 characters";
                        } elseif($error == "passwords_not_match"){
                            echo "passwords do not match";
                        } elseif($error == "email_taken"){
                            echo "email already taken";
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
        </p>
    </div>
    <form action="./sign-up-handler.php" method="post" novalidate>
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
        <button type="submit">sign in</button>
    </form>
</body>

</html>