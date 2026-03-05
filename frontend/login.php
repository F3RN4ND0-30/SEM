<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- login.php -->
    <form action="../backend/php/login_process.php" method="post">
        <label for="correo">Correo:</label>
        <input type="email" name="correo" id="correo" required>

        <label for="pass">Contraseña:</label>
        <input type="password" name="pass" id="pass" required>

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>

</html>