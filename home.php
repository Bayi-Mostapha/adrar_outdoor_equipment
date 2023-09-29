<?php
    session_start();
    require_once("db-connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/home.css">
    <title>store</title>
</head>
<body>
    <?php
        if(isset($_SESSION["id"])){
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">
                        <img src=\"images/logo.png\">
                    </div>
                    <div class=\"btns\">
                        <a href=\"user/cart.php\" class=\"icon-btn\"><i class=\"fa-solid fa-cart-shopping\"></i></a>
                        <a href=\"user/logout.php\" class=\"icon-btn\"><i class=\"fa-solid fa-arrow-right-from-bracket\"></i></a>
                    </div>
                </div>
                <div class=\"home-bg home-bg2\">
                    <h1 class=\"bg-content\">welcome <span class=\"color\">$_SESSION[name]</span></h1>
                    <p class=\"bg-content\">explore our products</p>
                    <button class=\"scroll-down mb-btn bg-content\"><i class=\"fa-solid fa-arrow-down\"></i></button>
                </div>
                ";
        } else {
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">
                        <img src=\"images/logo.png\">
                    </div>
                    <a href=\"login.php\" class=\"icon-btn\"><i class=\"fa-solid fa-right-to-bracket\"></i></a>
                </div>
                <div class=\"home-bg home-bg1\">
                    <h1 class=\"bg-content\">welcome to <span class=\"color\">Adrar</span></h1>
                    <p class=\"bg-content\">explore our products</p>
                    <div class=\"btns bg-content\">
                        <a href=\"login.php\" class=\"mb-btn login-action\">login <i class=\"fa-solid fa-user\"></i></a>
                        <a href=\"sign-up.php\" class=\"mb-btn sign-up-action\">sign up <i class=\"fa-solid fa-user-plus\"></i></a>
                    </div>
                </div>
                ";
        }
    ?>
    <main>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION["id"])){
                if(isset($_GET["error"])) {
                    $error = $_GET["error"];
                    if($error == "product_in_cart") {
                        echo "<div class=\"news\"><p class=\"new\">product already in cart</p></div>";
                    }
                } elseif(isset($_GET["succes"])) {
                    $succes = $_GET["succes"];
                    if($succes == "add_to_cart") {
                        echo "<div class=\"news\"><p class=\"new\"><i class=\"fa-solid fa-plus\"></i> product added to cart succesfully</p></div>";
                    }
                }
            }
        ?>
        <div class="products">
            <?php
                $sql = "SELECT * FROM products";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        if(isset($_SESSION["id"])){
                            echo "
                            <div class=\"product\">
                                <div class=\"image-container\">
                                    <img src=\"uploads/$row[product_img]\">
                                </div>
                                <h2>$row[product_name]</h2>
                                <p>$row[product_desc]</p>
                                <p class=\"price\">$row[price] $</p>
                                <a href=\"user/cart-handler.php?product_id=$row[id]\" class=\"add-to-cart mb-btn\">
                                    <i class=\"fa-solid fa-plus\"></i>
                                    add to cart
                                </a>
                            </div>
                            ";
                        } else {
                            echo "
                            <div class=\"product\">
                                <div class=\"image-container\">
                                    <img src=\"uploads/$row[product_img]\">
                                </div>
                                <h2>$row[product_name]</h2>
                                <p>$row[product_desc]</p>
                                <p class=\"price\">$row[price] $</p>
                                <a href=\"login.php\" class=\"add-to-cart mb-btn\">
                                    <i class=\"fa-solid fa-plus\"></i>
                                    add to cart
                                </a>
                            </div>
                            ";
                        }
                    }
                } else {
                    echo "<p>no products are available</p>";
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
</body>
</html>