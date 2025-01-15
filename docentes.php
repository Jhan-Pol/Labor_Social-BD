<?php
require_once("conex.php");

// Función para ingresar un nuevo docente
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $materia = $_POST['materia'];

    $query = "INSERT INTO docentes (nombre_docente, apellido1_docente, apellido2_docente, materia) VALUES (?, ?, ?, ?)";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssss", $nombre, $apellido1, $apellido2, $materia);
        $stmt->execute();
        $stmt->close();
        echo "<p>Docente ingresado exitosamente.</p>";
    }
}

// Función para editar un docente
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $materia = $_POST['materia'];

    $query = "UPDATE docentes SET nombre_docente = ?, apellido1_docente = ?, apellido2_docente = ?, materia = ? WHERE id_docente = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssssi", $nombre, $apellido1, $apellido2, $materia, $id);
        $stmt->execute();
        $stmt->close();
        echo "<p>Docente actualizado exitosamente.</p>";
    }
}

// Función para borrar un docente
if (isset($_POST['borrar'])) {
    $id_borrar = $_POST['id_borrar'];
    $query = "DELETE FROM docentes WHERE id_docente = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $id_borrar);
        $stmt->execute();
        $stmt->close();
        echo "<p>Docente borrado exitosamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Docentes</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
    <header>
        <span id="presentacion">BASE DE DATOS - LABOR SOCIAL</span>
    </header>

    <h2 id="subtitulo">Administrar Docentes</h2>

    <!-- Formulario de Ingreso -->
    <h3>Ingreso de Docente</h3>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required>
        
        <label for="apellido1">Apellido 1:</label>
        <input type="text" name="apellido1" id="apellido1" required>
        
        <label for="apellido2">Apellido 2:</label>
        <input type="text" name="apellido2" id="apellido2" required>
        
        <label for="materia">Materia:</label>
        <input type="text" name="materia" id="materia" required>
        
        <button type="submit" name="guardar">Guardar</button>
    </form>
    <!-- Formulario de Edición -->
    <?php
    if (isset($_POST['editar'])) {
        $id_editar = $_POST['id_editar'];
        $query = "SELECT * FROM docentes WHERE id_docente = ?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param("i", $id_editar);
            $stmt->execute();
            $stmt->bind_result($id_docente, $nombre_docente, $apellido1_docente, $apellido2_docente, $materia);
            $stmt->fetch();
            ?>
            <h3>Editar Docente</h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $id_docente; ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" value="<?php echo $nombre_docente; ?>" required>
                
                <label for="apellido1">Apellido 1:</label>
                <input type="text" name="apellido1" value="<?php echo $apellido1_docente; ?>" required>
                
                <label for="apellido2">Apellido 2:</label>
                <input type="text" name="apellido2" value="<?php echo $apellido2_docente; ?>" required>
                
                <label for="materia">Materia:</label>
                <input type="text" name="materia" value="<?php echo $materia; ?>" required>
                
                <button type="submit" name="actualizar">Actualizar</button>
            </form>
            <?php
            $stmt->close();
        }
    }
    ?>
    <!-- Tabla de docentes -->
    <table border="1">
        <tr>
            <td colspan="6">Lista de Docentes</td>
        </tr>
        <tr>
            <td>ID Docente</td>
            <td>Nombre</td>
            <td>Apellido 1</td>
            <td>Apellido 2</td>
            <td>Materia</td>
            <td>Acciones</td>
        </tr>

        <?php
        // Mostrar los docentes existentes
        $query = "SELECT * FROM docentes";
        if ($stmt = $con->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id_docente, $nombre_docente, $apellido1_docente, $apellido2_docente, $materia);
            while ($stmt->fetch()) {
                echo "<tr>";
                echo "<td>".$id_docente. "</td>";
                echo "<td>".$nombre_docente. "</td>";
                echo "<td>".$apellido1_docente. "</td>";
                echo "<td>".$apellido2_docente. "</td>";
                echo "<td>".$materia. "</td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_editar' value='".$id_docente."'>
                            <button type='submit' name='editar'>Editar</button>
                        </form>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_borrar' value='".$id_docente."'>
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
