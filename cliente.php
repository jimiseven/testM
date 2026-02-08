<?php

declare(strict_types=1);

session_start();

$user = $_SESSION['user'] ?? null;
if (!is_array($user) || ($user['tipo_usuario'] ?? null) !== 'cliente') {
    header('Location: index.php?error=' . urlencode('Debes iniciar sesión como cliente.'));
    exit;
}

$nombre = (string)($user['nombre'] ?? '');
$apellido = (string)($user['apellido'] ?? '');

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 16px; }
        .card { border: 1px solid #ddd; padding: 16px; background: #f7f7f7; }
        a.button { display: inline-block; margin-top: 12px; padding: 10px 14px; border: 1px solid #333; text-decoration: none; color: #000; background: #fff; }
    </style>
</head>
<body>
    <h1>Bienvenido</h1>
    <div class="card">
        <div>Hola, <?php echo htmlspecialchars(trim($nombre . ' ' . $apellido), ENT_QUOTES, 'UTF-8'); ?>.</div>
        <a class="button" href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
