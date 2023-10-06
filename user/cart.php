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
        <div class="logo">
            <img src="../images/logo.png">
        </div>
        <div class="btns">
            <a href="../home.php" class="icon-btn"><i class="fa-solid fa-house"></i></a>
            <a href="logout.php" class="icon-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
        </div>
    </div>
    <main>
        <?php
            echo "<h1>welcome to your cart, $_SESSION[name]</h1>";
        ?>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["succes"])) {
                    $succes = $_GET["succes"];
                    if($succes == "remove") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product removed from cart succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    }
                }
            }
        ?>
        <div class="products">
            <?php
                $user_id = $mysqli->real_escape_string($_SESSION["id"]);
                $sql2 = "SELECT * FROM cart WHERE user_id=$user_id";
                $result2 = mysqli_query($mysqli, $sql2);

                if(mysqli_num_rows($result2) > 0) {
                    while($row2 = mysqli_fetch_assoc($result2)){
                        $sql = "SELECT * FROM products WHERE id=$row2[product_id]";
                        $result = mysqli_query($mysqli, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $urlColor = substr($row2["color"], 1);
                        echo "
                        <div class=\"product\">
                            <div class=\"image-container\">
                                <img src=\"../uploads/$row[product_img]\">
                            </div>
                            <h2>$row[product_name]</h2>
                            <p>$row[product_desc]</p>
                            <p>$row[price]</p>
                            <div class=\"cart-color\" style=\"background-color: $row2[color];\"></div>
                            <p>$row2[quantity]</p>
                            <a href=\"cart-delete.php?id=$row[id]&color=%23$urlColor&quantity=$row2[quantity]\" class=\"delete mb-btn\">
                                <i class=\"fa-solid fa-trash\"></i>
                                remove from cart
                            </a>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>your cart is empty</p>";
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