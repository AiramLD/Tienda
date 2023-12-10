<?php
// Configuración de la clasificación 'sort'
// Se verifica si hay un parámetro 'sort' en la URL.
if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
    // Si existe, se usa ese valor. De lo contrario, se verifica si hay una cookie 'sort'.
} else if (isset($_COOKIE["sort"])) {
    $sort = $_COOKIE['sort'];
    // Si no hay ninguno, se establece por defecto en "id" y se guarda en una cookie llamada 'sort'.
} else {
    $sort = "id";
}
setcookie('sort', $sort);
// Iniciamos la sesión o recuperamos la anterior existente
session_start();
// Comprobamos si la variable existe o no
if (!isset($_SESSION['stock'])) {
    $_SESSION['stock'] = 0;
    $_SESSION['cart'] = array();
}
?>
<!-- HTML BASICO -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Tienda de Airam</h2>
    <form action="cart.php" method="get">
        <button type="submit">Ver Carrito</button>
    </form>
    <form action="logout.php" method="post">
        <button type="submit">Cerrar Sesión</button>
    </form>
    <!-- Tabla de productos -->
    <table>
        <tr>
            <th><a href="?sort=id">Id</a></th>
            <th><a href="?sort=name">Nombre</a></th>
            <th><a href="?sort=price">Precio</a></th>
            <th><a href="?sort=stock">Stock</a></th>
            <th> </th>
        </tr>
        <tr>
            <?php
            orderBy($sort);
            // Funcion para elegir como ordenar los productos
            function orderBy($sort) {
                require "connection.php";
                include "top.php";

                //Por defecto ordenamos por id
                $sql = "SELECT * FROM products ORDER BY id";
                // Utilizamos un switch para determinar el campo por el cual ordenar.
                switch ($sort) {
                    case "id":
                        $sql = "SELECT * FROM products ORDER BY id";
                        break;
                    case "name":
                        $sql = "SELECT * FROM products ORDER BY name";
                        break;
                    case "price":
                        $sql = "SELECT * FROM products ORDER BY price";
                        break;
                    case "stock":
                        $sql = "SELECT * FROM products ORDER BY amount";
                        break;
                }
                // Recuperamos y mostramos los resultados en la tabla.
                $result = $link->query($sql);
                $field = $result->fetch_assoc();

                while ($field !== null) {
                    $id = $field['id'];
                    $name = $field['name'];
                    $price = $field['price'];
                    $amount = $field['amount'];
            ?>
                    <td id="<?= $id ?>"><?= $id ?></td>
                    <td id="<?= $id ?>"><?= $name ?></td>
                    <td id="<?= $id ?>"><?= $price ?></td>
                    <td id="<?= $id ?>"><?= $amount ?></td>
                    <!-- Creamos un formulario para enviar el id del producto
                    al hacer clic en Añadir al carrito -->
                    <form method="post" action="">
                        <input type="hidden" name="product_id" value="<?= $id ?>">
                        <td>
                            <!-- Usamos el utf8icons para poner el icono de un carrito -->
                            <button type="submit" name="add_cart_button">&#128722;</button>
                        </td>
                    </form>
                    <?php
                    $field = $result->fetch_assoc();
                    ?>
        </tr>
<?php
                }
                $result->close();
            }
            if (isset($_POST['add_cart_button'])) {
                subtract($_POST['product_id']);
            }
            // La función subtract resta uno del stock y 
            // luego llama a la función addCart para añadir el producto al carrito
            function subtract($product_id) {
                require "connection.php";
                include "top.php";

                //Buscamos la cantidad de stock
                $sql1 = "SELECT amount FROM products WHERE id = $product_id";
                $result = $link->query($sql1);
                $field = $result->fetch_assoc();
                $amount = $field['amount'];
                //si no da error
                if ($result) {
                    //si es mayor que 0
                    if ($amount > 0) {
                        $sql2 = "UPDATE products SET amount = amount -1 WHERE id=$product_id";
                        $result = $link->query($sql2);
                        echo "<p class='success'>Se ha añadido al carrito de la compra el producto con id $product_id</p>";
                        addCart($_POST['product_id']);
                    } else {
                        echo "<p class='error'>No hay stock suficiente del producto con id $product_id</p>";
                    }
                }
            }
            // addCart aumenta la cantidad del producto en el carrito.
            function addCart($product_id) {
                $_SESSION['stock']++;
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = array();
                }
                // Aumenta la cantidad
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]++;
                } else {
                    $_SESSION['cart'][$product_id] = 1;
                }
            }
?>
    </table>
</body>
</html>