<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/db.php'; 

$alumno_encontrado = false;
$mensaje = '';
$mis_alumnos = [];
$stats = [
    'promedio' => 0,
    'aprobadas' => 0,
    'pendientes' => 0,
    'intensificaciones' => 0,
    'nombre_completo' => ''
];

$id_tutor = $_SESSION['usuario_id'] ?? null; 

if ($id_tutor) {
    try {
        // Procesa el envio de informacion del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['dni'])) {
            $dni = preg_replace('/[^0-9]/', '', $_POST['dni']);
            $stmt = $pdo->prepare("SELECT id, nombre, apellido FROM usuarios WHERE dni = :dni AND id_rol = 3 LIMIT 1");
            $stmt->execute([':dni' => $dni]);
            $alumno_nuevo = $stmt->fetch();

            if ($alumno_nuevo) {
                $id_alumno_nuevo = $alumno_nuevo['id'];
                $stmt_check = $pdo->prepare("SELECT 1 FROM tutores_alumnos WHERE id_tutor = :tutor AND id_alumno = :alumno LIMIT 1");
                $stmt_check->execute([':tutor' => $id_tutor, ':alumno' => $id_alumno_nuevo]);
                if (!$stmt_check->fetch()) {
                    $stmt_link = $pdo->prepare("INSERT INTO tutores_alumnos (id_tutor, id_alumno, parentesco) VALUES (:tutor, :alumno, 'Tutor')");
                    $stmt_link->execute([':tutor' => $id_tutor, ':alumno' => $id_alumno_nuevo]);
                    $mensaje = "¡Alumno vinculado correctamente!";
                } else {
                    $mensaje = "El alumno ya estaba vinculado.";
                }
                $_GET['alumno_id'] = $id_alumno_nuevo;
            } else {
                $mensaje = "No se encontró un alumno con ese DNI.";
            }
        }
        // Obtener los alumnos vinculados al tutor
        $stmt_mis_alumnos = $pdo->prepare("
            SELECT u.id, u.nombre, u.apellido 
            FROM usuarios u
            INNER JOIN tutores_alumnos ta ON u.id = ta.id_alumno
            WHERE ta.id_tutor = :tutor
        ");
        $stmt_mis_alumnos->execute([':tutor' => $id_tutor]);
        $mis_alumnos = $stmt_mis_alumnos->fetchAll();
        // Determina el alumno a mostrar(el primero en la lista o el seleccionado)
        $id_alumno_activo = $_GET['alumno_id'] ?? ($_POST['alumno_id'] ?? ($mis_alumnos[0]['id'] ?? null));
        if ($id_alumno_activo) {
            // Buscar datos del alumno activo
            $stmt_act = $pdo->prepare("
                SELECT u.id, u.nombre, u.apellido
                FROM usuarios u
                INNER JOIN tutores_alumnos ta
                    ON u.id = ta.id_alumno
                WHERE ta.id_tutor = :tutor
                AND ta.id_alumno = :alumno
                AND u.id_rol = 3
                LIMIT 1
            ");
            $stmt_act->execute([
                ':tutor' => $id_tutor,
                ':alumno' => $id_alumno_activo
            ]); 
            $alumno_actual = $stmt_act->fetch();
            if ($alumno_actual) {
                $stats['nombre_completo'] = $alumno_actual['nombre'] . ' ' . $alumno_actual['apellido'];
                // Promedio general y materias aprobadas
                $stmt_trayectoria = $pdo->prepare("
                    SELECT IFNULL(AVG(promedio_final), 0) as promedio, COUNT(*) as total_aprobadas 
                    FROM trayectorias WHERE id_alumno = :alumno AND id_estado_materia = 1
                ");
                $stmt_trayectoria->execute([':alumno' => $id_alumno_activo]);
                $res_trayectoria = $stmt_trayectoria->fetch();
                $stats['promedio'] = number_format($res_trayectoria['promedio'], 1);
                $stats['aprobadas'] = $res_trayectoria['total_aprobadas'];
                // Materias pendientes
                $stmt_pendientes = $pdo->prepare("SELECT COUNT(*) as total FROM v_materias_pendientes WHERE id_alumno = :alumno");
                $stmt_pendientes->execute([':alumno' => $id_alumno_activo]);
                $stats['pendientes'] = $stmt_pendientes->fetch()['total'];
                // Intensificaciones
                $stmt_intensif = $pdo->prepare("SELECT COUNT(*) as total FROM intensificaciones WHERE id_alumno = :alumno");
                $stmt_intensif->execute([':alumno' => $id_alumno_activo]);
                $stats['intensificaciones'] = $stmt_intensif->fetch()['total'];
                $alumno_encontrado = true;
            }
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}
?>

<main>
    <h1>Vista de inicio Padre/Tutor</h1>
    <div class="inicio_tutor">
        <div>
            <h2>Presentacion</h2>
        </div>
        <div>
            <p>(Insertar texto sobre este apartado, osea el de tutores)</p>
        </div>
    </div>

    <div class="inicio_tutor">
        <p>Por favor, ingrese los datos del alumno con el que está emparentado</p>
        <?php if (!empty($mensaje)): ?>
            <div style="padding: 12px; margin-bottom: 15px; border-radius: 6px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="campo-formulario">
                <label for="nombre">Nombre:</label>
                <input class="input_registro" type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del alumno" required>
            </div>

            <div class="campo-formulario">
                <label for="apellido">Apellido:</label>
                <input class="input_registro" type="text" id="apellido" name="apellido" placeholder="Ingrese el apellido del alumno" required>
            </div>

            <div class="campo-formulario">
                <label for="dni">DNI:</label>
                <input class="input_registro" type="text" id="dni" name="dni" placeholder="Ingrese el DNI del alumno" inputmode="numeric" required>
            </div>

            <div class="formulario-boton">
                <button type="submit">
                    Buscar alumno
                </button>
            </div>
        </form>
    </div>

    <div class="inicio_tutor" id="calificacionesAlumno" data-encontrado="<?= $alumno_encontrado ? 'true' : 'false' ?>" hidden>
        <?php if (!empty($mis_alumnos)): ?>
        <div style="margin-bottom: 15px;">
            <label for="selectAlumno"><strong>Seleccionar Alumno:</strong></label>
            <select id="selectAlumno" onchange="location = this.value;">
                <?php foreach ($mis_alumnos as $al): ?>
                    <option value="index.php?p=tutor_inicio&alumno_id=<?= $al['id'] ?>" 
                        <?= (isset($id_alumno_activo) && $id_alumno_activo == $al['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($al['nombre'] . ' ' . $al['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="calificaciones-header">
            <div>
                <p>Calificaciones generales de:</p>
                <h2 id="nombreAlumno"><?= htmlspecialchars($stats['nombre_completo']) ?></h2>
            </div>
            <button type="button" id="btnVerDetalle">
                Ver más en detalle
            </button>
        </div>
        <div class="calificaciones-resumen">
            <!-- Promedio -->
            <div class="calificacion-card blue">
                <div class="calificacion-icon">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none">
                        <path d="M10 2l2.5 5 5.5.8-4 3.9.94 5.5L10 14.5 5.06 17.2 6 11.7 2 7.8l5.5-.8L10 2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <span class="calificacion-label">Promedio general</span>
                    <span class="calificacion-value" id="promedioGeneral">
                        <?= $stats['promedio'] ?>
                    </span>
                    <span class="calificacion-sub">Sobre 10 puntos</span>
                </div>
            </div>
            
            <!-- Aprobadas -->
            <div class="calificacion-card green">
                <div class="calificacion-icon">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none">
                        <path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <span class="calificacion-label">Materias aprobadas</span>
                    <span class="calificacion-value" id="materiasAprobadas">
                        <?= $stats['aprobadas'] ?>
                    </span>
                    <span class="calificacion-sub" id="totalMaterias">de 12 materias totales</span>
                </div>
            </div>
            
            <!-- Pendientes -->
            <div class="calificacion-card red">
                <div class="calificacion-icon">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="10" y1="7" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="10" cy="13.5" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <span class="calificacion-label">Materias pendientes</span>
                    <span class="calificacion-value" id="materiasPendientes">
                        <?= $stats['pendientes'] ?>
                    </span>
                    <span class="calificacion-sub">Requieren atención</span>
                </div>
            </div>
            
            <!-- Intensificar -->
            <div class="calificacion-card cyan">
                <div class="calificacion-icon">
                    <svg viewBox="0 0 20 20" width="18" height="18" fill="none">
                        <path d="M10 4v12M6 8l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="4" y="12" width="12" height="5" rx="1" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div>
                    <span class="calificacion-label">Intensificación activa</span>
                    <span class="calificacion-value" id="intensificacionActiva">
                        <?= $stats['intensificaciones'] ?>
                    </span>
                    <span class="calificacion-sub" id="materiaIntensificacion">
                        Ver detalle 
                    </span>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="js/tutor/funcionest.js"></script>