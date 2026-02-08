<?php

declare(strict_types=1);

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function make_mailer(): PHPMailer
{
    $cfg = require __DIR__ . '/smtp_config.php';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = (string)$cfg['host'];
    $mail->SMTPAuth = true;
    $mail->Username = (string)$cfg['username'];
    $mail->Password = (string)$cfg['password'];

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];

    $enc = (string)$cfg['encryption'];
    if ($enc !== '') {
        $mail->SMTPSecure = $enc;
    }

    $mail->Port = (int)$cfg['port'];

    $mail->CharSet = 'UTF-8';
    $mail->setFrom((string)$cfg['from_email'], (string)$cfg['from_name']);

    return $mail;
}

function send_mail(string $toEmail, string $toName, string $subject, string $htmlBody, string $altBody = ''): void
{
    $mail = make_mailer();

    $mail->addAddress($toEmail, $toName);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $htmlBody;
    $mail->AltBody = $altBody !== '' ? $altBody : strip_tags($htmlBody);

    $mail->send();
}
