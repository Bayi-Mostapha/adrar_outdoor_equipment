<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $categorie_name = $mysqli->real_escape_string($_POST["name"]);
        $filename = "";

        if(empty($categorie_name)){
            header("Location: create-categorie.php?error=empty");
            exit();
        }

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES["image"]["error"]) {
                case UPLOAD_ERR_PARTIAL:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_NO_FILE:
                    header("Location: create-categorie.php?error=empty");
                    exit();
                    break;
                case UPLOAD_ERR_EXTENSION:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    header("Location: create-categorie.php?error=file_error");
                    exit();
                    break;
                default:
                    header("Location: create-categorie.php?error=file_error");
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
                header("Location: create-categorie.php?error=moving_file");
                exit();
            }
        } else {
            header("Location: create-categorie.php?error=wrong_file_type");
            exit();
        }
        
        $sql = "INSERT INTO categories (categorie_name, image) VALUES (?, ?);";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ss", $categorie_name, $filename);
        $stmt->execute();
        header("Location: ../admin.php?succes=create-categorie");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\styles\general.css">
    <link rel="stylesheet" href="..\styles\crud-form.css">
    <title>create categorie</title>
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
                } elseif($error == "file_error") {
                    echo "
                    <div class=\"errors\">
                        <p class=\"error\">there was an error while uploading your file</p>
                       <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                    </div>";
                } else {
                    header("Location: create-categorie.php");
                    exit();
                }
            }
        }
    ?>
    <form action="create-categorie.php" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <label for="name">categorie name</label>
            <input type="text" name="name" id="name">
        </div>
        <div class="form-row file-container">
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