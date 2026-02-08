<?php

declare(strict_types=1);

session_start();

$user = $_SESSION['user'] ?? null;
if (!is_array($user) || ($user['tipo_usuario'] ?? null) !== 'admin') {
    header('Location: index.php?error=' . urlencode('Debes iniciar sesión como admin.'));
    exit;
}

require __DIR__ . '/db.php';

try {
    $stmt = $pdo->query('SELECT id, tipo_usuario, nombre, apellido, pais, fecha_nacimiento, correo, creado_en FROM usuarios ORDER BY id DESC');
    $usuarios = $stmt->fetchAll();
} catch (Throwable $e) {
    $usuarios = [];
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f1f1f1; }
        a.button { display: inline-block; padding: 10px 14px; border: 1px solid #333; text-decoration: none; color: #000; background: #fff; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Panel de control (Admin)</h1>
        <a class="button" href="logout.php">Cerrar sesión</a>
    </div>

    <h2>Usuarios</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>País</th>
                <th>Fecha nacimiento</th>
                <th>Correo</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$u['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['tipo_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['apellido'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['pais'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['fecha_nacimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$u['creado_en'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
