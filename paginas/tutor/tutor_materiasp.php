<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/db.php';

$id_tutor = $_SESSION['usuario_id'] ?? null;

$mis_alumnos = [];
$alumno_actual = null;
$materias_pendientes = [];

$total_pendientes = 0;
$total_intensificaciones = 0;
$total_recursadas = 0;
$total_proximas = 0;

if ($id_tutor) {

    try {

        /*
        ==========================================
        OBTENER ALUMNOS ASOCIADOS AL TUTOR
        ==========================================
        */

        $stmt_alumnos = $pdo->prepare("
            SELECT
                u.id,
                u.nombre,
                u.apellido
            FROM usuarios u
            INNER JOIN tutores_alumnos ta
                ON u.id = ta.id_alumno
            WHERE ta.id_tutor = :tutor
              AND u.id_rol = 3
              AND u.activo = 1
            ORDER BY u.apellido, u.nombre
        ");

        $stmt_alumnos->execute([
            ':tutor' => $id_tutor
        ]);

        $mis_alumnos = $stmt_alumnos->fetchAll();


        /*
        ==========================================
        DETERMINAR ALUMNO SELECCIONADO
        ==========================================
        */

        $id_alumno_activo = $_GET['alumno_id'] ?? null;

        if (!$id_alumno_activo && !empty($mis_alumnos)) {
            $id_alumno_activo = $mis_alumnos[0]['id'];
        }


        /*
        ==========================================
        VALIDAR QUE EL ALUMNO PERTENEZCA AL TUTOR
        ==========================================
        */

        if ($id_alumno_activo) {

            $stmt_alumno = $pdo->prepare("
                SELECT
                    u.id,
                    u.nombre,
                    u.apellido
                FROM usuarios u
                INNER JOIN tutores_alumnos ta
                    ON u.id = ta.id_alumno
                WHERE ta.id_tutor = :tutor
                  AND ta.id_alumno = :alumno
                  AND u.id_rol = 3
                  AND u.activo = 1
                LIMIT 1
            ");

            $stmt_alumno->execute([
                ':tutor' => $id_tutor,
                ':alumno' => $id_alumno_activo
            ]);

            $alumno_actual = $stmt_alumno->fetch();


            /*
            ==========================================
            SI EL ALUMNO ES VÁLIDO
            ==========================================
            */

            if ($alumno_actual) {

                /*
                ==========================================
                MATERIAS PENDIENTES
                ==========================================
                */

                $stmt_pendientes = $pdo->prepare("
                    SELECT
                        v.id_alumno,
                        v.materia,
                        v.ciclo,
                        v.estado,
                        v.promedio_final,
                        v.fecha_actualizacion
                    FROM v_materias_pendientes v
                    INNER JOIN tutores_alumnos ta
                        ON v.id_alumno = ta.id_alumno
                    WHERE ta.id_tutor = :tutor
                      AND v.id_alumno = :alumno
                    ORDER BY v.materia
                ");

                $stmt_pendientes->execute([
                    ':tutor' => $id_tutor,
                    ':alumno' => $id_alumno_activo
                ]);

                $materias_pendientes = $stmt_pendientes->fetchAll();

                $total_pendientes = count($materias_pendientes);


                /*
                ==========================================
                INTENSIFICACIONES
                ==========================================
                */

                $stmt_intensificaciones = $pdo->prepare("
                    SELECT COUNT(*) AS total
                    FROM intensificaciones i
                    INNER JOIN tutores_alumnos ta
                        ON i.id_alumno = ta.id_alumno
                    WHERE ta.id_tutor = :tutor
                      AND i.id_alumno = :alumno
                ");

                $stmt_intensificaciones->execute([
                    ':tutor' => $id_tutor,
                    ':alumno' => $id_alumno_activo
                ]);

                $total_intensificaciones =
                    (int) $stmt_intensificaciones->fetch()['total'];
            }
        }

    } catch (PDOException $e) {

        $error = "Error al consultar la información académica.";
    }
}

?>

<main class="main-content">

    <!-- HEADER -->

    <div class="pendientes-header">

        <div>

            <span class="section-label">
                Seguimiento académico
            </span>

            <h1>
                Materias pendientes
            </h1>

            <p>
                Consultá las materias pendientes del alumno seleccionado.
            </p>

        </div>

        <div class="pendientes-actions">

            <button
                type="button"
                class="btn-pendiente outline"
            >
                Filtrar
            </button>

            <button
                type="button"
                class="btn-pendiente primary"
            >
                Exportar PDF
            </button>

        </div>

    </div>


    <!-- SELECTOR DE ALUMNO -->

    <?php if (!empty($mis_alumnos)): ?>

        <section class="alumno-selector-card">

            <div>

                <span class="selector-label">
                    Alumno asociado
                </span>

                <p>
                    Seleccioná el estudiante que querés consultar.
                </p>

            </div>

            <select
                id="selectAlumno"
                onchange="location.href=this.value;"
            >

                <?php foreach ($mis_alumnos as $alumno): ?>

                    <option
                        value="index.php?p=tutor_materiasp&alumno_id=<?= (int)$alumno['id'] ?>"
                        <?= (
                            isset($id_alumno_activo)
                            && $id_alumno_activo == $alumno['id']
                        ) ? 'selected' : '' ?>
                    >

                        <?= htmlspecialchars(
                            $alumno['nombre'] . ' ' . $alumno['apellido']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </section>

    <?php else: ?>

        <section class="sin-alumnos-card">

            <h2>
                No hay alumnos asociados
            </h2>

            <p>
                Actualmente no tenés estudiantes vinculados a tu cuenta.
            </p>

        </section>

    <?php endif; ?>


    <?php if ($alumno_actual): ?>


        <!-- ALUMNO ACTUAL -->

        <section class="alumno-seleccionado">

            <div>

                <span>
                    Alumno seleccionado
                </span>

                <h2>
                    <?= htmlspecialchars(
                        $alumno_actual['nombre'] . ' ' . $alumno_actual['apellido']
                    ) ?>
                </h2>

            </div>

        </section>


        <!-- RESUMEN -->

        <section class="pendientes-summary">

            <div class="pendiente-card red">

                <div class="pendiente-number">
                    <?= $total_pendientes ?>
                </div>

                <div>

                    <span class="pendiente-label">
                        Materias pendientes
                    </span>

                    <span class="pendiente-sub">
                        Requieren regularización
                    </span>

                </div>

            </div>


            <div class="pendiente-card blue">

                <div class="pendiente-number">
                    <?= $total_intensificaciones ?>
                </div>

                <div>

                    <span class="pendiente-label">
                        Intensificaciones
                    </span>

                    <span class="pendiente-sub">
                        Actualmente activas
                    </span>

                </div>

            </div>


            <div class="pendiente-card yellow">

                <div class="pendiente-number">
                    <?= $total_recursadas ?>
                </div>

                <div>

                    <span class="pendiente-label">
                        Recursadas
                    </span>

                    <span class="pendiente-sub">
                        En seguimiento
                    </span>

                </div>

            </div>


            <div class="pendiente-card cyan">

                <div class="pendiente-number">
                    <?= $total_proximas ?>
                </div>

                <div>

                    <span class="pendiente-label">
                        Próximas fechas
                    </span>

                    <span class="pendiente-sub">
                        Eventos próximos
                    </span>

                </div>

            </div>

        </section>


        <!-- BUSCADOR -->

        <section class="pendientes-search">

            <div class="search-input-wrapper">

                <span class="search-label">
                    Buscar
                </span>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Buscar materia..."
                >

            </div>

        </section>


        <!-- CONTENIDO -->

        <div class="pendientes-content">


            <!-- TABLA -->

            <section class="pendientes-table-card">

                <div class="pendientes-table-header">

                    <div>

                        <h2>
                            Detalle de materias pendientes
                        </h2>

                        <span>
                            <?= htmlspecialchars(
                                $alumno_actual['nombre'] . ' ' .
                                $alumno_actual['apellido']
                            ) ?>
                        </span>

                    </div>

                </div>


                <div class="pendientes-table-wrapper">

                    <table class="pendientes-table">

                        <thead>

                            <tr>

                                <th>
                                    Materia
                                </th>

                                <th>
                                    Ciclo
                                </th>

                                <th>
                                    Estado
                                </th>

                                <th>
                                    Promedio
                                </th>

                                <th>
                                    Última actualización
                                </th>

                                <th>
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody id="tableBody">

                            <?php if (!empty($materias_pendientes)): ?>

                                <?php foreach ($materias_pendientes as $materia): ?>

                                    <tr>

                                        <td>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $materia['materia']
                                                ) ?>
                                            </strong>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $materia['ciclo']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php

                                            $clase_estado = 'pending';

                                            if ($materia['estado'] === 'No aprobada') {
                                                $clase_estado = 'pending';
                                            }

                                            if ($materia['estado'] === 'En proceso') {
                                                $clase_estado = 'intensification';
                                            }

                                            ?>

                                            <span
                                                class="pendiente-status <?= $clase_estado ?>"
                                            >
                                                <?= htmlspecialchars(
                                                    $materia['estado']
                                                ) ?>
                                            </span>

                                        </td>


                                        <td>

                                            <?php if (
                                                $materia['promedio_final'] !== null
                                            ): ?>

                                                <?= htmlspecialchars(
                                                    $materia['promedio_final']
                                                ) ?>

                                            <?php else: ?>

                                                <span>
                                                    Sin promedio
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?= $materia['fecha_actualizacion']
                                                ? date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $materia['fecha_actualizacion']
                                                    )
                                                )
                                                : 'Sin información'
                                            ?>

                                        </td>


                                        <td class="pendiente-actions-cell">

                                            <button
                                                type="button"
                                                class="btn-small outline"
                                            >
                                                Ver detalle
                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="tabla-vacia"
                                    >
                                        Este alumno no tiene materias pendientes.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <!-- FOOTER -->

                <div class="pendientes-table-footer">

                    <span>
                        Mostrando las materias pendientes del alumno seleccionado.
                    </span>

                </div>

            </section>


            <!-- PANEL DERECHO -->

            <aside class="pendientes-sidebar">


                <div class="pendiente-panel alert">

                    <h3>
                        Alertas académicas
                    </h3>

                    <ul>

                        <?php if ($total_pendientes > 0): ?>

                            <li>
                                El alumno tiene
                                <?= $total_pendientes ?>
                                materia(s) pendiente(s).
                            </li>

                        <?php else: ?>

                            <li>
                                No hay materias pendientes.
                            </li>

                        <?php endif; ?>

                    </ul>

                </div>


                <div class="pendiente-panel">

                    <h3>
                        Seguimiento
                    </h3>

                    <p>
                        Revisá periódicamente el estado académico
                        del alumno seleccionado.
                    </p>

                </div>


                <div class="pendiente-panel">

                    <h3>
                        Intensificaciones
                    </h3>

                    <p>

                        Actualmente tiene
                        <strong>
                            <?= $total_intensificaciones ?>
                        </strong>
                        intensificación(es) registrada(s).

                    </p>

                </div>


                <div class="pendiente-panel">

                    <h3>
                        Recomendaciones
                    </h3>

                    <ul>

                        <li>
                            Revisá las materias pendientes.
                        </li>

                        <li>
                            Consultá las actualizaciones académicas.
                        </li>

                        <li>
                            Mantené comunicación con los docentes.
                        </li>

                    </ul>

                </div>


            </aside>

        </div>


    <?php endif; ?>

</main>