<?php
include_once('tienda.php');   // Incluye el archivo tienda.php dentro de este
$tienda = new Tienda(); // Crea el objeto que tiene las funciones de conexión a MySQL
$mysqli = $tienda->obten_conexion();    // Se obtiene la conexión

function tabla($resultado, $tabla_nombre, $tabla_descripcion)
{
    echo "<h2>Tabla {$tabla_nombre}</h2>";
    echo "<p>{$tabla_descripcion}</p>";
    $i = 0;
    echo '<table class="table table-sm table-striped table-bordered">';
    while ($fila = $resultado->fetch_assoc()) :    // Recorremos para ir fila x fila
        // Imprimimos los encabezados
        if ($i == 0) {
            echo "<thead><tr>";
            foreach (array_keys($fila) as $columna) {
                echo "<th>{$columna}</th>";
            }
            echo "</tr></thead><tbody>";
        }
        // Imprimimos los datos
        echo "<tr>";
        foreach ($fila as $dato) {
            echo "<td>{$dato}</td>";    // Se puede acceder también $fila["Id"]
        }
        echo "</tr>";
        $i++;
    endwhile;
    echo '</tbody></table>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas SQL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="m-4">
    <?php    
    // Obtenemos todos los fabricantes
    $resultado = null;  // Guarda el resultado de la consulta
    $consulta = "SELECT * FROM fabricante";
    // Creamos la consulta
    if ($sentencia = $mysqli->prepare($consulta)) {

        /* ejecutar la sentencia */
        $sentencia->execute();

        // Aqui queda el resultado
        $resultado = $sentencia->get_result();
        $sentencia->close();

        // Se envia el resultado de la consulta a la funcion para su impresión
        tabla($resultado, 'Fabricante', 'Ejercicio 1. Obtener todos los fabricantes.');
    }

    // Obtenemos los productos de Asus
    $resultado = null;  // Guarda el resultado de la consulta
    $consulta = "SELECT producto.id, producto.nombre, producto.precio, fabricante.nombre AS fabricante_nombre
                FROM fabricante INNER JOIN producto ON fabricante.id=producto.id_fabricante WHERE fabricante.nombre = ?";
    // Creamos la consulta
    if ($sentencia = $mysqli->prepare($consulta)) {

        // Se agregan los parametros de la consulta
        $fabricante_nombre = 'Asus';
        $sentencia->bind_param("s", $fabricante_nombre);

        /* ejecutar la sentencia */
        $sentencia->execute();

        // Aqui queda el resultado
        $resultado = $sentencia->get_result();
        $sentencia->close();

        tabla($resultado, 'Fabricante Asus', 'Ejercicio 2. Obtener los productos del fabricante llamado Asus.');
    }

    // Obtenemos precio promedio que no sean Crucial
    $resultado = null;  // Guarda el resultado de la consulta
    $consulta = "SELECT fabricante.nombre, AVG(producto.precio) precio_promedio FROM fabricante INNER JOIN producto ON 
                fabricante.id=producto.id_fabricante WHERE fabricante.nombre != ? GROUP BY fabricante.nombre HAVING AVG(producto.precio) > ?";
    // Creamos la consulta
    if ($sentencia = $mysqli->prepare($consulta)) {

        // Se agregan los parametros de la consulta
        $fabricante_nombre = 'Crucial';
        $precio = 150;
        $sentencia->bind_param("si", $fabricante_nombre, $precio);

        /* ejecutar la sentencia */
        $sentencia->execute();

        // Aqui queda el resultado
        $resultado = $sentencia->get_result();
        $sentencia->close();

        tabla($resultado, 'Precios promedio', 'Ejercicio 3. Obtener obtener el precio promedio por cada fabricante de computadoras mayores a 150 y que no sea Crucial.');
    }

    ?>
</body>

</html>