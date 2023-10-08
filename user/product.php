<?php
    session_start();
    require_once("../db-connection.php");
    if($_SERVER["REQUEST_METHOD"] != "GET"){
        header("Location: ../home.php");
        exit();
    }
    $id = $mysqli->real_escape_string($_GET["id"]);

    $product_image = "";
    $product_name = "";
    $product_desc = "";
    $product_price = "";
    $colors = array();
    $categorie = "";

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $mysqli->stmt_init();
    if(!$stmt->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt->bind_param("i", $id);
    try{
        $stmt->execute();
    }catch(mysqli_sql_exception){
        //a code that sends error to my email (database error)
        header("Location: ../home.php?error=unknown");
        exit();
    }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        $product_image = $row["product_img"];
        $product_name = $row["product_name"];
        $product_desc = $row["product_desc"];
        $product_price = $row["price"];
        $categorie = $row["categorie"];
    } else {
        header("Location: ../home.php");
        exit();
    }

    $sql = "SELECT * FROM colors WHERE product_id = ?";
    $stmt = $mysqli->stmt_init();
    if(!$stmt->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt->bind_param("i", $id);
    try{
        $stmt->execute();
    }catch(mysqli_sql_exception){
        //a code that sends error to my email (database error)
        header("Location: ../home.php?error=unknown");
        exit();
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = mysqli_fetch_assoc($result)){
            $colors[] = $row["color"];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/product.css">
    <title><?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8');?></title>
</head>
<body>
    <?php
        if(isset($_SESSION["id"])){
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">
                        <img src=\"../images/logo.png\">
                    </div>
                    <div class=\"btns\">
                        <a href=\"cart.php\" class=\"icon-btn\"><i class=\"fa-solid fa-cart-shopping\"></i></a>
                        <a href=\"logout.php\" class=\"icon-btn\"><i class=\"fa-solid fa-arrow-right-from-bracket\"></i></a>
                    </div>
                </div>
                ";
        } else {
            echo "
                <div class=\"navbar\">
                    <div class=\"logo\">
                        <img src=\"../images/logo.png\">
                    </div>
                    <a href=\"../login.php\" class=\"icon-btn\"><i class=\"fa-solid fa-right-to-bracket\"></i></a>
                </div>
                ";
        }
    ?>
    <main>
        <?php
            if(isset($_SESSION["id"])){
                if(isset($_GET["error"])) {
                    $error = $_GET["error"];
                    if($error == "product_in_cart") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">product already in cart</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($error == "empty") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">you need to choose a color and provide a quantity</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($error == "invalid_quantity") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">you need to provide a valid quantity</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    }
                } elseif(isset($_GET["succes"])) {
                    $succes = $_GET["succes"];
                    if($succes == "add_to_cart") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product added to cart succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    }
                }
            }
        ?>
        <div class="main-product">
            <div class="left">
                <div class="image-container">
                    <?php echo"<img src=\"../uploads/$product_image\">";?>
                </div>
            </div>
            <div class="right">
                <form action="cart-handler.php" method="post">
                    <input type="hidden" name="categorie" value=<?php echo htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8'); ?>>
                    <input type="hidden" name="product_id" value=<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>>
                    <div class="product-row">
                        <h1><?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?></h1>
                    </div>
                    <div class="product-row">
                        <p class="price">$<?php echo htmlspecialchars($product_price, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="product-row">
                        <h2>description</h2>
                        <p><?php echo htmlspecialchars($product_desc, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="product-row">
                        <h2>color</h2>
                        <div class="colors">
                            <?php
                                $flag = false;
                                foreach($colors as $color) {
                                    if($flag) {
                                        echo "
                                        <div class=\"product-color-wrapper\">
                                            <label for=\"color" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\" class=\"product-color\" style=\"background-color: " . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . ";\"></label>
                                            <input type=\"radio\" class=\"color-radio\" id=\"color" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\" name=\"color\" value=\"" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\">
                                        </div>
                                        ";
                                    } else {
                                        echo "
                                        <div class=\"product-color-wrapper\">
                                            <label for=\"color" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\" class=\"product-color checked\" style=\"background-color: " . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . ";\"></label>
                                            <input type=\"radio\" class=\"color-radio\" id=\"color" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\" name=\"color\" value=\"" . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . "\" checked>
                                        </div>
                                        ";
                                        $flag = true;
                                    }
                                }
                            ?>
                        </div>
                    </div>
                    <div class="product-row">
                        <h2>quantity</h2>
                        <div class="number-input">
                            <button type="button" class="minus mb-btn">-</button>
                            <input type="text" name="quantity" class="quantity" value="1">
                            <button type="button" class="plus mb-btn">+</button>
                        </div>
                    </div>
                    <?php
                        if(isset($_SESSION["id"])){
                            echo "<button type=\"submit\" class=\"add-to-cart mb-btn\"><i class=\"btn-icon fa-solid fa-plus\"></i> add to cart</button>";
                        } else {
                            echo "<a href=\"../login.php\" class=\"add-to-cart mb-btn\"><i class=\"btn-icon fa-solid fa-plus\"></i> add to cart</a>";
                        }
                    ?>
                </form>
            </div>
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
    <script src="../js/product.js"></script>
</body>
</html>