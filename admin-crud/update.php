<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }

    $id = null;
    $product_name = "";
    $product_desc = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_id = $_POST["id"];
        $product_name = $_POST["name"];
        $product_desc = $_POST["desc"];
        if(empty($product_name )|| empty($product_desc)){
            header("Location: update.php?error=empty");
            exit();
        }
        
        $sql = "UPDATE products SET product_name = ?, product_desc = ?, product_img = '' WHERE id=$product_id;";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ss", $product_name, $product_desc);
        $stmt->execute();
        header("Location: ../admin.php?succes=update");
        exit();
    } elseif ($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);

        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $product_name = $row["product_name"];
            $product_desc = $row["product_desc"];
        } else {
            header("Location: ../admin.php");
            exit();
        }
        $stmt->close();

    } else {
        header("Location: ../admin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit product</title>
</head>
<body>
    <form action="update.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div>
            <label for="name">product name</label>
            <input type="text" name="name" id="name" value="<?php echo $product_name; ?>">
        </div>
        <div>
            <label for="desc">product description</label>
            <textarea name="desc" id="desc" cols="30" rows="10"><?php echo $product_desc; ?></textarea>
        </div>
        <button type="submit">save</button>
    </form>
</body>
</html>