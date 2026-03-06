<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../backend/css/login.css" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;800;900&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="top-bar">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="wrapper">

        <div class="brand">
            <div class="brand-flag">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <span class="brand-name">Sistema Nacional</span>
        </div>

        <div class="card">
            <div class="card-title">Inicio de <span>Sesión</span></div>
            <div class="card-sub">Ingresa tus credenciales para continuar</div>
            <hr />

            <form action="../backend/php/login_process.php" method="POST">

                <div class="field">
                    <label for="usuario">correo</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <input type="email" id="correo" name="correo" placeholder="Tu correo" autocomplete="username" />
                    </div>
                </div>

                <div class="field">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" id="pass" name="pass" placeholder="••••••••" autocomplete="current-password" />
                        <button type="button" class="toggle-btn" onclick="togglePass(this)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" name="recordar" /> Recordarme
                    </label>
                    <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="btn">
                    Ingresar
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>

            </form>

    <div class="foot-note">
      ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
  </div>

        <div class="status-row">
            <span class="status-dot"></span> Conexión segura
        </div>

    </div>

    <script>
        function togglePass(btn) {
            const input = document.getElementById('contrasena');
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.querySelector('svg').innerHTML = isPass ?
                `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>` :
                `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    </script>

</body>

</html>