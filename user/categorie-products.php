<?php
    session_start();
    require_once("../db-connection.php");
    if($_SERVER["REQUEST_METHOD"] != "GET"){
        header("Location: ../home.php");
        exit();
    }
    $categorie = $mysqli->real_escape_string($_GET["categorie"]);

    $bg = "";
    $sql = "SELECT * FROM categories WHERE categorie_name = ?";
    $stmt = $mysqli->stmt_init();
    if(!$stmt->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt->bind_param("s", $categorie);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        $bg = $row["image"];
    } else {
        header("Location: ../home.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/general.css">
    <link rel="stylesheet" href="../styles/home.css">
    <title><?php echo $categorie;?></title>
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
        echo "
        <div class=\"home-bg categorie-bg\" style=\"background: url('../uploads/$bg');\">
            <h1 class=\"bg-content\">explore <span class=\"color\">$categorie</span> products</h1>
            <button class=\"scroll-down mb-btn bg-content\"><i class=\"fa-solid fa-arrow-down\"></i></button>
        </div>
        ";
    ?>
    <main>
        <div class="products">
            <?php
                $sql = "SELECT * FROM products WHERE categorie = ?";
                $stmt = $mysqli->stmt_init();
                if(!$stmt->prepare($sql)){
                    die("SQL error: " . $mysqli->error);
                }
                $stmt->bind_param("s", $categorie);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                            <div class=\"product\">
                                <div class=\"image-container\">
                                    <img src=\"../uploads/$row[product_img]\">
                                </div>
                                <h2>$row[product_name]</h2>
                                <p class=\"price\">$row[price] $</p>
                                <a href=\"product.php?id=$row[id]\" class=\"link\">more details</a>
                            </div>
                            ";
                        
                    }
                } else {
                    echo "<p>no products are available in this categorie</p>";
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