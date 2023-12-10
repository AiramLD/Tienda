<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Carrito</h2>

    <?php
    // iniciamos la sesion
    session_start();

    $total = 0;

    require "connection.php";
    include "top.php";

    // Verifica si hay algo en el carrito
    if ($_SESSION['stock'] != 0) {
        mostrarCarrito($link);
    } else {
        echo "<h3>El carrito está vacío</h3>";
    }
    ?>
    <br>
    <form method="get" action="index.php">
        <button type="submit">Volver a la página principal</button>
    </form>


    <form method="post" action="">
        <button type="submit" name="empty_trolley">Vaciar carrito</button>
    </form>

    <?php
    if (isset($_POST['empty_trolley'])) {
        // Llama a la función para vaciar el carrito
        emptytrolley($link);
    }

    // Función para vaciar el carrito
    function emptytrolley($link) {
        if (!isset($_SESSION['trolley'])) {
            return; // No se hace nada 
        }

        // Obtenemos la cantidad de producto antes de vaciar
        foreach ($_SESSION['trolley'] as $product_id => $quantity) {
            // Sumamos cantidad + stock en la BD
            $sql = "UPDATE products SET amount = amount + ? WHERE id = ?";
            $stm = $link->prepare($sql);
            $stm->bind_param("ss", $quantity, $product_id);
            $stm->execute();
            $stm->close();
        }

        // Elimina la sesión
        unset($_SESSION['trolley']);
        // Reinicia el contador
        $_SESSION['stock'] = 0;
        // Redirige a la misma página para evitar problemas con el formulario al recargar
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Botones - y +
    if (isset($_POST["subtract"])) {
        subtract($_POST['product_id']);
    }
    if (isset($_POST["add"])) {
        addCant($_POST['product_id']);
    }

    // Función para mostrar el carrito
    function mostrarCarrito($link) {
        global $total;

        echo '<table>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>';

        foreach ($_SESSION["trolley"] as $product_id => $quantity) {
            $sql = "SELECT * FROM products WHERE id=?";
            $stm = $link->prepare($sql);
            $stm->bind_param("i", $product_id);
            $stm->execute();
            $result = $stm->get_result();
            $field = $result->fetch_assoc();

            while ($field !== null) {
                $id = $field['id'];
                $name = $field['name'];
                $price = $field['price'];
                $subtotal = $price * $quantity;
                $total += $subtotal;

                echo '<tr>
                        <td>' . $id . '</td>
                        <td>' . $name . '</td>
                        <td>' . $price . '</td>
                        <td>' . $quantity . '</td>
                        <td>' . $subtotal . '</td>
                        <td class="td_buttons">
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="' . $id . '">
                                <input type="hidden" name="current_quantity" value="' . $quantity . '">
                                <button type="submit" name="add" class="button">+</button>
                                <button type="submit" name="subtract" class="button">-</button>
                            </form>
                        </td>
                    </tr>';

                $field = $result->fetch_assoc();
            }

            $stm->close();
        }
        echo '<tr>
                <td colspan="2"></td>
                <td colspan="2" id="total">Suma total del carrito</td>
                <td>' . $total . '</td>
            </tr>
            </table>';
    }

    // Función para restar cantidad de un producto
    function subtract($product_id) {
        require "connection.php";
        include "top.php";

        // Cantidad de stock
        $sql1 = "SELECT amount FROM products WHERE id = $product_id";
        $result = $link->query($sql1);

        $field = $result->fetch_assoc();
        $amount = $field['amount'];

        // Si no da error
        if ($result) {
            // Si es mayor de 0
            if ($amount > 0) {
                // +1 al stock  
                $sql2 = "UPDATE products SET amount = amount +1 WHERE id=$product_id";
                $result = $link->query($sql2);

                $productId = $_POST["product_id"];
                $currentQuantity = $_POST["current_quantity"];

                // Si la cantidad del carrito es mayor que 0
                if (isset($_SESSION["trolley"][$productId]) && $_SESSION["trolley"][$productId] > 0) {
                    // Se resta uno a la cantidad del producto en el carrito
                    $_SESSION["trolley"][$productId] = $currentQuantity - 1;

                    // Si llega a 0
                    if ($_SESSION["trolley"][$productId] == 0) {
                        // Se borra del carrito
                        unset($_SESSION["trolley"][$productId]);
                    }

                    // Redirige a la misma página para evitar problemas con el formulario al recargar
                    // header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
            } else {
                echo "<p class='error'>No hay stock</p>";
            }
        }
    }
     // Función para añadir cantidad de un producto
     function addCant($product_id) {
        require "connection.php";
        include "top.php";

        // Busco cuánto stock hay del producton con ID x
        $sql1 = "SELECT amount FROM products WHERE id = $product_id";
        $result = $link->query($sql1);

        $field = $result->fetch_assoc();
        $amount = $field['amount'];

        // Si no da error
        if ($result) {
            // Si es mayor que 0
            if ($amount > 0) {
                // -1 al stock
                $sql2 = "UPDATE products SET amount = amount -1 WHERE id=$product_id";
                $result = $link->query($sql2);

                $productId = $_POST["product_id"];
                $currentQuantity = $_POST["current_quantity"];
                $_SESSION["trolley"][$productId] = $currentQuantity + 1;

                // Redirige a la misma página para evitar problemas con el formulario al recargar
                // header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo "<p class='error'>No hay stock suficiente</p>";
            }
        }
    }
    // Vaciar el carrito 
    if (empty($_SESSION["trolley"])) {
        emptytrolley($link);
    }
    ?>
</body>
</html>