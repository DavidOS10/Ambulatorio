<?php
require_once("conecta.php");

// Iniciamos sesión
session_start();

// Verificamos el inicio de sesión del médico
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos los datos del formulario
    $id_medico = $_POST["id_medico"];
    $nombre_medico = $_POST["nombre_medico"];

    // Consultamos la base de datos para verificar el inicio de sesión
    $sql_verificar_medico = "SELECT * FROM medico WHERE id_medico = '$id_medico' AND nombre_medico = '$nombre_medico'";
    $resultado = $conexion->query($sql_verificar_medico);

    if ($resultado->num_rows > 0) {
        // Inicio de sesión exitoso, almacenamos datos del medico en la sesión
        $medico = $resultado->fetch_assoc();
        $_SESSION["medico"] = $medico;

        // Redirigimos a la página de inicio de sesión exitosa
        header("Location: menu_medico.php");
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
    <title>Iniciar Sesión - Médico</title>
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
        #container_inicio_medico {
            text-align: center;
            padding: 60px;
            background-color: #AED6F1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            height: 300px;
        }
    </style>
    <div id="container_inicio_medico">
        <h2>Iniciar Sesión - Médico</h2>
        <form method="post" action="" onsubmit="validarFormulario()">
            <label for="id_medico">ID del Médico:</label>
            <input type="text" id="id_medico" name="id_medico">

            <br><br>

            <label for="nombre_medico">Nombre del Médico:</label>
            <input type="text" id="nombre_medico" name="nombre_medico">

            <script>
                function validarFormulario() {
                    var nombre = document.getElementById("nombre_medico");
                    var numero = document.getElementById("id_medico");

                    var nombrePattern = /^[a-zA-Z]+$/; // Expresión regular para letras
                    var numeroPattern = /^\d+$/; // Expresión regular para números

                    if (nombre.value.trim() === "") {
                        alert("Rellene el campo de nombre.");
                        return false; // Evitamos el envío del formulario si hay errores
                    } else if (!nombrePattern.test(nombre.value)) {
                        alert("Solo se permiten letras en el nombre.");
                        return false;
                    }

                    if (numero.value.trim() === "") {
                        alert("Rellene el campo de ID médico.");
                        return false; // Evitamos el envío del formulario si hay errores
                    } else if (!numeroPattern.test(numero.value)) {
                        alert("Solo se permiten números en el ID del médico.");
                        return false;
                    }
                    alert("Usuario correcto.");
                    return true; // Envíamos el formulario si no hay errores
                }
            </script>

            <br><br>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
