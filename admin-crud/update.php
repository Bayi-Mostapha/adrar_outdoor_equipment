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
    $price = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_id = $_POST["id"];
        $product_name = $_POST["name"];
        $product_desc = $_POST["desc"];
        $price = $_POST["price"];

        $filename = "";
        $stmt = "";

        if(empty($product_name )|| empty($product_desc) || empty($price)){
            header("Location: update.php?error=empty");
            exit();
        }

        if (!empty($_FILES["image"]["name"])) {
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
            
            $sql_img = "SELECT * FROM products WHERE id = ?";
            $img_stmt = $mysqli->stmt_init();
            if(!$img_stmt->prepare($sql_img)){
                die("SQL error: " . $mysqli->error);
            }
            $img_stmt->bind_param("i", $product_id);
            $img_stmt->execute();
            $product_img = "";
            $img_result = $img_stmt->get_result();
            if ($img_result->num_rows > 0) {
                $img_row = $img_result->fetch_assoc();
                $product_img = $img_row["product_img"];
            }
            $path = "../uploads/$product_img";
            if(!unlink($path)){
                header("Location: ../admin.php?error=img");
                exit();
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

            $sql = "UPDATE products SET product_name = ?, product_desc = ?, product_img = ?, price = ? WHERE id=$product_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("sssd", $product_name, $product_desc, $filename, $price);
            $stmt->execute();
            header("Location: ../admin.php?succes=updateimg");
            exit();
        } else {
            $sql = "UPDATE products SET product_name = ?, product_desc = ?, price = ? WHERE id=$product_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("ssd", $product_name, $product_desc, $price);
            $stmt->execute();
            header("Location: ../admin.php?succes=update");
            exit();
        }
        
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
            $price = $row["price"];
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
    <form action="update.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div>
            <label for="name">product name</label>
            <input type="text" name="name" id="name" value="<?php echo $product_name; ?>">
        </div>
        <div>
            <label for="desc">product description</label>
            <textarea name="desc" id="desc" cols="30" rows="10"><?php echo $product_desc; ?></textarea>
        </div>
        <div>
            <label for="price">product price</label>
            <input type="text" name="price" id="price" value="<?php echo $price; ?>">
        </div>
        <input type="file" name="image">
        <a href="../admin.php">cancel</a>
        <button type="submit">save</button>
    </form>
</body>
</html>