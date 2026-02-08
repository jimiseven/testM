<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$missing = [];
$base = __DIR__ . '/lib/PHPMailer/';
foreach (['PHPMailer.php', 'SMTP.php', 'Exception.php'] as $f) {
    if (!is_file($base . $f)) {
        $missing[] = $f;
    }
}

if ($missing) {
    echo "Faltan archivos de PHPMailer en {$base}\n";
    echo "Copia estos archivos desde PHPMailer/src/:\n";
    foreach ($missing as $m) {
        echo "- {$m}\n";
    }
    exit;
}

$cfg = require __DIR__ . '/smtp_config.php';

if (!is_string($cfg['username'] ?? null) || trim((string)$cfg['username']) === '' ||
    !is_string($cfg['password'] ?? null) || trim((string)$cfg['password']) === '' ||
    !is_string($cfg['from_email'] ?? null) || trim((string)$cfg['from_email']) === '') {
    echo "Completa smtp_config.php con username/password/from_email antes de probar.\n";
    exit;
}

$to = isset($_GET['to']) ? trim((string)$_GET['to']) : '';
if ($to === '') {
    echo "Uso: http://localhost/testM/send_test.php?to=TU_CORREO@DOMINIO\n";
    echo "Ejemplo: http://localhost/testM/send_test.php?to=" . urlencode((string)$cfg['username']) . "\n";
    exit;
}

try {
    require __DIR__ . '/mailer.php';

    $subject = 'Test SMTP PHPMailer - TestM';
    $body = '<p>Correo de prueba enviado desde <b>TestM</b>.</p><p>Fecha: ' . htmlspecialchars(date('Y-m-d H:i:s'), ENT_QUOTES, 'UTF-8') . '</p>';

    send_mail($to, $to, $subject, $body);

    echo "OK: correo enviado a {$to}\n";
} catch (Throwable $e) {
    echo "ERROR enviando correo: " . $e->getMessage() . "\n";
}
