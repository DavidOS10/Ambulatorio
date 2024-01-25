<?php
// Incluimos el archivo que contiene la conexión a la base de datos
require_once("conecta.php");

// Iniciamos la sesión
session_start();

// Manejo de la inserción de medicación cuando se envía el formulario 'anadir_medicacion'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['anadir_medicacion'])) {
    // Verificamos la existencia de las variables antes de usarlas
    if (isset($_POST['id_consulta'], $_POST['medicacion'], $_POST['posologia'])) {
        // Obtenemos los datos del formulario
        $id_consulta = mysqli_real_escape_string($conexion, $_POST['id_consulta']);
        $medicacion_seleccionada = mysqli_real_escape_string($conexion, $_POST['medicacion']);
        $posologia = mysqli_real_escape_string($conexion, $_POST['posologia']);
        $fecha_fin = mysqli_real_escape_string($conexion, $_POST['fecha_fin']);  // No parece estar en el formulario

        // Obtenemos el valor del checkbox 'es_cronico'
        $es_cronico = isset($_POST['es_cronico']) ? intval($_POST['es_cronico']) : 0;

        // Consulta SQL para insertar la medicación
        $sql_insert_medicacion = "INSERT INTO receta (id_consulta, id_medicamento, posologia, fecha_fin) VALUES
            ('$id_consulta', (SELECT id_medicamento FROM medicamento WHERE nombre_medicamento = '$medicacion_seleccionada'), '$posologia', CURDATE())";

        // Si es crónico, actualiza la fecha_fin sumando 1 año
        if ($es_cronico) {
            $sql_insert_medicacion = "INSERT INTO receta (id_consulta, id_medicamento, posologia, fecha_fin) VALUES
                ('$id_consulta', (SELECT id_medicamento FROM medicamento WHERE nombre_medicamento = '$medicacion_seleccionada'), '$posologia', DATE_ADD(CURDATE(), INTERVAL 1 YEAR))
                ON DUPLICATE KEY UPDATE fecha_fin = DATE_ADD(CURDATE(), INTERVAL 1 YEAR)";
                //DATE_ADD(CURDATE(), INTERVAL 1 YEAR): Es una función MySQL que agrega 1 año a la fecha actual (CURDATE()).
                //ON DUPLICATE KEY UPDATE: Indica que si hay un conflicto con una clave única (duplicado), se realizará una 
                //actualización en lugar de una inserción.
        } else {
            // Si no es crónico, deja la fecha_fin como está
            $sql_insert_medicacion = "INSERT INTO receta (id_consulta, id_medicamento, posologia, fecha_fin) VALUES
                ('$id_consulta', (SELECT id_medicamento FROM medicamento WHERE nombre_medicamento = '$medicacion_seleccionada'), '$posologia', CURDATE())
                ON DUPLICATE KEY UPDATE fecha_fin = CURDATE()";
        }

        // Ejecuta la consulta SQL
        if ($conexion->query($sql_insert_medicacion) === TRUE) {
            echo "Medicación añadida con éxito.<br>";
        } else {
            echo "Error al añadir medicación: " . $conexion->error . "<br>";
        }
    } else {
        echo "Faltan datos en el formulario.<br>";
    }
}

// Manejo de la actualización de diagnóstico cuando se envía el formulario 'actualizar_diagnostico'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_diagnostico'])) {
    // Obtenemos los datos del formulario
    $id_consulta = mysqli_real_escape_string($conexion, $_POST['id_consulta']);
    $sintomatologia = mysqli_real_escape_string($conexion, $_POST['sintomatologia']);
    $diagnostico = mysqli_real_escape_string($conexion, $_POST['diagnostico']);

    // Consulta SQL para actualizar la consulta con el nuevo diagnóstico
    $sql_actualizar_consulta = "UPDATE consulta 
                                SET sintomatologia = '$sintomatologia', diagnostico = '$diagnostico' 
                                WHERE id_consulta = $id_consulta";

    // Ejecuta la consulta SQL
    if ($conexion->query($sql_actualizar_consulta) === TRUE) {
        echo "Consulta actualizada con éxito.<br>";
    } else {
        echo "Error al actualizar consulta: " . $conexion->error . "<br>";
    }
}

// Verificamos si se ha proporcionado el parámetro 'id_consulta' en la URL
if (isset($_GET['id_consulta'])) {
    $id_consulta_detalle = $_GET['id_consulta'];
    
    // Consulta SQL para obtener detalles de la consulta y medicación asociada
    $sql_detalle_cita = "SELECT c.id_consulta, c.fecha_consulta, c.sintomatologia, m.nombre_medicamento, r.posologia, c.diagnostico
                    FROM consulta c
                    LEFT JOIN receta r ON c.id_consulta = r.id_consulta
                    LEFT JOIN medicamento m ON r.id_medicamento = m.id_medicamento
                    WHERE c.id_consulta = $id_consulta_detalle";

    // Ejecutamos la consulta
    $resultado_detalle_cita = $conexion->query($sql_detalle_cita);
    
    // Obtenemos los resultados de la consulta
    $detalle_cita = $resultado_detalle_cita->fetch_assoc();
} else {
    // Si no se proporciona 'id_consulta' en la URL, redirigimos a la página de perfil médico
    header("Location: perfil_medico.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Consulta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4F5FF;
            color: #000;
            margin: 50;
            padding: 0;
        }

        h1, h2 {
            color: #000;
        }

        p {
            color: #000;
        }

        form {
            margin-bottom: 20px;
        }

        textarea, input, select, button {
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        checkbox {
            margin-bottom: 10px;
            float: left;
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-left: 100px;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #AED6F1;
            color: #000;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #EC7063;
            color: #fff;
        }

        a {
            text-decoration: none;
            color: #000;
        }

        a:hover {
            color: #EC7063;
        }
    </style>
</head>
<body>
    <h1>Detalles de la Consulta</h1>

    <p><b>ID de Consulta:</b> <?php echo $detalle_cita['id_consulta']; ?></p>
    <p><b>Fecha de Consulta:</b> <?php echo $detalle_cita['fecha_consulta']; ?></p>

    <form method="post" action="" onsubmit="validarFormulario()">
    <input type="hidden" name="id_consulta" value="<?php echo $detalle_cita['id_consulta']; ?>">
    <h3>Sintomatología</h3>
    <textarea name="sintomatologia" id="sintomatologia"><?php echo $detalle_cita['sintomatologia']; ?></textarea>
    <br>
    <h3>Diagnóstico</h3>
    <textarea name="diagnostico" id="diagnostico"><?php echo $detalle_cita['diagnostico']; ?></textarea>
    <br><br>
    <button type="submit" name="actualizar_diagnostico">Actualizar</button>
</form>

    <h2>Medicación</h2>
        <?php
        // Obtener todas las medicaciones (incluida la actual) para la consulta actual
        $sql_medicaciones = "SELECT m.nombre_medicamento, r.posologia, r.fecha_fin
                            FROM receta r
                            LEFT JOIN medicamento m ON r.id_medicamento = m.id_medicamento
                            WHERE r.id_consulta = $id_consulta_detalle";

        $resultado_medicaciones = $conexion->query($sql_medicaciones);

        if ($resultado_medicaciones->num_rows > 0) {
            while ($fila_medicacion = $resultado_medicaciones->fetch_assoc()) {
                echo "<p><b>Medicación:</b> {$fila_medicacion['nombre_medicamento']}</p>";
                echo "<p><b>Posología:</b> {$fila_medicacion['posologia']}</p>";
                echo "<p><b>Fecha Fin:</b> {$fila_medicacion['fecha_fin']}</p>";
            }
        } else {
            echo "<p>No hay medicaciones anteriores.</p>";
        }
        ?>

    <h2>Añadir Medicación</h2>
    <form method="post" action="">
        <input type="hidden" name="id_consulta" value="<?php echo $detalle_cita['id_consulta']; ?>">

        <label for="medicacion"><b>Seleccionar Medicación:</b></label>
        <br><br>
        <select name="medicacion" required>
            <?php
             // Obtener todas las medicaciones disponibles para el paciente que dispone el ambulatorio
            $sql_obtener_medicacion = "SELECT * FROM medicamento";
            $resultado_obtener_medicacion = $conexion->query($sql_obtener_medicacion);

            while ($fila_medicacion = $resultado_obtener_medicacion->fetch_assoc()) {
                echo "<option value='" . $fila_medicacion['nombre_medicamento'] . "'>" . $fila_medicacion['nombre_medicamento'] . "</option>";
            }
            ?>
        </select>
    <br><br>
        <label for="posologia"><b>Posología:</b></label>
        <br><br>
        <input type="text" placeholder="dosis/mañana o tarde-cada que horas hay que tomarlo-durante cuanto" name="posologia" required>

        <label for="cronico"><b>Es crónico?</b></label>
        <input type="checkbox" name="cronico" id="cronico">
        <input type="hidden" name="es_cronico" id="es_cronico" value="0">

        <br><br>

        <button type="submit" name="anadir_medicacion">Añadir Medicación</button>
    </form>

    <script>
        // Función para validar el formulario
        function validarFormulario() {
            // Validar sintomatología
            var sintomatologia = document.getElementById('sintomatologia').value.trim();
            if (sintomatologia === "") {
                alert("El campo de sintomatología no puede estar vacío.");
                return false;
            } else if (sintomatologia.length > 100) {
                alert("El campo de sintomatología no puede superar los 100 caracteres.");
                return false;
            }

            // Validar diagnóstico
            var diagnostico = document.getElementById('diagnostico').value.trim();
            if (diagnostico === "") {
                alert("El campo de diagnóstico no puede estar vacío.");
                return false;
            }

            // Continuar con el envío del formulario si todas las validaciones son exitosas
            alert("Paciente derivado correctamente.");
            return true;
        }

        // Agregar un evento al cambio en la casilla de verificación 'cronico'
        document.getElementById('cronico').addEventListener('change', function () {
            // Establecer el valor del campo oculto 'es_cronico' según si la casilla está marcada o no
            if (this.checked) {
                document.getElementById('es_cronico').value = "1";
            } else {
                document.getElementById('es_cronico').value = "0";
            }
        });

    </script>

    <a href="derivar_paciente.php"><button type="submit">Derivar Paciente</button></a>
<br><br>
    <a href="menu_medico.php"><button type="submit">Volver al Perfil</button></a>
</body>
</html>
