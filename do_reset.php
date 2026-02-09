<?php

declare(strict_types=1);

require __DIR__ . '/db.php';

function redirect_with_error(string $msg): void
{
    header('Location: reset.php?error=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reset.php');
    exit;
}

$correo = isset($_POST['correo']) ? trim((string)$_POST['correo']) : '';
$codigo = isset($_POST['codigo']) ? trim((string)$_POST['codigo']) : '';
$contrasena = isset($_POST['contrasena']) ? (string)$_POST['contrasena'] : '';
$contrasena2 = isset($_POST['contrasena2']) ? (string)$_POST['contrasena2'] : '';

if ($correo === '' || mb_strlen($correo) > 255 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Correo inválido.');
}

if ($codigo === '' || strlen($codigo) < 6 || strlen($codigo) > 12) {
    redirect_with_error('Código inválido.');
}

if ($contrasena === '' || strlen($contrasena) < 6 || strlen($contrasena) > 72) {
    redirect_with_error('La contraseña debe tener entre 6 y 72 caracteres.');
}

if ($contrasena !== $contrasena2) {
    redirect_with_error('Las contraseñas no coinciden.');
}

try {
    $stmt = $pdo->prepare('SELECT id, reset_code_hash, reset_code_expires FROM usuarios WHERE correo = :correo LIMIT 1');
    $stmt->execute([':correo' => $correo]);
    $user = $stmt->fetch();

    if (!$user) {
        redirect_with_error('Código o correo incorrecto.');
    }

    $hash = (string)($user['reset_code_hash'] ?? '');
    $expires = (string)($user['reset_code_expires'] ?? '');

    if ($hash === '' || $expires === '') {
        redirect_with_error('Código o correo incorrecto.');
    }

    $now = new DateTimeImmutable('now');
    $exp = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $expires);
    if ($exp === false || $exp < $now) {
        redirect_with_error('El código expiró. Solicita uno nuevo.');
    }

    if (!password_verify($codigo, $hash)) {
        redirect_with_error('Código o correo incorrecto.');
    }

    $newHash = password_hash($contrasena, PASSWORD_DEFAULT);
    if ($newHash === false) {
        redirect_with_error('No se pudo procesar la contraseña.');
    }

    $upd = $pdo->prepare('UPDATE usuarios SET contrasena_hash = :ph, reset_code_hash = NULL, reset_code_expires = NULL WHERE id = :id');
    $upd->execute([
        ':ph' => $newHash,
        ':id' => (int)$user['id'],
    ]);

    header('Location: reset.php?ok=1');
    exit;
} catch (Throwable $e) {
    redirect_with_error('No se pudo completar el reset.');
}
