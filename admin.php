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
        <div class="logo">
            <img src="images/logo.png">
        </div>
        <a href="admin-crud/admin-logout.php" class="icon-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
    </div>
    <main>
        <h1>welcome <?php echo $_SESSION["name"]; ?></h1>
        <?php
            // stats
            echo "<div class=\"stats\">"; 
            $sql = "SELECT COUNT(*) as n FROM products;";
            $result = mysqli_query($mysqli, $sql);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "
                <div class=\"stat\">
                    <p class=\"title\">total products</p>
                    <p class=\"number\">
                        $row[n]
                    </p>
                    <div class=\"icon\">
                        <i class=\"fa-solid fa-store\"></i>
                    </div>
                </div>
                ";
            }

            $sql = "SELECT COUNT(*) as n FROM users;";
            $result = mysqli_query($mysqli, $sql);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "
                <div class=\"stat\">
                    <p class=\"title\">total users</p>
                    <p class=\"number\">
                        $row[n]
                    </p>
                    <div class=\"icon\">
                        <i class=\"fa-solid fa-users\"></i>
                    </div>
                </div>
                ";
            }

            $sql = "SELECT COUNT(*) as n FROM admins;";
            $result = mysqli_query($mysqli, $sql);
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "
                <div class=\"stat\">
                    <p class=\"title\">total admins</p>
                    <p class=\"number\">
                        $row[n]
                    </p>
                    <div class=\"icon\">
                        <i class=\"fa-solid fa-user-tie\"></i>
                    </div>
                </div>
                ";
            }
            echo "</div>"; 
            // end

            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["error"])) {
                    $error = $_GET["error"];
                    if($error == "undeleted_img") {
                        echo "<div class=\"news\"><p class=\"new\">there was an error while trying to delete your product</p></div>";
                    } else {
                        header("Location: admin.php");
                        exit();
                    }
                } elseif(isset($_GET["succes"])) {
                    $succes = $_GET["succes"];
                    if($succes == "create") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product created succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "delete") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product deleted succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    }
                    elseif($succes == "update") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product updated succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } else {
                        header("Location: admin.php");
                        exit();
                    }
                }
            }
        ?>
        <a href="admin-crud/create.php" class="add-product mb-btn">create a product <i class="fa-solid fa-plus"></i></a>

        <div class="products">
            <?php
                $sql = "SELECT * FROM products";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <div class=\"product\">
                            <div class=\"image-container\">
                                <img src=\"uploads/$row[product_img]\">
                            </div>
                            <h2>$row[product_name]</h2>
                            <p>$row[product_desc]</p>
                            <p class=\"price\">$row[price] $</p>
                            <div class=\"btns\">
                                <a href=\"admin-crud/update.php?id=$row[id]\" class=\"update mb-btn\">
                                    <i class=\"fa-solid fa-pen\"></i>
                                    edit
                                </a>
                                <a href=\"admin-crud/delete.php?id=$row[id]\" class=\"delete mb-btn\">
                                    <i class=\"fa-solid fa-trash\"></i>
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
    </main>
    <footer class="footer">
        <div class="f-logo">
            <img src="images/logo.png">
        </div>
        <h2>Adrar</h2>
        <p><i class="fa-regular fa-copyright"></i> 2023</p>
    </footer>
    <?php include "componants/icons.php"; ?>
    <script src="js/general.js"></script>
</body>
</html>