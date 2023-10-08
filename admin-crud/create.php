<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    $selected_categorie = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_name = strtolower($mysqli->real_escape_string($_POST["name"]));
        $product_desc = strtolower($mysqli->real_escape_string($_POST["desc"]));
        $product_price = $mysqli->real_escape_string($_POST["price"]);
        $product_categorie = strtolower($mysqli->real_escape_string($_POST["categorie"]));
        $colors = $_POST["color"];
        $filename = "";

        if(empty($product_name)|| empty($product_desc) || empty($product_price) || empty($product_categorie)){
            header("Location: create.php?error=empty");
            exit();
        }

        $sql = "SELECT * FROM categories WHERE categorie_name = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("s", $product_categorie);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            header("Location: ../admin.php?error=unknown");
            exit();
        }
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            header("Location: create.php?error=categorie_not_exists");
            exit();
        }
        $stmt->close();

        if(!filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT)){
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
        
        $sql = "INSERT INTO products (product_name, product_desc, product_img, price, categorie) VALUES (?, ?, ?, ?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("sssds", $product_name, $product_desc, $filename, $product_price, $product_categorie);
        try{
            $stmt->execute();
        }catch(mysqli_sql_exception){
            //send error to email
            header("Location: admin.php?error=product_not_created");
            exit();
        }
        $id = $mysqli->insert_id;

        $sqlColor = "INSERT INTO colors (product_id, color) VALUES (?, ?);";
        $stmtColor = $mysqli->stmt_init();
        if (!$stmtColor->prepare($sqlColor)) {
            die("SQL error: " . $mysqli->error);
        }
        $color_pattern = '/^#([A-Fa-f0-9]{3}){1,2}$/';
        foreach ($colors as $color) {
            $escapedColor = strtolower($mysqli->real_escape_string($color));
            if(empty($escapedColor) || !preg_match($color_pattern, $escapedColor)){
                header("Location: create.php?error=empty");
                exit();
            }
            $stmtColor->bind_param("is", $id, $escapedColor);
            try{
                $stmtColor->execute();
            }catch(mysqli_sql_exception){
                //send error to email
                header("Location: ../admin.php?error=unknown");
                exit();
            }
        }

        header("Location: ../admin.php?succes=create");
        exit();
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
    <title>create product</title>
</head>
<body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["categorie"]))
                $selected_categorie = $_GET["categorie"];

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
                    header("Location: create.php");
                    exit();
                }
            }
        }
    ?>
    <form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <label for="name" class="input-title">product name</label>
            <input type="text" name="name" id="name">
        </div>
        <div class="form-row">
            <label for="desc" class="input-title">product description</label>
            <textarea name="desc" id="desc" cols="30" rows="10"></textarea>
        </div>
        <div class="form-row">
            <label for="price" class="input-title">product price</label>
            <input type="text" name="price" id="price">
        </div>
        <div class="form-row">
            <p class="input-title">product categorie</p>
            <?php
                foreach ($DB_categories as $DB_categorie) {
                    $filtered_DB_categorie = htmlspecialchars($DB_categorie, ENT_QUOTES, 'UTF-8');
                    echo "<div><input type=\"radio\" name=\"categorie\" value=\"$filtered_DB_categorie\" id=\"$filtered_DB_categorie\"";
                    if(isset($selected_categorie) && $DB_categorie == $selected_categorie){
                        echo "checked";
                    }
                    echo "> <label for=\"$filtered_DB_categorie\">$filtered_DB_categorie</label></div>";
                }
            ?>
        </div>
        <div class="form-row color-inputs">
            <p class="input-title">product colors (optionnal)</p>
            <button type="button" class="add-color-input">add color</button>
            <div class="input-color">
                <input type="color" name="color[]"><button type="button" class="remove-color-input mb-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
        <div class="form-row file-container">
            <p class="input-title">product image</p>
            <div id="preview"></div>
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