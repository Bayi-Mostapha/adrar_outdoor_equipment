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

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES["image"]["error"]) {
                case UPLOAD_ERR_PARTIAL:
                    exit('File only partially uploaded');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    exit('No file was uploaded');
                    break;
                case UPLOAD_ERR_EXTENSION:
                    exit('File upload stopped by a PHP extension');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    exit('File exceeds MAX_FILE_SIZE in the HTML form');
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    exit('File exceeds upload_max_filesize in php.ini');
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    exit('Temporary folder not found');
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    exit('Failed to write file');
                    break;
                default:
                    exit('Unknown upload error');
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
                header("Location: ../admin.php?error=moving_file");
                exit();
            }
        } else {
            echo "only images (png, jpeg, jpg) are allowed!!";
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