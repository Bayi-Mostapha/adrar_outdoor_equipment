<?php
    include_once "session-config.php";
    session_start();
    include_once "session-regeneration.php";
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
                    <p class=\"bg-content\">Adrar Outdoors is your go-to store for all your 
                    outdoor adventure and camping needs. We offer a vast selection of top-quality
                     gear, from rugged backpacks to cozy sleeping bags, ensuring you're well-equipped
                      for your next expedition.</p>
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
                    <p class=\"bg-content\">Adrar Outdoors is your go-to store for all your 
                    outdoor adventure and camping needs. We offer a vast selection of top-quality
                     gear, from rugged backpacks to cozy sleeping bags, ensuring you're well-equipped
                      for your next expedition.</p>
                    <div class=\"btns bg-content\">
                        <a href=\"login.php\" class=\"mb-btn login-action\">login <i class=\"fa-solid fa-user\"></i></a>
                        <a href=\"sign-up.php\" class=\"mb-btn sign-up-action\">sign up <i class=\"fa-solid fa-user-plus\"></i></a>
                    </div>
                </div>
                ";
        }
    ?>
    <main class="scroll-dest">
        <h2 class="cat-title">explore our categories</h2>
        <div class="categories">
            <?php
                $sql = "SELECT * FROM categories";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <div class=\"categorie\">
                            <div class=\"image-container\">
                                <img src=\"uploads/$row[image]\">
                            </div>
                            <h2>$row[categorie_name]</h2>
                            <a href=\"user/categorie-products.php?categorie=$row[categorie_name]\" class=\"link\">
                                view products
                            </a>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>no categories are available</p>";
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