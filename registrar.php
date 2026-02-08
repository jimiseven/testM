<?php

declare(strict_types=1);

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

$tipo_usuario = isset($_POST['tipo_usuario']) ? trim((string)$_POST['tipo_usuario']) : '';
$nombre = isset($_POST['nombre']) ? trim((string)$_POST['nombre']) : '';
$apellido = isset($_POST['apellido']) ? trim((string)$_POST['apellido']) : '';
$pais = isset($_POST['pais']) ? trim((string)$_POST['pais']) : '';
$fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? trim((string)$_POST['fecha_nacimiento']) : '';
$correo = isset($_POST['correo']) ? trim((string)$_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? (string)$_POST['contrasena'] : '';
$contrasena2 = isset($_POST['contrasena2']) ? (string)$_POST['contrasena2'] : '';

if (!in_array($tipo_usuario, ['admin', 'cliente'], true)) {
    redirect_with_error('Tipo de usuario inválido.');
}

if ($nombre === '' || mb_strlen($nombre) > 100) {
    redirect_with_error('Nombre inválido.');
}

if ($apellido === '' || mb_strlen($apellido) > 100) {
    redirect_with_error('Apellido inválido.');
}

if ($pais === '' || mb_strlen($pais) > 100) {
    redirect_with_error('País inválido.');
}

$dt = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
$dt_errors = DateTime::getLastErrors();
if ($dt === false || ($dt_errors !== false && ($dt_errors['warning_count'] > 0 || $dt_errors['error_count'] > 0))) {
    redirect_with_error('Fecha de nacimiento inválida.');
}

if ($correo === '' || mb_strlen($correo) > 255 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Correo inválido.');
}

if ($contrasena === '' || strlen($contrasena) < 6 || strlen($contrasena) > 72) {
    redirect_with_error('La contraseña debe tener entre 6 y 72 caracteres.');
}

if ($contrasena !== $contrasena2) {
    redirect_with_error('Las contraseñas no coinciden.');
}

try {
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = :correo LIMIT 1');
    $stmt->execute([':correo' => $correo]);
    $exists = $stmt->fetch();
    if ($exists) {
        redirect_with_error('Este correo ya está registrado.');
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    if ($hash === false) {
        redirect_with_error('No se pudo procesar la contraseña.');
    }

    $insert = $pdo->prepare(
        'INSERT INTO usuarios (tipo_usuario, nombre, apellido, pais, fecha_nacimiento, correo, contrasena_hash)
         VALUES (:tipo_usuario, :nombre, :apellido, :pais, :fecha_nacimiento, :correo, :contrasena_hash)'
    );

    $insert->execute([
        ':tipo_usuario' => $tipo_usuario,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':pais' => $pais,
        ':fecha_nacimiento' => $fecha_nacimiento,
        ':correo' => $correo,
        ':contrasena_hash' => $hash,
    ]);

    header('Location: index.php?ok=1');
    exit;
} catch (Throwable $e) {
    redirect_with_error('Error al registrar.');
}
