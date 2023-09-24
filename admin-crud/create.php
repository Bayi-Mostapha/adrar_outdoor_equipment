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
        $filename = "";

        if(empty($product_name )|| empty($product_desc) || empty($product_price)){
            header("Location: create.php?error=empty");
            exit();
        }

        if(!filter_input(INPUT_POST, "price", FILTER_VALIDATE_INT)){
            header("Location: create.php?error=invalid_price");
            exit();
        }

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES["image"]["error"]) {
                case UPLOAD_ERR_PARTIAL:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_NO_FILE:
                    header("Location: create.php?error=empty");
                    exit();
                    break;
                case UPLOAD_ERR_EXTENSION:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
                default:
                    header("Location: create.php?error=file_error");
                    exit();
                    break;
            }
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $file_ext = $finfo->file($_FILES["image"]["tmp_name"]);
        $allowed_exts = array("image/jpg", "image/png", "image/jpeg");
        if(in_array($file_ext, $allowed_exts)){
            $pathinfo = pathinfo($_FILES["image"]["name"]);
            $base = $pathinfo["filename"];
            $base = preg_replace("/[^\w-]/", "_", $base);
            $filename = $base . "." . $pathinfo["extension"];

            $destination = "../uploads/" . $filename;
            $i = 1;
            while(file_exists($destination)){
                $filename = $base . "($i)." . $pathinfo["extension"];
                $destination = "../uploads/" . $filename;
                $i++;
            }

            if(!move_uploaded_file($_FILES["image"]["tmp_name"], $destination)){
                header("Location: create.php?error=moving_file");
                exit();
            }
        } else {
            header("Location: create.php?error=wrong_file_type");
            exit();
        }
        
        $sql = "INSERT INTO products (product_name, product_desc, product_img, price) VALUES (?, ?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("sssd", $product_name, $product_desc, $filename, $product_price);
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
    <div class="errors">
        <p class="error">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "GET"){
                    if(isset($_GET["error"])){
                        $error = $_GET["error"];
                        if(isset($error)) {
                            if($error == "empty") {
                                echo "all fields must be filled";
                            } elseif($error == "moving_file") {
                                echo "error while uploading file";
                            } elseif($error == "wrong_file_type") {
                                echo "only images are allowed (png, jpeg...)";
                            } elseif($error == "invalid_price") {
                                echo "price must be a number";
                            } elseif($error == "file_error") {
                                echo "there was an error while uploading your file";
                            }
                        }
                    }
                }
            ?>
        </p>
    </div>
    <form action="create.php" method="post" enctype="multipart/form-data">
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
        <input type="file" name="image">
        <a href="../admin.php">cancel</a>
        <button type="submit">save</button>
    </form>
</body>
</html>