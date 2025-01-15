<?php
require_once("conex.php");

// Función para ingresar un nuevo estudiante
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $grado = $_POST['grado'];

    $query = "INSERT INTO estudiantes (nombre_estudiante, apellido1_estudiante, apellido2_estudiante, grado) VALUES (?, ?, ?, ?)";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssss", $nombre, $apellido1, $apellido2, $grado);
        $stmt->execute();
        $stmt->close();
        echo "<p>Estudiante ingresado exitosamente.</p>";
    }
}

// Función para editar un estudiante
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $grado = $_POST['grado'];

    $query = "UPDATE estudiantes SET nombre_estudiante = ?, apellido1_estudiante = ?, apellido2_estudiante = ?, grado = ? WHERE id_estudiante = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssssi", $nombre, $apellido1, $apellido2, $grado, $id);
        $stmt->execute();
        $stmt->close();
        echo "<p>Estudiante actualizado exitosamente.</p>";
    }
}

// Función para borrar un estudiante
if (isset($_POST['borrar'])) {
    $id_borrar = $_POST['id_borrar'];
    $query = "DELETE FROM estudiantes WHERE id_estudiante = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $id_borrar);
        $stmt->execute();
        $stmt->close();
        echo "<p>Estudiante borrado exitosamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Estudiantes</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
    <header>
        <span id="presentacion">BASE DE DATOS - LABOR SOCIAL</span>
    </header>

    <h2 id="subtitulo">Administrar estudiantes</h2>

    <!-- Formulario de Ingreso -->
    <h3>Ingreso de Estudiante</h3>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        
        <label for="apellido1">Apellido 1:</label>
        <input type="text" name="apellido1" id="apellido1" required>
        
        <label for="apellido2">Apellido 2:</label>
        <input type="text" name="apellido2" id="apellido2" required>
        
        <label for="grado">Grado:</label>
        <input type="text" name="grado" id="grado" required>
        
        <button type="submit" name="guardar">Guardar</button>
    </form>
       <!-- Formulario de Edición -->
       <?php
    if (isset($_POST['editar'])) {
        $id_editar = $_POST['id_editar'];
        $query = "SELECT * FROM estudiantes WHERE id_estudiante = ?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param("i", $id_editar);
            $stmt->execute();
            $stmt->bind_result($id_estudiante, $nombre_estudiante, $apellido1_estudiante, $apellido2_estudiante, $grado);
            $stmt->fetch();
            ?>
            <h3>Editar Estudiante</h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $id_estudiante; ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" value="<?php echo $nombre_estudiante; ?>" required>
                
                <label for="apellido1">Apellido 1:</label>
                <input type="text" name="apellido1" value="<?php echo $apellido1_estudiante; ?>" required>
                
                <label for="apellido2">Apellido 2:</label>
                <input type="text" name="apellido2" value="<?php echo $apellido2_estudiante; ?>" required>
                
                <label for="grado">Grado:</label>
                <input type="text" name="grado" value="<?php echo $grado; ?>" required>
                
                <button type="submit" name="actualizar">Actualizar</button>
            </form>
            <?php
            $stmt->close();
        }
    }
    ?>
    <!-- Tabla de estudiantes -->
    <table border="1">
        <tr>
            <td colspan="6">Lista de Estudiantes</td>
        </tr>
        <tr>
            <td>ID Estudiante</td>
            <td>Nombre</td>
            <td>Apellido 1</td>
            <td>Apellido 2</td>
            <td>Grado</td>
            <td>Acciones</td>
        </tr>

        <?php
        // Mostrar los estudiantes existentes
        $query = "SELECT * FROM estudiantes";
        if ($stmt = $con->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id_estudiante, $nombre_estudiante, $apellido1_estudiante, $apellido2_estudiante, $grado);
            while ($stmt->fetch()) {
                echo "<tr>";
                echo "<td>".$id_estudiante. "</td>";
                echo "<td>".$nombre_estudiante. "</td>";
                echo "<td>".$apellido1_estudiante. "</td>";
                echo "<td>".$apellido2_estudiante. "</td>";
                echo "<td>".$grado. "</td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_editar' value='".$id_estudiante."'>
                            <button type='submit' name='editar'>Editar</button>
                        </form>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_borrar' value='".$id_estudiante."'>
                            <button type='submit' name='borrar'>Borrar</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            $stmt->close();
        }
        ?>
    </table>
        <button><a href="http://localhost/Proyecto-Final-BD">VOLVER A LA PANTALLA PRINCIPAL</a></button>
 
</body>
</html>

