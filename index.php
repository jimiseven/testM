<?php

declare(strict_types=1);

session_start();

if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $tipo = $_SESSION['user']['tipo_usuario'] ?? null;
    if ($tipo === 'admin') {
        header('Location: admin.php');
        exit;
    }
    if ($tipo === 'cliente') {
        header('Location: cliente.php');
        exit;
    }
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login / Registro</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 16px; }
        form { display: grid; gap: 12px; }
        label { display: grid; gap: 6px; }
        input, select { padding: 10px; font-size: 14px; }
        button { padding: 10px 14px; font-size: 14px; cursor: pointer; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .msg { padding: 12px; border: 1px solid #ddd; background: #f7f7f7; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
        h2 { margin: 0 0 12px; }
    </style>
</head>
<body>
    <h1>Login / Registro</h1>

    <?php if (isset($_GET['ok']) && $_GET['ok'] === '1'): ?>
        <div class="msg">Usuario registrado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] !== ''): ?>
        <div class="msg">Error: <?php echo htmlspecialchars((string)$_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="grid2">
        <section>
            <h2>Login</h2>
            <form method="post" action="login.php" autocomplete="off">
                <label>
                    Correo
                    <input type="email" name="correo" required maxlength="255">
                </label>

                <label>
                    Contraseña
                    <input type="password" name="contrasena" required maxlength="72">
                </label>

                <button type="submit">Entrar</button>
            </form>
        </section>

        <section>
            <h2>Registro</h2>
            <form method="post" action="registrar.php" autocomplete="off">
                <label>
                    Tipo de usuario
                    <select name="tipo_usuario" required>
                        <option value="cliente" selected>Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                </label>

                <div class="row">
                    <label>
                        Nombre
                        <input type="text" name="nombre" required maxlength="100">
                    </label>
                    <label>
                        Apellido
                        <input type="text" name="apellido" required maxlength="100">
                    </label>
                </div>

                <div class="row">
                    <label>
                        País
                        <input type="text" name="pais" required maxlength="100">
                    </label>
                    <label>
                        Fecha de nacimiento
                        <input type="date" name="fecha_nacimiento" required>
                    </label>
                </div>

                <label>
                    Correo
                    <input type="email" name="correo" required maxlength="255">
                </label>

                <div class="row">
                    <label>
                        Contraseña
                        <input type="password" name="contrasena" required minlength="6" maxlength="72">
                    </label>
                    <label>
                        Repetir contraseña
                        <input type="password" name="contrasena2" required minlength="6" maxlength="72">
                    </label>
                </div>

                <button type="submit">Registrar</button>
            </form>
        </section>
    </div>
</body>
</html>
