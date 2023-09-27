<?php
    session_start();
    require_once("db-connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles\general.css">
    <link rel="stylesheet" href="styles\home.css">
    <title>store</title>
</head>
<body>
    <?php
        if(isset($_SESSION["id"])){
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">logo</div>
                    <div class=\"btns\">
                        <a href=\"user/cart.php\" class=\"cart mb-btn\">cart</a>
                        <a href=\"user/logout.php\" class=\"logout mb-btn\">logout</a>
                    </div>
                </div>
                <h1>welcome $_SESSION[name], discover our products</h1>
                ";
        } else {
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">logo</div>
                    <a href=\"login.php\" class=\"login mb-btn\">login</a>
                </div>
                <h1>Discover our products</h1>
                ";
        }
    ?>
    <div class="news">
        <p class="new">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "GET"){
                    if(isset($_GET["error"])) {
                        $error = $_GET["error"];
                        if($error == "product_in_cart") {
                            echo "product already in cart";
                        }
                    } elseif(isset($_GET["succes"])) {
                        $succes = $_GET["succes"];
                        if($succes == "add_to_cart") {
                            echo "product added to cart succesfully";
                        }
                    }
                }
            ?>
        </p>
    </div>
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
                            <p>$row[price] $</p>
                            <a href=\"user/cart-handler.php?product_id=$row[id]\" class=\"add-to-cart mb-btn\">
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
                            <p>$row[price] $</p>
                            <a href=\"login.php\" class=\"add-to-cart mb-btn\">add to cart</a>
                        </div>
                        ";
                    }
                }
            } else {
                echo "<p>no products are available</p>";
            }
        ?>
    </div>
</body>
</html>