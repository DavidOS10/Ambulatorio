<?php
require_once("conecta.php");
// Iniciamos sesión
session_start();

// Verificamos si se ha enviado un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos datos del formulario
$id_medico = mysqli_real_escape_string($conexion, $_POST['id_medico']);
$sintomatologia = mysqli_real_escape_string($conexion, $_POST['sintomatologia']);
$fecha_consulta = mysqli_real_escape_string($conexion, $_POST['fecha_consulta']);

// Obtenemos el id_paciente del paciente que ha iniciado sesión
$id_paciente = $_SESSION["paciente"]["id_paciente"];

// Consulta SQL para verificar si el médico existe
$sql_verificar_medico_asignado = "SELECT m.*
                                    FROM medico m
                                    JOIN paciente p ON m.id_medico = p.id_medico
                                    WHERE p.id_paciente = '$id_paciente'";
$resultado_verificar_medico_asignado = $conexion->query($sql_verificar_medico_asignado);

if ($resultado_verificar_medico_asignado->num_rows > 0) {
    // El médico existe, procedemos con la inserción en la tabla de consultas
    $sql_insert_consulta = "INSERT INTO consulta (id_medico, id_paciente, fecha_consulta, sintomatologia) VALUES
        ('$id_medico', '$id_paciente', '$fecha_consulta', '$sintomatologia')";

    // Ejecutamos la consulta
    if ($conexion->query($sql_insert_consulta) === TRUE) {
        "";
    } else {
        echo "Error al realizar la cita: " . $conexion->error . "<br>";
    }
} else {
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
    <form id="citaForm" onsubmit="return validarFormulario()" method="post">
        <?php
        $id_paciente_actual = $_SESSION["paciente"]["id_paciente"];

        // Consulta para obtener los valores de id_medico de la tabla paciente
        $sql_obtener_id_medicos = "SELECT id_medico FROM paciente WHERE id_paciente = '$id_paciente_actual'";
        
        $resultado_obtener_id_medicos = $conexion->query($sql_obtener_id_medicos);
        
        if ($resultado_obtener_id_medicos->num_rows > 0) {
            // Extraemos los valores de id_medico
            $fila_id_medicos = $resultado_obtener_id_medicos->fetch_assoc();
            $id_medicos = $fila_id_medicos['id_medico'];
        
            // Ahora podemos usar $id_medicos en tu consulta
            $sql_obtener_medicos_asignados = "SELECT id_medico, nombre_medico, apellidos_medico, especialidad
                                               FROM medico
                                               WHERE id_medico IN ($id_medicos)";
        
            $resultado_obtener_medicos_asignados = $conexion->query($sql_obtener_medicos_asignados);
        
            if ($resultado_obtener_medicos_asignados->num_rows > 0) {
                // Construimos el select
                echo '<label for="id_medico"><h2>Seleccionar Especialista:</h2></label>';
                echo '<select name="id_medico" required>';
        
                while ($fila_medico_asignado = $resultado_obtener_medicos_asignados->fetch_assoc()) {
                    $id_medico = $fila_medico_asignado['id_medico'];
                    $nombre_medico = $fila_medico_asignado['nombre_medico'] . ' ' . $fila_medico_asignado['apellidos_medico'];
                    $especialidad_medico = $fila_medico_asignado['especialidad'];
        
                    echo "<option value='$id_medico'>$nombre_medico - $especialidad_medico</option>";
                }
        
                echo '</select>';
            } else {
                echo "No hay médicos asignados al paciente actual.<br>";
            }
        } else {
            echo "No se encontró el paciente actual en la base de datos.<br>";
        }
        ?>

        <label for="sintomatologia"><h2>Sintomatología:</h2></label>
        <textarea name="sintomatologia" id="sintomatologia"></textarea>

        <label for="fecha_consulta"><h2>Fecha de la consulta:</h2></label>
        <input type="date" name="fecha_consulta" id="fecha_consulta" value="aaaa/mm/dd" required>

        <br>

        <button type="submit">Agregar Cita</button>
        </form>
        <script>
            function validarFormulario() {
                 // Obtener la fecha actual sin la hora
                var fechaActual = new Date();
                fechaActual.setHours(0, 0, 0, 0); // Establecer la hora a medianoche

                // Obtener la fecha seleccionada por el usuario
                var fechaSeleccionada = new Date(document.getElementById('fecha_consulta').value);
                fechaSeleccionada.setHours(0, 0, 0, 0); // Establecer la hora a medianoche

                // Comparar las fechas
                if (fechaSeleccionada < fechaActual) {
                    alert("No se puede seleccionar una fecha anterior a la de hoy.");
                    // Evitar que se envíe el formulario
                    return false;
                }

                // Validar sintomatología
                var sintomatologia = document.getElementById('sintomatologia').value.trim();
                if (sintomatologia === "") {
                    alert("El campo de sintomatología no puede estar vacío.");
                    return false;
                } else if (sintomatologia.length > 100) {
                    alert("El campo de sintomatología no puede superar los 100 caracteres.");
                    return false;
                }

                // Continuar con el envío del formulario si todas las validaciones son exitosas
                alert("Cita agregada correctamente.");
                return true;
            }
    </script>
        <a href="../php/menu_paciente.php"><button type="">Volver</button></a>
    </div>
</body>
</html>
