<?php
    session_start();
    require_once("db-connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>store</title>
</head>
<body>
    <?php
        if(isset($_SESSION["id"])){
            echo "
                <div class=\"navbar\">
                    <a href=\"user/cart.php\">cart</a>
                    <a href=\"user/logout.php\">logout</a>
                </div>
                <h1>welcome $_SESSION[name]</h1>
                ";
        } else {
            echo "
                <div class=\"navbar\">
                    <a href=\"login.php\">login</a>
                </div>
                <h1>welcome</h1>
                ";
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
                            <h2>$row[product_name]</h2>
                            <img src=\"uploads/$row[product_img]\">
                            <p>$row[product_desc]</p>
                            <p>$row[price]</p>
                            <a href=\"user/cart-handler.php?product_id=$row[id]\">add to cart</a>
                        </div>
                        ";
                    } else {
                        echo "
                        <div class=\"product\">
                            <h2>$row[product_name]</h2>
                            <img src=\"uploads/$row[product_img]\">
                            <p>$row[product_desc]</p>
                            <p>$row[price]</p>
                            <a href=\"login.php\">add to cart</a>
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