<?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  require_once 'php/db.php';
  $id_tutor = $_SESSION['usuario_id'] ?? null;
  $alumno_actual = null;

  // if ($id_tutor) {
  //   $id_alumno = $_GET['alumno_id'] ?? null;
  //   if ($id_alumno) {
  //     $stmt = $pdo->prepare("
  //         SELECT
  //             u.id,
  //             u.nombre,
  //             u.apellido,
  //             c.nombre AS curso,
  //             c.anio_escolar,
  //             c.division,
  //             cl.anio AS ciclo_lectivo,
  //             cl.descripcion AS ciclo_descripcion
  //         FROM usuarios u
  //         INNER JOIN tutores_alumnos ta
  //             ON ta.id_alumno = u.id
  //         INNER JOIN alumnos a
  //             ON a.id_alumno = u.id
  //         INNER JOIN cursos c
  //             ON c.id = a.id_curso
  //         INNER JOIN ciclos_lectivos cl
  //             ON cl.id = c.id_ciclo
  //         WHERE ta.id_tutor = :tutor
  //           AND ta.id_alumno = :alumno
  //         LIMIT 1");
  //     $stmt->execute([
  //         ':tutor' => $id_tutor,
  //         ':alumno' => $id_alumno
  //     ]);
  //     $alumno_actual = $stmt->fetch();
  //   }
  // }
?>

<main class="main">
  <header class="topbar">
    <div class="topbar-left">
      <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <div class="page-heading">
        <h1 class="page-title">
          Situación Académica
        </h1>

        <div class="page-meta">
          <span>
            Ciclo Lectivo Activo:
            <?php if ($alumno_actual): ?>
                <?= htmlspecialchars(
                    $alumno_actual['ciclo_lectivo']
                ) ?>
            <?php else: ?>
               -
            <?php endif; ?>
        </span>
          <span class="meta-dot">·</span>
          <span class="meta-text">
            Seguimiento académico
          </span>
        </div>
      </div>
    </div>

    <div class="topbar-right">
      <button class="btn-export" id="btnExport">
        <svg viewBox="0 0 16 16" width="13" height="13" fill="none">
          <path
            d="M8 2v8M5 7l3 3 3-3"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"/>

          <path
            d="M2.5 12h11"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"/>
        </svg>

        <span>
          Descargar Analítico
        </span>
      </button>
    </div>
  </header>
  <div class="content">
    <!-- Seleccion alumno -->
    
    <section class="student-selector">
        <div class="student-selector-header">
            <div>
                <h2 class="section-title-small">
                    Seleccionar alumno
                </h2>
                <p>
                    Seleccione el hijo/a cuya situación académica desea consultar.
                </p>
            </div>
        </div>
        <?php if (!empty($mis_alumnos)): ?>
            <div class="student-selector-body">
                <label for="alumnoSelect">
                    Alumno/a
                </label>
                <select id="alumnoSelect">
                    <?php foreach ($mis_alumnos as $alumno): ?>
                        <option
                            value="<?= (int)$alumno['id'] ?>"
                            <?= (
                                isset($id_alumno)
                                && $id_alumno == $alumno['id']
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $alumno['nombre'] . ' ' . $alumno['apellido']
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="btnConsultarAlumno">
                  Consultar situación académica
                </button>
            </div>
        <?php else: ?>
            <div class="student-selector-empty">
                <p>
                    No tenés alumnos asociados a tu cuenta.
                </p>
            </div>
        <?php endif; ?>
    </section>
    <!-- Informacion del alumno -->
    <section class="student-info">
        <div>
          <span class="student-label">
              Alumno/a seleccionado
          </span>
          <h2 id="studentName">
              <?php if ($alumno_actual): ?>
                  <?= htmlspecialchars(
                      $alumno_actual['nombre'] . ' ' .
                      $alumno_actual['apellido']
                  ) ?>
              <?php else: ?>
                  Seleccione un alumno
              <?php endif; ?>
          </h2>
      </div>

      <div class="student-data">
        <span>
          Curso
        </span>
        <strong id="studentCourse">
          <?php if ($alumno_actual): ?>
              <?= htmlspecialchars(
                  $alumno_actual['curso']
              ) ?>
              <?php if (!empty($alumno_actual['division'])): ?>
                  — División <?= htmlspecialchars(
                      $alumno_actual['division']
                  ) ?>
              <?php endif; ?>
          <?php else: ?>
            -
          <?php endif; ?>
      </strong>
      </div>

      <div class="student-data">
        <span>
          Orientación
        </span>
        <strong id="studentOrientation">
          Programación
        </strong>
      </div>
    </section>

    <section
      class="stat-cards"
      aria-label="Resumen de situación académica">
      <!-- Promedios -->
      <div class="stat-card" data-accent="blue">
        <div class="stat-icon blue">
          <svg
            viewBox="0 0 20 20"
            width="17"
            height="17"
            fill="none">
            <path
              d="M10 2l2.2 4.5 5 .73-3.6 3.52.85 4.97L10 13.25l-4.45 2.47.85-4.97L2.8 7.23l5-.73L10 2z"
              stroke="currentColor"
              stroke-width="1.5"
              stroke-linejoin="round"/>
          </svg>
        </div>

        <div class="stat-body">
          <span class="stat-label">
            Promedio Acumulado
          </span>
          <span
            class="stat-value"
            id="promedioAcumulado">
            7.6
          </span>
          <span class="stat-hint">
            Historial completo
          </span>
        </div>
        <div class="stat-bar blue"></div>
      </div>
      <!-- Aprobadas -->
      <div class="stat-card" data-accent="green">
        <div class="stat-icon green">
          <svg
            viewBox="0 0 20 20"
            width="17"
            height="17"
            fill="none">
            <path
              d="M4 10.5l4 4 8-8"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"/>
          </svg>
        </div>

        <div class="stat-body">
          <span class="stat-label">
            Aprobadas / Totales
          </span>
          <span
            class="stat-value"
            id="aprobadasTotales">
            8 / 12
          </span>
          <span>
            Ciclo Lectivo Activo:
            <?php if ($alumno_actual): ?>
                <?= htmlspecialchars(
                    $alumno_actual['ciclo_lectivo']
                ) ?>
            <?php else: ?>
              -
            <?php endif; ?>
        </span>
        </div>

        <div class="stat-bar green"></div>
      </div>
      <!-- INTENSIFICACIÓN -->
      <div class="stat-card" data-accent="amber">
        <div class="stat-icon amber">
          <svg
            viewBox="0 0 20 20"
            width="17"
            height="17"
            fill="none">
            <rect
              x="3"
              y="4"
              width="14"
              height="12"
              rx="2"
              stroke="currentColor"
              stroke-width="1.5"/>
            <path
              d="M3 8h14"
              stroke="currentColor"
              stroke-width="1.3"/>
            <path
              d="M7 2v3M13 2v3"
              stroke="currentColor"
              stroke-width="1.5"
              stroke-linecap="round"/>
          </svg>
        </div>

        <div class="stat-body">
          <span class="stat-label">
            Instancias Activas
          </span>
          <span
            class="stat-value"
            id="instanciasActivas">
            1
          </span>
          <span class="stat-hint">
            Intensificación
          </span>
        </div>

        <div class="stat-bar amber"></div>
      </div>
      <!-- PENDIENTES -->
      <div class="stat-card" data-accent="red">
        <div class="stat-icon red">
          <svg
            viewBox="0 0 20 20"
            width="17"
            height="17"
            fill="none">
            <circle
              cx="10"
              cy="10"
              r="7.5"
              stroke="currentColor"
              stroke-width="1.5"/>
            <line
              x1="10"
              y1="7"
              x2="10"
              y2="11"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"/>
            <circle
              cx="10"
              cy="13.5"
              r="1.1"
              fill="currentColor"/>
          </svg>
        </div>

        <div class="stat-body">
          <span class="stat-label">
            Materias Pendientes
          </span>
          <span
            class="stat-value"
            id="materiasDeuda">
            3
          </span>
          <span class="stat-hint">
            Requieren atención
          </span>
        </div>
        <div class="stat-bar red"></div>
      </div>
    </section>
    <!-- Trayectoria academica -->
    <div class="cycle-tabs-container">
      <h2 class="section-title-small">
        Seleccionar Trayectoria Anual
      </h2>
      <div
        class="cycle-tabs"
        id="cycleTabs">
      </div>
    </div>

    <div class="main-grid">
      <section class="table-card">
        <div class="table-card-header">
          <div>
            <h2
              class="table-card-title"
              id="selectedCycleTitle">
              Detalle de Calificaciones
            </h2>

            <span
              class="table-card-count"
              id="selectedCycleSubtitle">
              Seleccione una trayectoria
            </span>
          </div>

          <span
            class="badge-status-cycle"
            id="cycleStatusBadge">
            Regular
          </span>
        </div>


        <div class="table-wrap">
          <table
            class="eval-table"
            id="trajectoryTable">
            <thead>
              <tr>
                <th>
                  Asignatura / Materia
                </th>
                <th>
                  Estado RITE
                </th>
                <th>
                  Calificación Final
                </th>
                <th>
                  Condición Académica
                </th>
                <th>
                  Observaciones / Docente
                </th>
              </tr>
            </thead>

            <tbody id="trajectoryBody">
              <tr>
                <td
                  colspan="5"
                  class="td-loading">
                  Seleccione un alumno para consultar
                  su trayectoria académica.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <aside class="right-col">
        <!-- Recuperacion -->
        <div class="side-card">
          <div class="side-card-header">
            <h3 class="side-card-title">
              Instancias de Recuperación
            </h3>
          </div>

          <div
            id="recoveryList"
            class="recovery-list">
          </div>
        </div>
        <!-- Avisos-Aclaraciones -->
        <div class="side-card recommendation-card">
          <div class="side-card-header">
            <h3 class="side-card-title">
              Información Académica
            </h3>
          </div>

          <div class="recommendation-content">
            <p>
              Aquí podrá consultar el estado académico
              de su hijo/a y las instancias que requieran
              seguimiento.
            </p>
            <ul>
              <li>
                Consulte las materias que se encuentran
                <strong>en proceso</strong>.
              </li>
              <li>
                Revise las materias que requieren
                <strong>intensificación</strong>.
              </li>
              <li>
                Consulte las materias
                <strong>pendientes</strong>.
              </li>
            </ul>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<script src="js/tutor/tutor_calificacion.js"></script>