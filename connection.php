<?php

    // Configuración de conexión a la base de datos
    $host = "localhost";    
    $user = "root";
    $password = "";
    $db = "tienda";

    // Crear la cadena de conexión PDO (PHP Data Objects)
    $dsn = "mysql:host=$host;dbname=$db";

    // Establecer la conexión PDO
    $link = new PDO($dsn, $user, $password);
