<?php
// Iniciamos la sesion
session_start();
// Destruimos la sesion
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logout</title>
  <link rel="stylesheet" href="style.css">

</head>
<body>
  <h1>Se ha cerrado la sesion correctamente, para volver a iniciar pulse el boton de abajo</h1>
  <p>
    <form method="get" action="index.php">
    <button type="submit">Volver a loguearse</button>
    </form>
  </p>
</body>
</html>