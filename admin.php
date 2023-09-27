<?php
    session_start();
    require_once("db-connection.php");
    if(!isset($_SESSION["id"]) || !$_SESSION["is_admin"]){
        header("Location: admin-login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles\general.css">
    <link rel="stylesheet" href="styles\admin.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="navbar">
        <div class="logo">logo</div>
        <a href="admin-crud/admin-logout.php" class="logout mb-btn">logout</a>
    </div>
    <h1>welcome <?php echo $_SESSION["name"]; ?></h1>
    <div class="news">
        <p class="new">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "GET"){
                    if(isset($_GET["error"])) {
                        $error = $_GET["error"];
                        if($error == "undeleted_img") {
                            echo "there was an error while trying to delete your product";
                        } else {
                            header("Location: admin.php");
                            exit();
                        }
                    } elseif(isset($_GET["succes"])) {
                        $succes = $_GET["succes"];
                        if($succes == "create") {
                            echo "product created succesfully";
                        } elseif($succes == "delete") {
                            echo "product deleted succesfully";
                        }
                        elseif($succes == "update") {
                            echo "product updated succesfully";
                        } else {
                            header("Location: admin.php");
                            exit();
                        }
                    }
                }
            ?>
        </p>
    </div>
    <a href="admin-crud/create.php" class="add-product mb-btn">add product</a>

    <div class="products">
        <?php
            $sql = "SELECT * FROM products";
            $result = mysqli_query($mysqli, $sql);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    echo "
                    <div class=\"product\">
                        <h2>$row[product_name]</h2>
                        <img src=\"uploads/$row[product_img]\">
                        <p>$row[product_desc]</p>
                        <p>$row[price]</p>
                        <div class=\"btns\">
                            <a href=\"admin-crud/update.php?id=$row[id]\" class=\"update mb-btn\">
                                edit
                            </a>
                            <a href=\"admin-crud/delete.php?id=$row[id]\" class=\"delete mb-btn\">
                                delete
                            </a>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<p>there are no products</p>";
            }
        ?>
    </div>
</body>
</html>