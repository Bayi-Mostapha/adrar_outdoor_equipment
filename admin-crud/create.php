<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_name = $_POST["name"];
        $product_desc = $_POST["desc"];
        if(empty($product_name )|| empty($product_desc)){
            header("Location: create.php?error=empty");
            exit();
        }
        
        $sql = "INSERT INTO products (product_name, product_desc, product_img) VALUES (?, ?, '');";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ss", $product_name, $product_desc);
        $stmt->execute();
        header("Location: ../admin.php?succes=create");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>create product</title>
</head>
<body>
    <form action="create.php" method="post">
        <div>
            <label for="name">product name</label>
            <input type="text" name="name" id="name">
        </div>
        <div>
            <label for="desc">product description</label>
            <textarea name="desc" id="desc" cols="30" rows="10"></textarea>
        </div>
        <button type="submit">save</button>
    </form>
</body>
</html>