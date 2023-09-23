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
        $product_price = $_POST["price"];

        if(empty($product_name )|| empty($product_desc) || empty($product_price)){
            header("Location: create.php?error=empty");
            exit();
        }
        
        $sql = "INSERT INTO products (product_name, product_desc, product_img, price) VALUES (?, ?, '', ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ssd", $product_name, $product_desc, $product_price);
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
        <div>
            <label for="price">product price</label>
            <input type="text" name="price" id="price">
        </div>
        <button type="submit">save</button>
    </form>
</body>
</html>