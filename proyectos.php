<?php
require_once("conex.php");

// Función para agregar un nuevo proyecto
if (isset($_POST['guardar'])) {
    $nombre_proyecto = $_POST['nombre_proyecto'];
    $descripcion = $_POST['descripcion'];

    $query = "INSERT INTO proyectos (nombre_proyecto, descripcion) VALUES (?, ?)";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ss", $nombre_proyecto, $descripcion);
        $stmt->execute();
        $stmt->close();
        echo "<p>Proyecto agregado exitosamente.</p>";
    }
}

// Función para actualizar un proyecto
if (isset($_POST['actualizar'])) {
    $id_proyecto = $_POST['id_proyecto'];
    $nombre_proyecto = $_POST['nombre_proyecto'];
    $descripcion = $_POST['descripcion'];

    $query = "UPDATE proyectos SET nombre_proyecto = ?, descripcion = ? WHERE id_proyecto = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssi", $nombre_proyecto, $descripcion, $id_proyecto);
        $stmt->execute();
        $stmt->close();
        echo "<p>Proyecto actualizado exitosamente.</p>";
    }
}

// Función para borrar un proyecto
if (isset($_POST['borrar'])) {
    $id_borrar = $_POST['id_borrar'];
    $query = "DELETE FROM proyectos WHERE id_proyecto = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $id_borrar);
        $stmt->execute();
        $stmt->close();
        echo "<p>Proyecto borrado exitosamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Proyectos</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
    <header>
        <span id="presentacion">BASE DE DATOS - LABOR SOCIAL</span>
    </header>

    <h2 id="subtitulo">Administrar Proyectos</h2>

    <!-- Formulario de Ingreso -->
    <h3>Ingreso de Proyecto</h3>
    <form method="POST">
        <label for="nombre_proyecto">Nombre del Proyecto:</label>
        <input type="text" name="nombre_proyecto" id="nombre_proyecto" required>
        
        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" required></textarea>
        
        <button type="submit" name="guardar">Guardar</button>
    </form>
    <!-- Formulario de Edición -->
    <?php
    if (isset($_POST['editar'])) {
        $id_editar = $_POST['id_editar'];
        $query = "SELECT * FROM proyectos WHERE id_proyecto = ?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param("i", $id_editar);
            $stmt->execute();
            $stmt->bind_result($id_proyecto, $nombre_proyecto, $descripcion);
            $stmt->fetch();
            ?>
            <h3>Editar Proyecto</h3>
            <form method="POST">
                <input type="hidden" name="id_proyecto" value="<?php echo $id_proyecto; ?>">
                
                <label for="nombre_proyecto">Nombre del Proyecto:</label>
                <input type="text" name="nombre_proyecto" value="<?php echo $nombre_proyecto; ?>" required>
                
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" required><?php echo $descripcion; ?></textarea>
                
                <button type="submit" name="actualizar">Actualizar</button>
            </form>
            <?php
            $stmt->close();
        }
    }
    ?>
    <!-- Tabla de Proyectos -->
    <table border="1">
        <tr>
            <td colspan="4">Lista de Proyectos</td>
        </tr>
        <tr>
            <td>ID Proyecto</td>
            <td>Nombre del Proyecto</td>
            <td>Descripción</td>
            <td>Acciones</td>
        </tr>

        <?php
        // Mostrar los proyectos existentes
        $query = "SELECT * FROM proyectos";
        if ($stmt = $con->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id_proyecto, $nombre_proyecto, $descripcion);
            while ($stmt->fetch()) {
                echo "<tr>";
                echo "<td>".$id_proyecto. "</td>";
                echo "<td>".$nombre_proyecto. "</td>";
                echo "<td>".$descripcion. "</td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_editar' value='".$id_proyecto."'>
                            <button type='submit' name='editar'>Editar</button>
                        </form>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_borrar' value='".$id_proyecto."'>
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
