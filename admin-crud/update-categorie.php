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
    $categorie_name = "";
    $old_categorie = "";
    $categorie_img_prev = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $categorie_id = $mysqli->real_escape_string($_POST["id"]);
        $categorie_name = $mysqli->real_escape_string($_POST["name"]);

        $filename = "";
        $stmt = "";

        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $categorie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_categorie = $row["categorie_name"];
        }

        if(empty($categorie_name)){
            header("Location: update-categorie.php?id=$categorie_id&error=empty");
            exit();
        }        

        if (!empty($_FILES["image"]["name"])) {
            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                switch ($_FILES["image"]["error"]) {
                    case UPLOAD_ERR_PARTIAL:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                    default:
                        header("Location: update-categorie.php?id=$categorie_id&error=file_error");
                        exit();
                        break;
                }
            }
            
            // deleting 
            $sql_img = "SELECT * FROM categories WHERE id = ?";
            $img_stmt = $mysqli->stmt_init();
            if(!$img_stmt->prepare($sql_img)){
                die("SQL error: " . $mysqli->error);
            }
            $img_stmt->bind_param("i", $categorie_id);
            $img_stmt->execute();
            $categorie_img = "";
            $img_result = $img_stmt->get_result();
            if ($img_result->num_rows > 0) {
                $img_row = $img_result->fetch_assoc();
                $categorie_img = $img_row["image"];
            }
            $path = "../uploads/$categorie_img";
            
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
                    header("Location: update-categorie.php?id=$categorie_id&error=moving_file");
                    exit();
                }
            } else {
                header("Location: update-categorie.php?id=$categorie_id&error=wrong_file_type");
                exit();
            }

            $sql = "UPDATE categories SET categorie_name = ?, image = ? WHERE id=$categorie_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("ss", $categorie_name, $filename);
            $stmt->execute();
            unlink($path);
        } else {
            $sql = "UPDATE categories SET categorie_name = ? WHERE id=$categorie_id;";
            $stmt = $mysqli->stmt_init();
            if(!$stmt->prepare($sql)){
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("s", $categorie_name);
            $stmt->execute();
        }

        $sql = "UPDATE products SET categorie=? WHERE categorie=?";
        $stmt = $mysqli->stmt_init();
        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("ss", $categorie_name, $old_categorie);
        $stmt->execute();
        header("Location: ../admin.php?succes=update-categorie");
        exit();
    } elseif ($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $mysqli->real_escape_string($_GET["id"]);

        if(empty($id)){
            header("Location: ../admin.php");
            exit();
        }

        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if(!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $categorie_name = $row["categorie_name"];
            $categorie_img_prev = $row["image"];
        } else {
            header("Location: ../admin.php");
            exit();
        }
        $stmt->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\styles\general.css">
    <link rel="stylesheet" href="..\styles\crud-form.css">
    <title>edit categorie</title>
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
                    header("Location: update-categorie.php");
                    exit();
                }
            }
        }
    ?>
    <form action="update-categorie.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-row">
            <label for="name">categorie name</label>
            <input type="text" name="name" id="name" value="<?php echo $categorie_name; ?>">
        </div>
        <div class="form-row file-container">
            <div id="preview" class="visible">
                <img src="<?php echo "../uploads/" . $categorie_img_prev; ?>">
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