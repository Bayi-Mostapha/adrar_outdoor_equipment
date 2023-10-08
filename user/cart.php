<?php
    include_once "../session-config.php";
    session_start();
    include_once "../session-regeneration.php";
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
            echo "<h1>welcome to your cart, " . htmlspecialchars($_SESSION["name"], ENT_QUOTES, 'UTF-8') . "</h1>";
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
                } else if(isset($_GET["error"])){
                    $error = $_GET["error"];
                    if($error == "unknown") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">unknown error, please try again later</p>
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
                        $sql = "SELECT * FROM products WHERE id=" . $mysqli->real_escape_string($row2["product_id"]);
                        $result = mysqli_query($mysqli, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $urlColor = htmlspecialchars(substr($row2["color"], 1), ENT_QUOTES, 'UTF-8');
                        echo "
                        <div class=\"product\">
                            <div class=\"image-container\">
                                <img src=\"../uploads/$row[product_img]\">
                            </div>
                            <h2>" . htmlspecialchars($row["product_name"], ENT_QUOTES, 'UTF-8') . "</h2>
                            <p>" .  htmlspecialchars($row2["quantity"] , ENT_QUOTES, 'UTF-8') . " x " . htmlspecialchars($row["price"], ENT_QUOTES, 'UTF-8') . "$</p>
                            <div class=\"cart-color\" style=\"background-color: " . htmlspecialchars($row2["color"], ENT_QUOTES, 'UTF-8') . ";\"></div>
                            <p>quantity: </p>
                            <a href=\"cart-delete.php?id=" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "&color=%23$urlColor&quantity=" . htmlspecialchars($row2["quantity"], ENT_QUOTES, 'UTF-8') . "\" class=\"delete mb-btn\">
                                <i class=\"fa-solid fa-trash\"></i>
                                remove from cart
                            </a>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>your cart is empty <a href=\"../home.php\" class=\"link\">explore products!</a></p>";
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