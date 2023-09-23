<?php 
    include_once("db-connection.php");

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email = $_POST["email"];
        $password = $_POST["password"];


        $sql = "SELECT * FROM admins WHERE email=?";
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
                header("Location: admin.php");
                exit();
            } else {
                header("Location: login.php?error=wrong_login");
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
    <h1>welcome admin</h1>
    <form action="admin-login.php" method="post" novalidate>
        <div>
            <label for="email">email:</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="password">password:</label>
            <input type="password" name="password" id="password">
        </div>
        <button type="submit">login</button>
    </form>
</body>
</html>