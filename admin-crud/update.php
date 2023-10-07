<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }

    $id = null;
    $product_name = "";
    $product_desc = "";
    $price = "";
    $product_categorie = "";
    $colors = array();
    $product_img_prev = "";
    $flag = false;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_id = $mysqli->real_escape_string($_POST["id"]);
        $product_name = $mysqli->real_escape_string($_POST["name"]);
        $product_desc = $mysqli->real_escape_string($_POST["desc"]);
        $price = $mysqli->real_escape_string($_POST["price"]);
        $product_categorie = $mysqli->real_escape_string($_POST["categorie"]);
        $colors = $_POST["color"];

        $filename = "";
        $stmt = "";

        if(empty($product_name )|| empty($product_desc) || empty($price) || empty($product_categorie)){
            header("Location: update.php?id=$product_id&error=empty");
            exit();
        }

        $sql = "SELECT * FROM categories WHERE categorie_name = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("s", $product_categorie);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            header("Location: ../update.php?error=categorie_not_exists");
            exit();
        }
        $stmt->close();

        if (!filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT)) {
            header("Location: update.php?id=$product_id&error=invalid_price");
            exit();
        }        

        if (!empty($_FILES["image"]["name"])) {
            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                switch ($_FILES["image"]["error"]) {
                    case UPLOAD_ERR_PARTIAL:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                    default:
                        header("Location: update.php?id=$product_id&error=file_error");
                        exit();
                        break;
                }
            }
            
            // deleting 
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
            
            // get new img 
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
                    header("Location: update.php?id=$product_id&error=moving_file");
                    exit();
                }
            } else {
                header("Location: update.php?id=$product_id&error=wrong_file_type");
                exit();
            }

            $sql = "UPDATE products SET product_name = ?, product_desc = ?, product_img = ?, price = ?, categorie = ? WHERE id=$product_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("sssds", $product_name, $product_desc, $filename, $price, $product_categorie);
            $stmt->execute();
            unlink($path);
            $flag = true;
        } else {
            $sql = "UPDATE products SET product_name = ?, product_desc = ?, price = ?, categorie = ? WHERE id = $product_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("ssds", $product_name, $product_desc, $price, $product_categorie);
            $stmt->execute();
            $flag = true;
        }
        if (isset($colors)) {
            $sqlDelete = "DELETE FROM colors WHERE product_id = ?;";
            $stmtDelete = $mysqli->stmt_init();
            if (!$stmtDelete->prepare($sqlDelete)) {
                die("SQL error: " . $mysqli->error);
            }
            $stmtDelete->bind_param("i", $product_id);
            $stmtDelete->execute();
        
            $sqlColor = "INSERT INTO colors (product_id, color) VALUES (?, ?);";
            $stmtColor = $mysqli->stmt_init();
            if (!$stmtColor->prepare($sqlColor)) {
                die("SQL error: " . $mysqli->error);
            }
            foreach ($colors as $color) {
                if ($color != "not-color") {
                    $escapedColor = $mysqli->real_escape_string($color);
                    $stmtColor->bind_param("is", $product_id, $escapedColor); 
                    $stmtColor->execute();
                }
            }
        
            $stmtDelete->close();
            $stmtColor->close();
        }  
        if($flag){
            header("Location: ../admin.php?succes=update");
            exit();
        }      
    } elseif ($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);

        if(empty($id)){
            header("Location: ../admin.php");
            exit();
        }

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
            $product_categorie = $row["categorie"];
            $product_img_prev = $row["product_img"];
        } else {
            header("Location: ../admin.php");
            exit();
        }

        $sql = "SELECT * FROM colors WHERE product_id = ?";
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $colors[] = $row["color"];
            }
        }
    }
    $sql = "SELECT * FROM categories";
    $result = $mysqli->query($sql);
    $DB_categories = array();
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $DB_categories[] = $row["categorie_name"];
            }
        } else {
            header("Location: ../admin.php?error=no_categories");
            exit();
        }
        $result->close();
    } else {
        die("SQL error: " . $mysqli->error);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\styles\general.css">
    <link rel="stylesheet" href="..\styles\crud-form.css">
    <title>edit product</title>
</head>
<body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["error"])){
                $error = $_GET["error"];
                if($error == "empty") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">all fields must be filled</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } elseif($error == "moving_file") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">error while uploading file</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } elseif($error == "wrong_file_type") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">only images are allowed (png, jpeg...)</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } elseif($error == "invalid_price") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">price must be a number</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } elseif($error == "file_error") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">there was an error while uploading your file</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } elseif($error == "categorie_not_exists") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">this categorie does not exist in database</p>
                        <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } else {
                    header("Location: update.php");
                    exit();
                }
            }
        }
    ?>
    <form action="update.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-row">
            <label for="name" class="input-title">product name</label>
            <input type="text" name="name" id="name" value="<?php echo $product_name; ?>">
        </div>
        <div class="form-row">
            <label for="desc" class="input-title">product description</label>
            <textarea name="desc" id="desc" cols="30" rows="10"><?php echo $product_desc; ?></textarea>
        </div>
        <div class="form-row">
            <label for="price" class="input-title">product price</label>
            <input type="text" name="price" id="price" value="<?php echo $price; ?>">
        </div>
        <div class="form-row">
            <p class="input-title">product categorie</p>
            <?php
                foreach ($DB_categories as $DB_categorie) {
                    echo "<div><input type=\"radio\" name=\"categorie\" value=\"$DB_categorie\" id=\"$DB_categorie\"";
                    if($DB_categorie == $product_categorie){
                        echo "checked";
                    }
                    echo "> <label for=\"$DB_categorie\">$DB_categorie</label></div>";
                }
            ?>
        </div>
        <div class="form-row color-inputs">
            <p class="input-title">product colors (optionnal)</p>
            <button type="button" class="add-color-input">add color</button>
            <?php
                foreach($colors as $color){
                    echo"
                    <div class=\"input-color\">
                        <input type=\"color\" name=\"color[]\" value=\"$color\"><button type=\"button\" class=\"remove-color-input mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>
                    ";
                }
            ?>
        </div>
        <div class="form-row file-container">
            <p class="input-title">product image</p>
            <div id="preview" class="visible">
                <img src="<?php echo "../uploads/" . $product_img_prev; ?>">
            </div>
            <input type="file" name="image" id="image">
        </div>
        <div class="btns">
            <a href="../admin.php" class="mb-btn cancel"><i class="fa-solid fa-ban"></i> cancel</a>
            <button type="submit" class="mb-btn save"><i class="fa-solid fa-cloud"></i> save</button>
        </div>
    </form>
    <?php include "../componants/icons.php"; ?>
    <script src="../js/general.js"></script>
    <script src="../js/admin-crud.js"></script>
</body>
</html>