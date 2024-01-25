<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ambulatorio";

// Creamos conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Comprobamos la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Establecemos el conjunto de caracteres a utf8
$conexion->set_charset("utf8");
?>