<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: ../admin-login.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] != 'GET'){
        header("Location: ../admin.php");
        exit();
    }

    $categorie =  $mysqli->real_escape_string($_GET["categorie"]);
    $htmlcategorie = htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8');
    if(empty($categorie)){
        header("Location: ../admin.php");
        exit();
    }

    $sql = "SELECT * FROM categories";
    $result = mysqli_query($mysqli, $sql);
    $flag = false;
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($categorie == $row["categorie_name"]){
                    $flag = true;
                    break;
                }
            }
        } else {
            header("Location: ../admin.php");
            exit();
        }
        $result->close();
    } else {
        die("SQL error: " . $mysqli->error);
    }
    if(!$flag){
        header("Location: ../admin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles\general.css">
    <link rel="stylesheet" href="../styles\admin.css">
    <title><?php echo $htmlcategorie;?></title>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../images/logo.png">
        </div>
        <a href="admin-logout.php" class="icon-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
    </div>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["error"])) {
            $error = $_GET["error"];
            if($error == "zero_products") {
                echo "
                <div class=\"errors\">
                    <p class=\"error\">there no products to delete</p>
                    <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                </div>";
            } elseif($error == "unkown") {
                echo "
                <div class=\"errors\">
                    <p class=\"error\">unknown error, please try again later</p>
                   <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                </div>";
            } else {
                header("Location: categorie-products.php");
                exit();
            }
        }
    }
    ?>
    <main>
        <h1>welcome <?php echo htmlspecialchars($_SESSION["name"], ENT_QUOTES, 'UTF-8'); ?>, check <span class="color"><?php echo $htmlcategorie;?></span> products</h1>
        <div class="btns">
            <?php
                echo "<a href=\"create.php?categorie=$categorie\" class=\"add-product mb-btn\"><i class=\"btn-icon fa-solid fa-plus\"></i> create a product</a>";
                echo "<a href=\"delete-all.php?categorie=$categorie\" class=\"delete mb-btn\"><i class=\"btn-icon fa-solid fa-trash\"></i> delete all products</a>";
            ?>
        </div>
        <div class="products">
            <?php
                $sql = "SELECT * FROM products WHERE categorie = ?";
                $stmt = $mysqli->stmt_init();
                if(!$stmt->prepare($sql)){
                    die("SQL error: " . $mysqli->error);
                }
                $stmt->bind_param("s", $categorie);
                try{
                    $stmt->execute();
                }catch(mysqli_sql_exception){
                    //a code that sends error to my email (database error)
                    header("Location: ../admin.php");
                    exit();
                }
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <div class=\"product\">
                            <div class=\"image-container\">
                                <img src=\"../uploads/$row[product_img]\">
                            </div>
                            <h2>" . htmlspecialchars($row["product_name"], ENT_QUOTES, 'UTF-8') . "</h2>
                            <p>" . htmlspecialchars($row["product_desc"], ENT_QUOTES, 'UTF-8') . "</p>
                            <p class=\"price\">" . htmlspecialchars($row["price"], ENT_QUOTES, 'UTF-8') . " $</p>
                            <div class=\"btns\">
                                <a href=\"update.php?id=$row[id]\" class=\"update mb-btn\">
                                    <i class=\"btn-icon fa-solid fa-pen\"></i>
                                    edit
                                </a>
                                <a href=\"delete.php?id=$row[id]\" class=\"delete mb-btn\">
                                    <i class=\"btn-icon fa-solid fa-trash\"></i>
                                    delete
                                </a>
                            </div>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>there are no products in this categorie</p>";
                }
            ?>
        </div>
    </main>
    <footer class="footer">
        <div class="f-logo">
            <img src="../images/logo.png">
        </div>
        <h2>Adrar</h2>
        <p><i class="fa-regular fa-copyright"></i> 2023</p>
    </footer>
    <?php include "../componants/icons.php"; ?>
    <script src="../js/general.js"></script>
</body>
</html>