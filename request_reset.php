<?php

declare(strict_types=1);

require __DIR__ . '/db.php';

function redirect_with_error(string $msg): void
{
    header('Location: forgot.php?error=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot.php');
    exit;
}

$correo = isset($_POST['correo']) ? trim((string)$_POST['correo']) : '';

if ($correo === '' || mb_strlen($correo) > 255 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    redirect_with_error('Correo inválido.');
}

try {
    $stmt = $pdo->prepare('SELECT id, nombre, apellido, correo FROM usuarios WHERE correo = :correo LIMIT 1');
    $stmt->execute([':correo' => $correo]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: forgot.php?ok=1');
        exit;
    }

    $code = (string)random_int(100000, 999999);
    $codeHash = password_hash($code, PASSWORD_DEFAULT);
    if ($codeHash === false) {
        header('Location: forgot.php?ok=1');
        exit;
    }

    $expires = (new DateTimeImmutable('now'))->modify('+15 minutes')->format('Y-m-d H:i:s');

    $upd = $pdo->prepare('UPDATE usuarios SET reset_code_hash = :h, reset_code_expires = :e WHERE id = :id');
    $upd->execute([
        ':h' => $codeHash,
        ':e' => $expires,
        ':id' => (int)$user['id'],
    ]);

    $toName = trim(((string)$user['nombre']) . ' ' . ((string)$user['apellido']));

    require __DIR__ . '/mailer.php';

    $subject = 'Código de recuperación';
    $html = '<p>Tu código de recuperación es:</p><h2>' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</h2><p>Este código expira en 15 minutos.</p>';

    send_mail((string)$user['correo'], $toName !== '' ? $toName : (string)$user['correo'], $subject, $html);

    header('Location: forgot.php?ok=1');
    exit;
} catch (Throwable $e) {
    redirect_with_error('No se pudo procesar la solicitud.');
}
