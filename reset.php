<?php

declare(strict_types=1);

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resetear contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 16px; }
        form { display: grid; gap: 12px; }
        label { display: grid; gap: 6px; }
        input { padding: 10px; font-size: 14px; }
        button { padding: 10px 14px; font-size: 14px; cursor: pointer; }
        .msg { padding: 12px; border: 1px solid #ddd; background: #f7f7f7; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        a { color: #000; }
    </style>
</head>
<body>
    <h1>Resetear contraseña</h1>

    <?php if (isset($_GET['ok']) && $_GET['ok'] === '1'): ?>
        <div class="msg">Contraseña actualizada correctamente. Ya puedes iniciar sesión.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
        <div class="msg">Error: <?php echo htmlspecialchars((string)$_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="post" action="do_reset.php" autocomplete="off">
        <label>
            Correo
            <input type="email" name="correo" required maxlength="255">
        </label>

        <label>
            Código
            <input type="text" name="codigo" required minlength="6" maxlength="12">
        </label>

        <div class="row">
            <label>
                Nueva contraseña
                <input type="password" name="contrasena" required minlength="6" maxlength="72">
            </label>
            <label>
                Repetir nueva contraseña
                <input type="password" name="contrasena2" required minlength="6" maxlength="72">
            </label>
        </div>

        <button type="submit">Cambiar contraseña</button>
    </form>

    <p>
        <a href="forgot.php">Solicitar otro código</a>
    </p>

    <p>
        <a href="index.php">Volver</a>
    </p>
</body>
</html>
