<?php
require_once("conecta.php");

// Creamos tablas si no existen
$sql_medicamento = "CREATE TABLE IF NOT EXISTS medicamento (
    id_medicamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre_medicamento VARCHAR(255)
)";

$sql_medico = "CREATE TABLE IF NOT EXISTS medico (
    id_medico INT AUTO_INCREMENT PRIMARY KEY,
    nombre_medico VARCHAR(255) NOT NULL,
    apellidos_medico VARCHAR(255) NOT NULL,
    especialidad VARCHAR(255) NOT NULL
)";

$sql_paciente = "CREATE TABLE IF NOT EXISTS paciente (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    DNI VARCHAR(255) NOT NULL,
    nombre_paciente VARCHAR(255) NOT NULL,
    apellidos_paciente VARCHAR(255) NOT NULL,
    genero VARCHAR(10) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    id_medico VARCHAR(255)
)";

$sql_consulta = "CREATE TABLE IF NOT EXISTS consulta (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_medico INT NOT NULL,
    id_paciente INT NOT NULL,
    fecha_consulta DATE NOT NULL,
    diagnostico TEXT,
    sintomatologia TEXT,
    FOREIGN KEY (id_medico) REFERENCES medico(id_medico),
    FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente)
)";

$sql_receta = "CREATE TABLE IF NOT EXISTS receta (
    id_receta INT AUTO_INCREMENT PRIMARY KEY,
    id_medicamento INT,
    id_consulta INT,
    posologia VARCHAR(255) NOT NULL,
    fecha_fin DATE NOT NULL,
    FOREIGN KEY (id_medicamento) REFERENCES medicamento(id_medicamento),
    FOREIGN KEY (id_consulta) REFERENCES consulta(id_consulta)
)";

// Ejecutamos consultas
if ($conexion->query($sql_medicamento) === TRUE) {
    echo "Tabla 'medicamento' creada con éxito.<br>";
} else {
    echo "Error al crear la tabla 'medicamento': " . $conexion->error . "<br>";
}

if ($conexion->query($sql_medico) === TRUE) {
    echo "Tabla 'medico' creada con éxito.<br>";
} else {
    echo "Error al crear la tabla 'medico': " . $conexion->error . "<br>";
}

if ($conexion->query($sql_paciente) === TRUE) {
    echo "Tabla 'paciente' creada con éxito.<br>";
} else {
    echo "Error al crear la tabla 'paciente': " . $conexion->error . "<br>";
}

if ($conexion->query($sql_consulta) === TRUE) {
    echo "Tabla 'consulta' creada con éxito.<br>";
} else {
    echo "Error al crear la tabla 'consulta': " . $conexion->error . "<br>";
}

if ($conexion->query($sql_receta) === TRUE) {
    echo "Tabla 'receta' creada con éxito.<br>";
} else {
    echo "Error al crear la tabla 'receta': " . $conexion->error . "<br>";
}

// Insertamos datos iniciales
$sql_insert_medicamento = "INSERT INTO medicamento (nombre_medicamento) VALUES
    ('Polibutin'),
    ('Ibupofreno'),
    ('Eparina'),
    ('Fluticasona'),
    ('Dalsy'),
    ('Lorazepam'),
    ('Paracetamol')";

$sql_insert_medico = "INSERT INTO medico (nombre_medico, apellidos_medico, especialidad) VALUES
    ('Antonio', 'López Vera', 'Urología'),
    ('Mara', 'Sánchez Martín', 'Ginecología'),
    ('Ignacio', 'Fernández Romero', 'Cardiología'),
    ('Fernando', 'Rodríguez Bellido', 'Cabecera'),
    ('Sergio', 'Arias Martínez', 'Traumatología'),
    ('Cristina', 'Sánchez Romero', 'Endocrinología'),
    ('Beatriz', 'Rodrigo Marquínez', 'Pediatría')";

$sql_insert_paciente = "INSERT INTO paciente (DNI, nombre_paciente, apellidos_paciente, genero, fecha_nacimiento, id_medico) VALUES
    ('45735844S', 'Diego', 'Aceituno Villar', 'M', '2001-04-05', '1,4'),
    ('15430654P', 'Ana', 'Morrel Sánchez', 'F', '2007-03-16', '2,4'),
    ('25439643T', 'Maria', 'Cañadas Abad', 'F', '2005-05-31', '3,4'),
    ('85335324P', 'Roberto', 'Sánchez Romero', 'M', '2000-02-01', '4'),
    ('05435644P', 'David', 'Oñate Sánchez', 'M', '2003-01-28', '5,4'),
    ('85335324P', 'Lucía', 'Fernández Sánchez', 'F', '2003-01-30', '6,4'),
    ('52633694M', 'Raul', 'Serrano Nieto', 'M', '2015-08-07', '7,4')";

$sql_insert_receta = "INSERT INTO receta (id_consulta, posologia, fecha_fin) VALUES
    ('1', '1 cap/man-24h-2sem', '2023-12-20'),
    ('2', '2 cap/man-8h-4d', '2023-12-09'),
    ('3', '1 dosis/man-24h-1mes', '2024-01-04'),
    ('4', '2 cap/man-8h-4d', '2023-12-09'),
    ('5', '1 dosis/man-8h-1sem', '2023-12-12'),
    ('6', '1 cap/man-24h-1mes', '2024-01-05'),
    ('7', '2 dosis/man-12h-3sem', '2023-12-26')";  

$sql_insert_consulta = "INSERT INTO consulta (id_medico, id_paciente, fecha_consulta, diagnostico, sintomatologia) VALUES
    (1, 1, '2024-01-21', 'Molestias en la zona genital', 'Dolor al hacer pis'),
    (1, 1, '2022-12-17', 'Consulta rutinaria', 'Revisión'),
    (2, 2, '2023-10-16', 'Analisis rutinario', 'Consulta rutinaria'),
    (3, 3, '2023-12-29', 'Tension alta en relación a su edad', 'Taquicardias en relación a su tensión'),
    (4, 4, '2023-08-30', 'Dolor de cabeza y fiebre', 'Escalofríos y malestar'),
    (5, 5, '2022-05-19', 'Rotura de ligamento cruzado', 'Dolor intenso en la rodilla imposibilidandole caminar'),
    (5, 5, '2023-12-05', 'Torcedura de tobillo, posible esguince', 'Dolor intenso en el tobillo con hematoma e inflamcion'),
    (6, 6, '2023-01-30', 'Desorden alimentario', 'Poca energía y bajas defensas'),
    (7, 7, '2023-11-31', 'Sinusitis aguda', 'Dolor de cabeza, concentrado mas especificamente en la frente')";

if ($conexion->query($sql_insert_medicamento) === TRUE) {
    echo "Datos iniciales de medicamento insertados con éxito.<br>";
} else {
    echo "Error al insertar datos iniciales de medicamentos: " . $conexion->error . "<br>";
}

if ($conexion->query($sql_insert_medico) === TRUE) {
    echo "Datos iniciales de medico insertados con éxito.<br>";
} else {
    echo "Error al insertar datos iniciales de medico: " . $conexion->error . "<br>";
}

if ($conexion->query($sql_insert_paciente) === TRUE) {
    echo "Datos iniciales de paciente insertados con éxito.<br>";
} else {
    echo "Error al insertar datos iniciales de paciente: " . $conexion->error . "<br>";
}

if ($conexion->query($sql_insert_consulta) === TRUE) {
    echo "Datos iniciales de consulta insertados con éxito.<br>";
} else {
    echo "Error al insertar datos iniciales de consulta: " . $conexion->error . "<br>";
}

if ($conexion->query($sql_insert_receta) === TRUE) {
    echo "Datos iniciales de receta insertados con éxito.<br>";
} else {
    echo "Error al insertar datos iniciales de receta: " . $conexion->error . "<br>";
}

// Cerramos conexión
mysqli_close($conexion);
?>