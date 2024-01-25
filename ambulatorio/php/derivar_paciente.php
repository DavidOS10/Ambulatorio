<?php
require_once("conecta.php");

// Verificamos si se ha enviado un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos datos del formulario
    $id_medico = mysqli_real_escape_string($conexion, $_POST['id_medico']);
    $sintomatologia = mysqli_real_escape_string($conexion, $_POST['sintomatologia']);
    $fecha_consulta = mysqli_real_escape_string($conexion, $_POST['fecha_consulta']);

    // Obtenemos id_paciente desde la sesión
    session_start();
    if (!isset($_SESSION["paciente"])) {
        // Redirigimos a la página de inicio de sesión si el paciente no ha iniciado sesión
        header("Location: login_paciente.php");
        exit();
    }
    $id_paciente = $_SESSION["paciente"]["id_paciente"];

    // Verificamos si el médico existe
    $sql_verificar_medico = "SELECT * FROM medico WHERE id_medico = '$id_medico'";
    $resultado_verificar_medico = $conexion->query($sql_verificar_medico);

    if ($resultado_verificar_medico->num_rows > 0) {
        // El médico existe, procedemos con la inserción en la tabla de consultas
        // Verificamos si el paciente ya tiene asignado un médico
        $sql_verificar_asignacion = "SELECT id_medico FROM paciente WHERE id_paciente = '$id_paciente'";
        $resultado_verificar_asignacion = $conexion->query($sql_verificar_asignacion);

        if ($resultado_verificar_asignacion->num_rows > 0) {
            // El paciente ya tiene asignado un médico, procedemos con la inserción en la tabla de consultas
            $sql_insert_consulta = "INSERT INTO consulta (id_medico, id_paciente, fecha_consulta, sintomatologia) VALUES
                ('$id_medico', '$id_paciente', '$fecha_consulta', '$sintomatologia')";
            
            // Ejecutamos la consulta
            if ($conexion->query($sql_insert_consulta) === TRUE) {
                echo "";
            } else {
                echo "Error al realizar la cita: " . $conexion->error . "<br>";
            }
        } else {
            // El paciente aún no tiene asignado un médico, actualizamos la tabla paciente con el nuevo médico
            $sql_actualizar_paciente = "UPDATE paciente SET id_medico = '$id_medico' WHERE id_paciente = '$id_paciente'";
            if ($conexion->query($sql_actualizar_paciente) === TRUE) {
                // Ahora, procedemos con la inserción en la tabla de consultas
                $sql_insert_consulta = "INSERT INTO consulta (id_medico, id_paciente, fecha_consulta, sintomatologia) VALUES
                    ('$id_medico', '$id_paciente', '$fecha_consulta', '$sintomatologia')";
                // Ejecutamos la consulta
                if ($conexion->query($sql_insert_consulta) === TRUE) {
                    echo "";
                } else {
                    echo "Error al realizar la cita con el nuevo médico: " . $conexion->error . "<br>";
                }
            } else {
                echo "Error al actualizar el ID del médico en la tabla paciente: " . $conexion->error . "<br>";
            }
        }
    } else {
        // El médico no existe
        echo "Especialista no encontrado.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedir cita</title>
</head>
<body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4F5FF;
            margin: 0;
            padding: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1, h2 {
            color: #333;
        }
        .form-container {
            text-align: center;
            background-color: #AED6F1;
            padding: 30px;
            border-radius: 10px;
            margin-top: 40px;
        }
        select, input {
            margin-bottom: 10px;
            width: 460px;
            height: 30px;
        }
        textarea{
            margin-bottom: 10px;
            width: 460px;
            height: 100px;
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
            margin-left: 35px;
            margin-top: 40px;
            background-color: #AED6F1;
        }
        button:hover{
            transition: 0.5s;
            color: black;
            background-color: #EC7063;
        }
    </style>
    <div class="form-container">
    <form method="post" action="">
    <label for="id_medico"><h2>Seleccionar Especialista:</h2></label>
    <select name="id_medico" required>
    <?php
            // Obtenemos la lista de médicos
            $sql_obtener_medicos = "SELECT * FROM medico";
            $resultado_obtener_medicos = $conexion->query($sql_obtener_medicos);

            while ($fila_medico = $resultado_obtener_medicos->fetch_assoc()) {
                echo "<option value='" . $fila_medico['id_medico'] . "'>" . $fila_medico['nombre_medico'] . " " . $fila_medico['apellidos_medico'] . " - " . $fila_medico['especialidad'] . "</option>";
            }
            ?>
    </select>

        <label for="sintomatologia"><h2>Sintomatología:</h2></label>
        <textarea name="sintomatologia" id="sintomatologia"></textarea>

        <label for="fecha_consulta"><h2>Fecha de la consulta:</h2></label>
        <input type="date" name="fecha_consulta" id="fecha_consulta" value="aaaa/mm/dd" required oninput="validarFecha()">
        <script>
            function validarFormulario() {
                // Obtenemos la fecha actual sin la hora
                var fechaActual = new Date();
                    fechaActual.setHours(0, 0, 0, 0); // Establecer la hora a medianoche

                    // Obtenemos la fecha seleccionada por el usuario
                    var fechaSeleccionada = new Date(document.getElementById('fecha_consulta').value);
                    fechaSeleccionada.setHours(0, 0, 0, 0); // Establecer la hora a medianoche

                    // Comparamos las fechas
                    if (fechaSeleccionada < fechaActual) {
                        alert("No se puede seleccionar una fecha anterior a la de hoy.");
                        // Evitamos que se envíe el formulario
                        return false;
                    }

                // Validamos el campo de sintomatología
                var sintomatologia = document.getElementById('sintomatologia').value.trim();
                    if (sintomatologia === "") {
                        alert("El campo de sintomatología no puede estar vacío.");
                        return false;
                    } else if (sintomatologia.length > 100) {
                        alert("El campo de sintomatología no puede superar los 100 caracteres.");
                        return false;
                    }

                    // Continuamos con el envío del formulario si todas las validaciones son exitosas
                    alert("Paciente derivado correctamente.");
                    return true;
                }
        </script>

        <br>

        <button type="submit">Agregar Cita</button>
        </form>
        <a href="../php/menu_medico.php"><button type="">Volver</button></a>
    </div>
</body>
