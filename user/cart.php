<?php
    session_start();
    require_once("../db-connection.php");
    if(!isset($_SESSION["id"])){
        header("Location: ../login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\styles\general.css">
    <link rel="stylesheet" href="..\styles\cart.css">
    <title>cart</title>
</head>
<body>
    <div class="navbar">
        <div class="logo">logo</div>
        <div class="btns">
            <a href="../home.php" class="home mb-btn">home</a>
            <a href="logout.php" class="logout mb-btn">logout</a>
        </div>
    </div>
    <?php
        echo "<h1>welcome to your cart, $_SESSION[name]</h1>";
    ?>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["succes"])) {
                $succes = $_GET["succes"];
                if($succes == "remove") {
                    echo "<div class=\"news\"><p class=\"new\">product removed from cart succesfully</p></div>";
                }
            }
        }
    ?>
    <div class="products">
        <?php
            $sql2 = "SELECT * FROM cart WHERE user_id=$_SESSION[id]";
            $result2 = mysqli_query($mysqli, $sql2);

            if(mysqli_num_rows($result2) > 0) {
                while($row2 = mysqli_fetch_assoc($result2)){
                    $sql = "SELECT * FROM products WHERE id=$row2[product_id]";
                    $result = mysqli_query($mysqli, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo "
                    <div class=\"product\">
                        <h2>$row[product_name]</h2>
                        <img src=\"../uploads/$row[product_img]\">
                        <p>$row[product_desc]</p>
                        <p>$row[price]</p>
                        <a href=\"cart-delete.php?id=$row[id]\" class=\"delete mb-btn\">remove from cart</a>
                    </div>
                    ";
                }
            } else {
                echo "<p>your cart is empty</p>";
            }
        ?>
    </div>
</body>
</html>