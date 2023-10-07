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
        <div class="greeting">
            <h1>Welcome back, <?php echo $_SESSION["name"]; ?> &#128075;</h1>
            <div class="date-time">
                <p class="date"></p>
                <p>|</p>
                <p class="time"></p>
            </div>
        </div>
        <?php
            // stats
            echo "<div class=\"stats\">"; 

            $sql = "SELECT COUNT(*) as n FROM categories;";
            $result = mysqli_query($mysqli, $sql);
            $row = mysqli_fetch_assoc($result);
            echo "
            <div class=\"stat card\">
                <p class=\"title\">total categories</p>
                <p class=\"number\">
                    $row[n]
                </p>
                <div class=\"icon\">
                    <i class=\"fa-solid fa-boxes-stacked\"></i>
                </div>
            </div>
            ";

            $sql = "SELECT COUNT(*) as n FROM products;";
            $result = mysqli_query($mysqli, $sql);
            $row = mysqli_fetch_assoc($result);
            echo "
            <div class=\"stat card\">
                <p class=\"title\">total products</p>
                <p class=\"number\">
                    $row[n]
                </p>
                <div class=\"icon\">
                    <i class=\"fa-solid fa-dolly\"></i>
                </div>
            </div>
            ";

            $sql = "SELECT COUNT(*) as n FROM users;";
            $result = mysqli_query($mysqli, $sql);
            $row = mysqli_fetch_assoc($result);
            echo "
            <div class=\"stat card\">
                <p class=\"title\">total users</p>
                <p class=\"number\">
                    $row[n]
                </p>
                <div class=\"icon\">
                    <i class=\"fa-solid fa-users\"></i>
                </div>
            </div>
            ";

            $sql = "SELECT COUNT(*) as n FROM admins;";
            $result = mysqli_query($mysqli, $sql);
            $row = mysqli_fetch_assoc($result);
            echo "
            <div class=\"stat card\">
                <p class=\"title\">total admins</p>
                <p class=\"number\">
                    $row[n]
                </p>
                <div class=\"icon\">
                    <i class=\"fa-solid fa-user-tie\"></i>
                </div>
            </div>
            ";

            echo "</div>"; 
            // end

            if($_SERVER["REQUEST_METHOD"] == "GET"){
                if(isset($_GET["error"])) {
                    $error = $_GET["error"];
                    if($error == "undeleted_img") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">there was an error while trying to delete your product</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($error == "no_categories") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">there is no categories in database</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($error == "products_in_categorie") {
                        echo "
                        <div class=\"errors\">
                            <p class=\"error\">you cannot delete a categorie if it has products in it</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>
                        ";
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
                    } elseif($succes == "delete_all") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">products deleted succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "update") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">product updated succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "create-categorie") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">categorie created succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "delete-categorie") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">categorie deleted succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "update-categorie") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">categorie updated succesfully</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "admin_approved") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">admin approved</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } elseif($succes == "admin_denied") {
                        echo "
                        <div class=\"news\">
                            <p class=\"new\">admin denied</p>
                            <button class=\"close-new mb-btn\"><i class=\"fa-solid fa-xmark\"></i></button>
                        </div>";
                    } else {
                        header("Location: admin.php");
                        exit();
                    }
                }
            }
        ?>
        <div class="admin-btns">
            <a href="admin-crud/create-categorie.php" class="add-product mb-btn"><i class="btn-icon fa-solid fa-plus"></i> create a categorie</a>
            <a href="admin-crud/create.php" class="add-product mb-btn"><i class="btn-icon fa-solid fa-plus"></i> create a product</a>
        </div>
        <h2>Categories</h2>
        <div class="table-wrapper card">
            <?php
                $sql = "SELECT * FROM categories";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    echo "
                    <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Picture</th>
                                    <th>Categorie's name</th>
                                    <th>Total products</th>
                                    <th>Action</th>
                                </tr>
                            </thead> 
                            <tbody>           
                    ";
                    while($row = mysqli_fetch_assoc($result)){
                        $sql2 = "SELECT COUNT(*) as n FROM products WHERE categorie='$row[categorie_name]';";
                        $result2 = mysqli_query($mysqli, $sql2);
                        $row2 = mysqli_fetch_assoc($result2);
                        echo "
                        <tr>
                            <td>
                                <p class=\"id\">
                                    $row[id]
                                </p>
                            </td>
                            <td>
                                <div class=\"simage-container\">
                                    <img src=\"uploads/$row[image]\">
                                </div>
                            </td>
                            <td>
                                <p>$row[categorie_name]</p>
                            </td>
                            <td>
                                <p>$row2[n]</p>
                            </td>
                            <td>
                                <a href=\"admin-crud/update-categorie.php?id=$row[id]\" class=\"mb-btn update\"><i class=\"btn-icon fa-solid fa-pen\"></i> update</a>
                                <a href=\"admin-crud/delete-categorie.php?id=$row[id]\" class=\"mb-btn delete\"><i class=\"btn-icon fa-solid fa-trash\"></i> delete</a>
                                <a href=\"admin-crud/categorie-products.php?categorie=$row[categorie_name]\" class=\"view\"><i class=\"btn-icon fa-regular fa-eye\"></i> view products</a>
                            </td>
                        </tr>
                        ";
                    }
                    echo "
                        </tbody>
                    </table>
                    ";
                } else {
                    echo "<p>there are no categories</p>";
                }
            ?>
        </div>
        <h2>users</h2>
        <div class="table-wrapper card">
            <?php
                $sql = "SELECT * FROM users";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    echo "
                    <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead> 
                            <tbody>           
                    ";
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <tr>
                            <td>
                                <p class=\"id\">$row[id]</p>
                            </td>
                            <td>
                                <p>$row[name]</p>
                            </td>
                            <td>
                                <p>$row[email]</p>
                            </td>
                        </tr>
                        ";
                    }
                    echo "
                        </tbody>
                    </table>
                    ";
                } else {
                    echo "<p>there are no users in database</p>";
                }
            ?>
        </div>
        <h2>admins</h2>
        <div class="table-wrapper card">
            <?php
                $sql = "SELECT * FROM admins";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    echo "
                    <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead> 
                            <tbody>           
                    ";
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <tr>
                            <td>
                                <p class=\"id\">$row[id]</p>
                            </td>
                            <td>
                                <p>$row[name]</p>
                            </td>
                            <td>
                                <p>$row[email]</p>
                            </td>
                        </tr>
                        ";
                    }
                    echo "
                        </tbody>
                    </table>
                    ";
                } else {
                    echo "<p>there are no admins in database</p>";
                }
            ?>
        </div>

        <h2>requests to be an admin</h2>
        <div class="table-wrapper card">
            <?php
                $sql = "SELECT * FROM admin_requests";
                $result = mysqli_query($mysqli, $sql);
                if(mysqli_num_rows($result) > 0) {
                    echo "
                    <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead> 
                            <tbody>           
                    ";
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <tr>
                            <td>
                                <p class=\"id\">$row[id]</p>
                            </td>
                            <td>
                                <p>$row[name]</p>
                            </td>
                            <td>
                                <p>$row[email]</p>
                            </td>
                            <td>
                                <a href=\"admin-crud/approve-admin.php?id=$row[id]\" class=\"update mb-btn\"><i class=\"btn-icon fa-solid fa-check\"></i> approve</a>
                                <a href=\"admin-crud/deny-admin.php?id=$row[id]\" class=\"delete mb-btn\"><i class=\"btn-icon fa-solid fa-xmark\"></i> deny</a>
                            </td>
                        </tr>
                        ";
                    }
                    echo "
                        </tbody>
                    </table>
                    ";
                } else {
                    echo "<p>there are no requests to be an admin in database</p>";
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
    <script src="js/admin.js"></script>
</body>
</html>