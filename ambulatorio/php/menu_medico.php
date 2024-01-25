<?php
require_once ("conecta.php");
// Iniciamos sesión
session_start();

// Verificamos si el medico ha iniciado sesión
if (!isset($_SESSION["medico"])) {
    // Si no ha iniciado sesión, redirigimos a la página de inicio de sesión
    header("Location: menu_medico.php");
    exit();
}

// Obtenemos datos del medico desde la sesión
$medico = $_SESSION["medico"];

// Obtenemos próximas citas del medico
$sql_proximas_citas = "SELECT c.id_consulta, c.fecha_consulta, c.sintomatologia, p.nombre_paciente, p.apellidos_paciente
                       FROM consulta c
                       INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                       WHERE c.id_medico = ? AND c.fecha_consulta > CURDATE()
                       ORDER BY c.fecha_consulta";
$proximas_citas = array();

// Preparamos una sentencia SQL para obtener detalles de una consulta
$stmt = $conexion->prepare($sql_proximas_citas);
// Vinculamos parámetros a la sentencia preparada
//Se utilizan los métodos bind_param para vincular parámetros a la sentencia preparada. 
$stmt->bind_param("i", $medico["id_medico"]);
// Ejecutamos la sentencia preparada
$stmt->execute();
// Obtenemos el resultado de la consulta
$result_proximas_citas = $stmt->get_result();

// Almacenamos resultados en un array
while ($cita = $result_proximas_citas->fetch_assoc()) {
    $proximas_citas[] = $cita;
}

$stmt->close();

// Obtenemos próximas citas del medico
$sql_citas_hoy = "SELECT c.id_consulta, c.fecha_consulta, c.sintomatologia, p.nombre_paciente, p.apellidos_paciente, p.id_paciente
                       FROM consulta c
                       INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                       WHERE c.id_medico = ? AND c.fecha_consulta = CURDATE()
                       ORDER BY c.fecha_consulta";
$citas_hoy = array();

// Obtenemos próximas citas para el medico actual
$stmt = $conexion->prepare($sql_citas_hoy);
$stmt->bind_param("i", $medico["id_medico"]);
$stmt->execute();
$result_citas_hoy = $stmt->get_result();

// Almacenamos resultados en un array
while ($cita_hoy = $result_citas_hoy->fetch_assoc()) {
    $citas_hoy[] = $cita_hoy;
}

$stmt->close();

// Cerramos conexión 
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del medico</title>
    <!-- Estilos CSS exclusivos de esta pagina ya que el general descolocaria todo -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4F5FF;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background-color: #AED6F1;
            justify-content: center;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
            background-color: #AED6F1;
        }
        th {
            background-color: #AED6F1;
        }
        .citas-pasadas {
            margin-top: 20px;
        }
        .cita {
            margin-bottom: 10px;
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
            margin-left: 0px;
            margin-top: 0px;
        }
        button:hover{
            transition: 0.5s;
            color: black;
            background-color: #EC7063;
        }
        #pasar_consulta {
            padding: 10px 20px;
            margin: 30px;
            font-size: 16px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            width: 200px;
            height: 70px;
            font-size: 20px;
            margin-left: 40px;
            margin-top: 40px;
        }
        #pasar_consulta:hover{
            transition: 0.5s;
            color: black;
            background-color: #EC7063;
        }
    </style>
</head>
<body>
    <h1>Bienvenido, <?php echo $medico["nombre_medico"] . " " . $medico["apellidos_medico"]; ?></h1>

    <h2>Perfil del medico</h2>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Especialidad</th>
        </tr>
        <tr>
            <td>
                <?php 
                    echo $medico["nombre_medico"]; 
                ?>
            </td>
            <td>
                <?php 
                    echo $medico["apellidos_medico"]; 
                ?>
            </td>
            <td>
                <?php 
                    echo $medico["especialidad"]; 
                ?>
            </td>
        </tr>
    </table>

    <!-- Código para mostrar próximas citas -->
    <?php
        // Mostramos información sobre próximas citas en los siguientes 7 días
        if (!empty($proximas_citas)) {
            // Obtenemos el número de próximas citas
            $num_citas = count($proximas_citas);
            
            // Mostramos el número de próximas citas
            echo "<h2>Número de Próximas Citas de los siguientes 7 días: $num_citas</h2>";
            
            // Mostramos una tabla con detalles de cada próxima cita
            echo "<table>";
            echo "<tr><th>ID Cita</th><th>Paciente</th><th>Sintomatología</th></tr>";

            foreach ($proximas_citas as $cita) {
                // Mostramos cada fila de la tabla con detalles de la cita
                echo "<tr>";
                echo "<td>{$cita['id_consulta']}</td>";
                echo "<td>{$cita['nombre_paciente']} {$cita['apellidos_paciente']}</td>";
                echo "<td>{$cita['sintomatologia']}</td>";
                echo "</tr>";
            }

            // Cerramos la tabla
            echo "</table>";
        } else {
            // Mensaje si no hay próximas citas
            echo "<h2>Número de Próximas Citas de los siguientes 7 días: 0</h2>";
            echo "<p>No hay próximas citas.</p>";
        }
    ?>

    <?php
        // Mostramos información sobre citas programadas para hoy
        if (!empty($citas_hoy)) {
            // Mostramos un encabezado para las citas de hoy
            echo "<h2>Citas de hoy</h2>";
            
            // Mostramos una tabla con detalles de cada cita programada para hoy
            echo "<table>";
            echo "<tr><th>ID Cita</th><th>Paciente</th><th>Sintomatología</th><th>Pasar Consulta</th></tr>";

            foreach ($citas_hoy as $cita_hoy) {
                // Mostramos cada fila de la tabla con detalles de la cita para hoy
                echo "<tr>";
                echo "<td>{$cita_hoy['id_consulta']}</td>";
                echo "<td>{$cita_hoy['nombre_paciente']} {$cita_hoy['apellidos_paciente']}</td>";
                echo "<td>{$cita_hoy['sintomatologia']}</td>";
                // Creamos un enlace para "Pasar Consulta" con el ID de la cita
                echo "<td><a href='pasar_consulta.php?id_consulta={$cita_hoy['id_consulta']}'><button type='submit' id='pasar_consulta'>Pasar consulta</button></a></td>";
                echo "</tr>";
            }

            // Cerrar la tabla
            echo "</table>";
        } else {
            // Mensaje si no hay citas programadas para hoy
            echo "<h2>No tiene citas para hoy.</h2>";
            echo "<p>No hay citas.</p>";
        }
    ?>

    </form>
    </div>

    <br>

    <a href="../html/index.html"><button type="submit">Cerrar Sesión</button></a>
</body>
</html>
