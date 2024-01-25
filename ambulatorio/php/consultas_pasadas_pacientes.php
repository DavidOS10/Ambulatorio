<?php
require_once("conecta.php");
session_start();

// Verificamos si el paciente ha iniciado sesión
if (!isset($_SESSION["paciente"])) {
    // Si no ha iniciado sesión, redirigimos a la página de inicio de sesión
    header("Location: login_paciente.php");
    exit();
}

// Obtener datos del paciente desde la sesión
$paciente = $_SESSION["paciente"];

// Obtenemos el ID de la consulta desde el parámetro de la URL
$id_consulta = isset($_GET['id']) ? $_GET['id'] : null;

// Obtenemos detalles de la consulta específica
$sql_detalle_consulta = "SELECT c.id_consulta, c.fecha_consulta, c.sintomatologia, c.diagnostico, m.nombre_medico, m.apellidos_medico
                       FROM consulta c
                       INNER JOIN medico m ON c.id_medico = m.id_medico
                       WHERE c.id_paciente = ? AND c.id_consulta = ?";

$detalle_consulta = array();

// Preparamos una sentencia SQL para obtener detalles de una consulta
$stmt_detalle_consulta = $conexion->prepare($sql_detalle_consulta);
// Vinculamos parámetros a la sentencia preparada
//Se utilizan los métodos bind_param para vincular parámetros a la sentencia preparada. 
$stmt_detalle_consulta->bind_param("ii", $paciente["id_paciente"], $id_consulta);
// Ejecutamos la sentencia preparada
$stmt_detalle_consulta->execute();
// Obtenemos el resultado de la consulta
$result_detalle_consulta = $stmt_detalle_consulta->get_result();

// Almacenamos resultados en un array
while ($detalle = $result_detalle_consulta->fetch_assoc()) {
    $detalle_consulta[] = $detalle;
}

$stmt_detalle_consulta->close();

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
<?php
    // Verificamos si hay detalles de la consulta para mostrar
    if (!empty($detalle_consulta)) {
        // Iteramos sobre cada detalle de consulta y mostrar información básica
        foreach ($detalle_consulta as $detalle) {
            echo "<h1>ID de la consulta: {$detalle['id_consulta']}</h1>";
            echo "<h2>Cita del {$detalle['fecha_consulta']}</h2>";
        }

        // Mostramos una tabla para presentar detalles más específicos
        echo "<table>";
        echo "<tr>
                <th>
                    Médico Asignado
                </th>
                <th>
                    Sintomatología
                </th>
                <th>
                    Diagnóstico
                </th>
            </tr>";

        // Iteramos sobre cada detalle de consulta y mostrar detalles específicos en la tabla
        foreach ($detalle_consulta as $detalle) {
            echo "<tr>";
            echo "<td>{$detalle['nombre_medico']} {$detalle['apellidos_medico']}</td>";
            echo "<td>{$detalle['sintomatologia']}</td>";
            echo "<td>{$detalle['diagnostico']}</td>";
            echo "</tr>";
        }

        // Cerramos la tabla
        echo "</table>";
    } else {
        // Mostramos un mensaje si no se encontraron detalles para la consulta
        echo "<p>No se encontraron detalles para la consulta.</p>";
    }
?>

</body>
</html>