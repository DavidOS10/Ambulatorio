<?php
require_once("conecta.php");

// Iniciamos sesión
session_start();

// Verificamos el inicio de sesión del paciente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos los datos del formulario
    $DNI = $_POST["DNI"];
    $nombre_paciente = $_POST["nombre_paciente"];

    // Consultamos la base de datos para verificar el inicio de sesión
    $sql_verificar_paciente = "SELECT * FROM paciente WHERE DNI = '$DNI' AND nombre_paciente = '$nombre_paciente'";
    $resultado = $conexion->query($sql_verificar_paciente);

    if ($resultado->num_rows > 0) {
        // Inicio de sesión exitoso, almacenamos datos del paciente en la sesión
        $paciente = $resultado->fetch_assoc();
        $_SESSION["paciente"] = $paciente;

        // Redirigimos a la página de inicio de sesión exitosa
        header("Location: menu_paciente.php");
        exit();
    } else {
        // Inicio de sesión fallido, mostramos un mensaje de error
        echo "";
    }
}

// Cerramos conexión
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Iniciar Sesión - Paciente</title>
</head>
<body>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4F5FF;
            margin: 0;
            margin-left: 450px;
            margin-top: 150px;
            padding: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 30px;
            font-size: 16px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            width: 200px;
            height: 70px;
            font-size: 20px;
        }

        button:hover {
            transition: 0.5s;
            color: black;
            background-color: #EC7063;
        }
        .container_inicio_paciente {
            text-align: center;
            padding: 60px;
            background-color: #AED6F1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            height: 300px;
        }
            </style>
    <div id="container_inicio_paciente">
        <h2>Iniciar Sesión - Paciente</h2>
        <form method="post" action="" onsubmit="validarFormulario()">
            <label for="DNI">DNI del Paciente:</label>
            <input type="text" id="DNI" name="DNI">

            <script>
                function dniValido(){
                    let valor = document.getElementById('DNI').value;

                    if (/^\d{8}[A-Za-z]$/.test(valor)) {
                        return true;
                    }
                        alert("Formato de DNI incorrecto.");
                        return false;
                }
                function validarFormulario() {
                    var nombre = document.getElementById("nombre_paciente");

                    var nombrePattern = /^[a-zA-Z]+$/; // Expresión regular para letras

                    if (nombre.value.trim() === "") {
                        alert ("Rellene el campo de nombre.");
                        return false; // Evitamos el envío del formulario si hay errores
                    } else if (!nombrePattern.test(nombre.value)) {
                        alert ("Solo se permiten letras en el nombre.");
                        return false;
                    } else {
                        nombreError.textContent = "";
                    }
                    alert("Usuario correcto.");
                    return true; // Envíamos el formulario si no hay errores
                }
            </script>

            <br><br>

            <label for="nombre_paciente">Nombre del Paciente:</label>
            <input type="text" id="nombre_paciente" name="nombre_paciente">

            <br><br>

            <button type="submit" onclick="dniValido()">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
