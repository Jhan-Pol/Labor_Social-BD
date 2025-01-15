<?php
require_once("conex.php");

// Función para manejar el formulario de creación o actualización
if (isset($_POST['submit'])) {
    $fecha_actividad = $_POST['fecha_actividad'];
    $estudiante = $_POST['estudiante'];
    $proyecto = $_POST['proyecto'];
    $cantidad_horas = $_POST['cantidad_horas'];
    $trabajo_realizado = $_POST['trabajo_realizado'];
    $docente_responsable = $_POST['docente_responsable'];

    if (isset($_POST['id_informe']) && $_POST['id_informe'] != '') {
        // Actualizar el informe existente
        $id_informe = $_POST['id_informe'];

        // Obtener las horas acumuladas actuales del informe
        $query = "SELECT horas_acumuladas FROM informe WHERE id_informe = ?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param('i', $id_informe);
            $stmt->execute();
            $stmt->bind_result($horas_acumuladas_actuales);
            $stmt->fetch();
            $stmt->close();
        }

        // Calcular las nuevas horas acumuladas
        $nuevas_horas_acumuladas = $horas_acumuladas_actuales + $cantidad_horas;

        // Actualizar el informe
        $query = "UPDATE informe SET fecha_actividad=?, estudiante=?, proyecto=?, cantidad_horas=?, horas_acumuladas=?, trabajo_realizado=?, docente_responsable=? WHERE id_informe=?";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param('siisissi', $fecha_actividad, $estudiante, $proyecto, $cantidad_horas, $nuevas_horas_acumuladas, $trabajo_realizado, $docente_responsable, $id_informe);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Insertar un nuevo informe
        $horas_acumuladas = $cantidad_horas;  // Para el primer informe, las horas acumuladas son iguales a la cantidad de horas

        $query = "INSERT INTO informe (fecha_actividad, estudiante, proyecto, cantidad_horas, horas_acumuladas, trabajo_realizado, docente_responsable) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param('siisisi', $fecha_actividad, $estudiante, $proyecto, $cantidad_horas, $horas_acumuladas, $trabajo_realizado, $docente_responsable);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Función para eliminar un informe
if (isset($_GET['delete'])) {
    $id_informe = $_GET['delete'];
    $query = "DELETE FROM informe WHERE id_informe=?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('i', $id_informe);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener datos del informe para la edición (si se seleccionó un informe)
if (isset($_GET['edit'])) {
    $id_informe = $_GET['edit'];
    $query = "SELECT id_informe, fecha_actividad, estudiante, proyecto, cantidad_horas, horas_acumuladas, trabajo_realizado, docente_responsable FROM informe WHERE id_informe = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('i', $id_informe);
        $stmt->execute();
        $stmt->bind_result($id_informe, $fecha_actividad, $estudiante, $proyecto, $cantidad_horas, $horas_acumuladas, $trabajo_realizado, $docente_responsable);
        $stmt->fetch();
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes de Estudiantes</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
    <h2>Informes de Estudiantes</h2>

    <!-- Formulario de Ingreso o Edición -->
    <form method="POST">
        <input type="hidden" name="id_informe" id="id_informe" value="<?= isset($id_informe) ? $id_informe : '' ?>">
        <label for="fecha_actividad">Fecha de Actividad</label>
        <input type="date" name="fecha_actividad" id="fecha_actividad" value="<?= isset($fecha_actividad) ? $fecha_actividad : '' ?>" required>

        <label for="estudiante">Estudiante</label>
        <select name="estudiante" id="estudiante" required>
            <?php
            $query = "SELECT id_estudiante, nombre_estudiante FROM estudiantes";
            $result = $con->query($query);
            while ($row = $result->fetch_assoc()) {
                $selected = (isset($estudiante) && $estudiante == $row['id_estudiante']) ? 'selected' : '';
                echo "<option value='" . $row['id_estudiante'] . "' $selected>" . $row['nombre_estudiante'] . "</option>";
            }
            ?>
        </select>

        <label for="proyecto">Proyecto</label>
        <select name="proyecto" id="proyecto" required>
            <?php
            $query = "SELECT id_proyecto, nombre_proyecto FROM proyectos";
            $result = $con->query($query);
            while ($row = $result->fetch_assoc()) {
                $selected = (isset($proyecto) && $proyecto == $row['id_proyecto']) ? 'selected' : '';
                echo "<option value='" . $row['id_proyecto'] . "' $selected>" . $row['nombre_proyecto'] . "</option>";
            }
            ?>
        </select>

        <label for="cantidad_horas">Cantidad de Horas</label>
        <input type="number" name="cantidad_horas" id="cantidad_horas" value="<?= isset($cantidad_horas) ? $cantidad_horas : '' ?>" required>

        <label for="trabajo_realizado">Trabajo Realizado</label>
        <textarea name="trabajo_realizado" id="trabajo_realizado" required><?= isset($trabajo_realizado) ? $trabajo_realizado : '' ?></textarea>

        <label for="docente_responsable">Docente Responsable</label>
        <select name="docente_responsable" id="docente_responsable" required>
            <?php
            $query = "SELECT id_docente, nombre_docente FROM docentes";
            $result = $con->query($query);
            while ($row = $result->fetch_assoc()) {
                $selected = (isset($docente_responsable) && $docente_responsable == $row['id_docente']) ? 'selected' : '';
                echo "<option value='" . $row['id_docente'] . "' $selected>" . $row['nombre_docente'] . "</option>";
            }
            ?>
        </select>

        <button type="submit" name="submit">Guardar Informe</button>
    </form>

    <!-- Tabla de Informes Existentes -->
    <table border="1">
        <tr>
            <th>Fecha de Actividad</th>
            <th>Estudiante</th>
            <th>Proyecto</th>
            <th>Cantidad de Horas</th>
            <th>Trabajo Realizado</th>
            <th>Docente Responsable</th>
            <th>Horas Acumuladas</th>
            <th>Acciones</th>
        </tr>
        <?php
        $query = "SELECT i.id_informe, i.fecha_actividad, e.nombre_estudiante, p.nombre_proyecto, i.cantidad_horas, i.trabajo_realizado, d.nombre_docente, i.horas_acumuladas FROM informe i
                  JOIN estudiantes e ON i.estudiante = e.id_estudiante
                  JOIN proyectos p ON i.proyecto = p.id_proyecto
                  JOIN docentes d ON i.docente_responsable = d.id_docente";
        $result = $con->query($query);
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['fecha_actividad'] . "</td>
                    <td>" . $row['nombre_estudiante'] . "</td>
                    <td>" . $row['nombre_proyecto'] . "</td>
                    <td>" . $row['cantidad_horas'] . "</td>
                    <td>" . $row['trabajo_realizado'] . "</td>
                    <td>" . $row['nombre_docente'] . "</td>
                    <td>" . $row['horas_acumuladas'] . "</td>
                    <td>
                        <a href='?edit=" . $row['id_informe'] . "'>Editar</a>
                        <a href='?delete=" . $row['id_informe'] . "'>Eliminar</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
    <button><a href="http://localhost/Proyecto-Final-BD">VOLVER A LA PANTALLA PRINCIPAL</a></button>
</body>
</html>
