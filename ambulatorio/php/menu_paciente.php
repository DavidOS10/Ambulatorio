<?php
require_once ("conecta.php");
// Iniciamos sesión
session_start();

// Verificamos si el paciente ha iniciado sesión
if (!isset($_SESSION["paciente"])) {
    // Si no ha iniciado sesión, redirigimos a la página de inicio de sesión
    header("Location: login_paciente.php");
    exit();
}

// Obtenemos datos del paciente desde la sesión
$paciente = $_SESSION["paciente"];

// Obtenemos próximas citas del paciente
$sql_proximas_citas = "SELECT c.id_consulta, c.fecha_consulta, m.nombre_medico, m.apellidos_medico
                       FROM consulta c
                       INNER JOIN medico m ON c.id_medico = m.id_medico
                       WHERE c.id_paciente = ? AND c.fecha_consulta >= CURDATE()
                       ORDER BY c.fecha_consulta";
$proximas_citas = array();

// Obtenemos próximas citas para el paciente actual
$stmt = $conexion->prepare($sql_proximas_citas);
$stmt->bind_param("i", $paciente["id_paciente"]);
$stmt->execute();
$result_proximas_citas = $stmt->get_result();

// Almacenamos resultados en un array
while ($cita = $result_proximas_citas->fetch_assoc()) {
    $proximas_citas[] = $cita;
}

$stmt->close();

// Obtenemos próximas citas del paciente
$sql_anteriores_citas = "SELECT c.id_consulta, c.fecha_consulta, m.nombre_medico, m.apellidos_medico
                       FROM consulta c
                       INNER JOIN medico m ON c.id_medico = m.id_medico
                       WHERE c.id_paciente = ? AND c.fecha_consulta < CURDATE()
                       ORDER BY c.fecha_consulta ";
$anteriores_citas = array();

// Preparamos una sentencia SQL para obtener detalles de una consulta
$stmt_citas_anteriores = $conexion->prepare($sql_anteriores_citas);
// Vinculamos parámetros a la sentencia preparada
$stmt_citas_anteriores->bind_param("i", $paciente["id_paciente"]);
// Ejecutamos la sentencia preparada
$stmt_citas_anteriores->execute();
// Obtenemos el resultado de la consulta
$result_anteriores_citas = $stmt_citas_anteriores->get_result();

// Almacenamos resultados en un array
while ($cita_pasada = $result_anteriores_citas->fetch_assoc()) {
    $anteriores_citas[] = $cita_pasada;
}

$stmt_citas_anteriores->close();

// Obtenemos datos de la medicacion del paciente
$sql_posologia_paciente = "SELECT r.id_consulta, r.posologia, r.fecha_fin
                           FROM receta r
                           INNER JOIN consulta c ON r.id_consulta = c.id_consulta
                           WHERE c.id_paciente = ?";

// Preparamos una sentencia SQL para obtener detalles de una consulta
$stmt_posologia_paciente = $conexion->prepare($sql_posologia_paciente);
// Vinculamos parámetros a la sentencia preparada
$stmt_posologia_paciente->bind_param("i", $paciente["id_paciente"]);
// Ejecutamos la sentencia preparada
$stmt_posologia_paciente->execute();
// Obtenemos el resultado de la consulta
$resultado_posologia_paciente = $stmt_posologia_paciente->get_result();
//Cerramos la consulta
$stmt_posologia_paciente->close();


// Cerramos conexión
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Paciente</title>
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
        }
        th, td {
            border: 2px solid #000000;
            padding: 8px;
            text-align: left;
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
            background-color: #AED6F1;
        }
        button:hover{
            transition: 0.5s;
            color: black;
            background-color: #EC7063;
        }
    </style>
</head>
<body>
    <h1>Bienvenido, <?php echo $paciente["nombre_paciente"] . " " . $paciente["apellidos_paciente"]; ?></h1>

    <h2>Perfil del Paciente</h2>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>DNI</th>
        </tr>
        <tr>
            <td>
                <?php 
                    echo $paciente["nombre_paciente"]; 
                ?>
            </td>
            <td>
                <?php 
                    echo $paciente["apellidos_paciente"]; 
                ?>
            </td>
            <td>
                <?php 
                    echo $paciente["DNI"]; 
                ?>
            </td>
        </tr>
    </table>

    <!-- Código para mostrar próximas citas -->
    <?php
    // Mostramos información sobre próximas citas
    if (!empty($proximas_citas)) {
        echo "<h2>Próximas Citas</h2>";
        echo "<table>";
        echo "<tr><th>ID Cita</th><th>Fecha</th><th>Médico Asignado</th></tr>";

        foreach ($proximas_citas as $cita) {
            // Mostramos cada fila de la tabla con detalles de la próxima cita
            echo "<tr>";
            echo "<td>{$cita['id_consulta']}</td>";
            echo "<td>{$cita['fecha_consulta']}</td>";
            echo "<td>{$cita['nombre_medico']} {$cita['apellidos_medico']}</td>";
            echo "</tr>";
        }

        // Cerramos la tabla
        echo "</table>";
    } else {
        // Mensaje si no hay próximas citas
        echo "<h2>Próximas Citas</h2>";
        echo "<p>No hay próximas citas.</p>";
    }
?>

<h2>Medicación del paciente</h2>
<table>
    <tr>
        <th>ID de la consulta</th>
        <th>Posología</th>
        <th>Fecha de duración de dosis</th>
    </tr>
    <?php
        // Mostramos la medicación actual del paciente
        while ($fila = $resultado_posologia_paciente->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$fila['id_consulta']}</td>";
            echo "<td>{$fila['posologia']}</td>";
            echo "<td>{$fila['fecha_fin']}</td>";
            echo "</tr>";
        }
    ?>
</table>

    <?php
        // Mostrarmos información sobre citas médicas pasadas
        if (!empty($anteriores_citas)) {
            echo "<h2>Citas Pasadas</h2>";
            echo "<table>";
            echo "<tr>
                    <th>ID Cita
                    </th>
                    <th>Fecha
                    </th>
                    <th>Médico Asignado
                    </th>
                </tr>";

            foreach ($anteriores_citas as $cita_pasada) {
                // Mostramos cada fila de la tabla con detalles de la cita pasada
                echo "<tr>";
                echo "<td>{$cita_pasada['id_consulta']}</td>";
                // Creamos un enlace a la página consultas_pasadas_pacientes.php con el ID de la cita
                echo "<td><a href ='consultas_pasadas_pacientes.php?id={$cita_pasada['id_consulta']}'>{$cita_pasada['fecha_consulta']}</a></td>";
                echo "<td>{$cita_pasada['nombre_medico']} {$cita_pasada['apellidos_medico']}</td>";
                echo "</tr>";
            }

            // Cerramos la tabla
            echo "</table>";
        } else {
            // Mensaje si no hay citas médicas pasadas
            echo "<h2>Citas Pasadas</h2>";
            echo "<p>No tiene citas pasadas.</p>";
        }
    ?>

    <br><br>

    <a href="../php/solicitar_consulta.php"><button type="submit">Solicitar cita</button></a>
    <a href="../html/index.html"><button type="submit">Cerrar Sesión</button></a>

</body>
</html>
