<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inicio de Sesión</title>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;800;900&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Barlow', sans-serif;
      background: #f5f0eb;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: repeating-linear-gradient(
        -55deg, transparent, transparent 40px,
        rgba(200,16,46,0.03) 40px, rgba(200,16,46,0.03) 41px
      );
      pointer-events: none;
    }

    .top-bar {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 5px;
      display: flex;
    }
    .top-bar div:nth-child(1) { background: #C8102E; flex: 1; }
    .top-bar div:nth-child(2) { background: #f5f0eb; flex: 1; }
    .top-bar div:nth-child(3) { background: #C8102E; flex: 1; }

    .wrapper {
      width: 100%;
      max-width: 400px;
      padding: 20px;
      animation: fadeUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      justify-content: center;
      margin-bottom: 28px;
    }

    .brand-flag {
      display: flex;
      border-radius: 3px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      height: 26px;
    }
    .brand-flag div { width: 10px; }
    .brand-flag div:nth-child(1),
    .brand-flag div:nth-child(3) { background: #C8102E; }
    .brand-flag div:nth-child(2) { background: white; }

    .brand-name {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: #1a3a6b;
    }

    .card {
      background: white;
      border-radius: 18px;
      padding: 36px 32px 32px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border-top: 3px solid #C8102E;
    }

    .card-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 30px;
      font-weight: 900;
      color: #1a1a2e;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      line-height: 1;
      margin-bottom: 4px;
    }
    .card-title span { color: #C8102E; }

    .card-sub {
      font-size: 13px;
      color: #999;
      margin-bottom: 26px;
    }

    hr {
      border: none;
      height: 1px;
      background: #271919;
      margin-bottom: 22px;
    }

    .field { margin-bottom: 16px; }

    label {
      display: block;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #1a3a6b;
      margin-bottom: 7px;
    }

    .input-wrap { position: relative; }

    .icon {
      position: absolute;
      left: 13px; top: 50%;
      transform: translateY(-50%);
      color: #ccc;
      display: flex;
      align-items: center;
      pointer-events: none;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      background: #fafafa;
      border: 1.5px solid #ececec;
      border-radius: 10px;
      padding: 11px 13px 11px 38px;
      font-family: 'Barlow', sans-serif;
      font-size: 14px;
      color: #1a1a2e;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    input::placeholder { color: #ccc; }
    input:focus {
      border-color: #C8102E;
      background: white;
      box-shadow: 0 0 0 3px rgba(200,16,46,0.08);
    }

    .toggle-btn {
      position: absolute;
      right: 11px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      color: #ccc; cursor: pointer;
      display: flex; align-items: center;
      padding: 4px;
      transition: color 0.2s;
    }
    .toggle-btn:hover { color: #888; }

    .row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 18px 0 22px;
      font-size: 12px;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 7px;
      color: #888;
      cursor: pointer;
      user-select: none;
    }
    input[type="checkbox"] {
      width: 14px; height: 14px;
      accent-color: #C8102E;
      cursor: pointer; padding: 0;
    }

    .forgot {
      color: #C8102E;
      text-decoration: none;
      font-weight: 600;
    }
    .forgot:hover { text-decoration: underline; }

    .btn {
      width: 100%;
      padding: 13px;
      background: #C8102E;
      color: white;
      border: none;
      border-radius: 10px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 16px;
      font-weight: 800;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(200,16,46,0.28);
    }
    .btn:hover {
      background: #a50d25;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(200,16,46,0.38);
    }
    .btn:active { transform: translateY(0); }

    .foot-note {
      text-align: center;
      font-size: 12px;
      color: #aaa;
      margin-top: 20px;
    }
    .foot-note a { color: #1a3a6b; font-weight: 600; text-decoration: none; }
    .foot-note a:hover { text-decoration: underline; }

    .status-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 6px;
      margin-top: 18px;
      font-size: 11px;
      color: #bbb;
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #22c55e; }
  </style>
</head>
<body>

<div class="top-bar"><div></div><div></div><div></div></div>

<div class="wrapper">

  <div class="brand">
    <div class="brand-flag">
      <div></div><div></div><div></div>
    </div>
    <span class="brand-name">Sistema Nacional</span>
  </div>

  <div class="card">
    <div class="card-title">Inicio de <span>Sesión</span></div>
    <div class="card-sub">Ingresa tus credenciales para continuar</div>
    <hr/>

    <form action="login.php" method="POST">

      <div class="field">
        <label for="usuario">Usuario</label>
        <div class="input-wrap">
          <span class="icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" autocomplete="username"/>
        </div>
      </div>

      <div class="field">
        <label for="contrasena">Contraseña</label>
        <div class="input-wrap">
          <span class="icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" autocomplete="current-password"/>
          <button type="button" class="toggle-btn" onclick="togglePass(this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="row">
        <label class="remember">
          <input type="checkbox" name="recordar"/> Recordarme
        </label>
        <a href="#" class="forgot"></a>
      </div>

      <button type="submit" class="btn">
        Ingresar
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
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
    btn.querySelector('svg').innerHTML = isPass
      ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`
      : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
  }
</script>

</body>
</html>