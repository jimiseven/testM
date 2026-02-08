<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/db.php';

function redirect_with_error(string $msg): void
{
    header('Location: index.php?error=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$correo = isset($_POST['correo']) ? trim((string)$_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? (string)$_POST['contrasena'] : '';

if ($correo === '' || mb_strlen($correo) > 255 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Correo inválido.');
}

if ($contrasena === '' || strlen($contrasena) > 72) {
    redirect_with_error('Contraseña inválida.');
}

try {
    $stmt = $pdo->prepare('SELECT id, tipo_usuario, nombre, apellido, correo, contrasena_hash FROM usuarios WHERE correo = :correo LIMIT 1');
    $stmt->execute([':correo' => $correo]);
    $user = $stmt->fetch();

    if (!$user) {
        redirect_with_error('Credenciales inválidas.');
    }

    if (!password_verify($contrasena, (string)$user['contrasena_hash'])) {
        redirect_with_error('Credenciales inválidas.');
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'tipo_usuario' => (string)$user['tipo_usuario'],
        'nombre' => (string)$user['nombre'],
        'apellido' => (string)$user['apellido'],
        'correo' => (string)$user['correo'],
    ];

    if ($_SESSION['user']['tipo_usuario'] === 'admin') {
        header('Location: admin.php');
        exit;
    }

    header('Location: cliente.php');
    exit;
} catch (Throwable $e) {
    redirect_with_error('Error al iniciar sesión.');
}
