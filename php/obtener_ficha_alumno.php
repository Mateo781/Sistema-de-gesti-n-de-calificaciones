<?php
require_once "db.php";

if (!isset($_GET['id'])) {
    echo "ID de alumno no proporcionado.";
    exit;
}

$id_alumno = $_GET['id'];

// Cargar datos del alumno
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_alumno]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alumno) {
    echo "Alumno no encontrado.";
    exit;
}
?>

<div style="font-family: 'Segoe UI', Tahoma, sans-serif; color: #333; animation: fadeIn 0.3s; width: 100%;">
    
    <!-- Encabezado -->
    <div style="background: #f8f9fa; padding: 20px; border-bottom: 3px solid #1a2d5a; margin-bottom: 20px; border-radius: 6px 6px 0 0;">
        <h3 style="margin: 0; color: #1a2d5a; font-size: 1.6em;"><?= htmlspecialchars($alumno['apellido'] . ', ' . $alumno['nombre']) ?></h3>
        <p style="margin: 5px 0 0 0; color: #666; font-size: 1.1em;">
            <strong>DNI:</strong> <?= htmlspecialchars($alumno['dni']) ?>
        </p>
    </div>

    <!-- Alta de observación -->
    <div style="padding: 0 20px 20px 20px;">
        <form action="php/guardar_observacion.php" method="POST">
            <input type="hidden" name="id_alumno" value="<?= $id_alumno ?>">
            
            <label style="display: block; font-weight: bold; margin-bottom: 10px; color: #1a2d5a; font-size: 1.1em;">
                Registrar nuevo aviso socioeducativo:
            </label>
            
            <textarea name="observacion" 
                      style="width: 100%; height: 200px; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1em; resize: vertical; transition: border-color 0.3s;" 
                      placeholder="Redactar observación o seguimiento pedagógico aquí..."></textarea>
            
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" 
                        style="background: #1a2d5a; color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1em; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: background 0.3s;">
                    Guardar Observación
                </button>
            </div>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
        
        <h4 style="color: #1a2d5a;">Historial de Observaciones</h4>
        
        <?php
        // Cargar historial del alumno
        $stmt_obs = $pdo->prepare("SELECT * FROM observaciones WHERE id_alumno = ? ORDER BY fecha DESC");
        $stmt_obs->execute([$id_alumno]);
        $observaciones = $stmt_obs->fetchAll(PDO::FETCH_ASSOC);

        if (empty($observaciones)): ?>
            <p style="color: #888;">No hay observaciones registradas aún.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($observaciones as $obs): ?>
                    <li style="background: #fff; border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 6px; display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="margin: 0 0 5px 0; font-size: 0.9em; color: #555;"><strong>Fecha:</strong> <?= $obs['fecha'] ?></p>
                            <p style="margin: 0;"><?= htmlspecialchars($obs['observacion']) ?></p>
                        </div>
                        
                        <a href="php/eliminar_observacion.php?id=<?= $obs['id'] ?>&id_alumno=<?= $id_alumno ?>" 
                           onclick="return confirm('¿Estás seguro de eliminar este aviso?');"
                           style="color: #d9534f; text-decoration: none; font-size: 0.85em; font-weight: bold; margin-left: 10px;">
                            Eliminar
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>