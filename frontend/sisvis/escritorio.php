<?php
require_once "../../frontend/auth.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Escritorio - Empadronamiento</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&family=Barlow+Condensed:wght@700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css" />
    <link rel="stylesheet" href="../../backend/css/sisvis/escritorio.css" />
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
</head>

<body>

    <?php include "../navbar/navbar.php"; ?>

    <main class="dashboard">

        <div class="brand">
            <div class="brand-name">Empadronamiento</div>
        </div>

        <section class="stats">
            <article class="card stat-card">
                <div class="card-title">150</div>
                <div class="card-sub">No Pobre</div>
            </article>
            <article class="card stat-card">
                <div class="card-title">80</div>
                <div class="card-sub">Pobre</div>
            </article>
            <article class="card stat-card">
                <div class="card-title">25</div>
                <div class="card-sub">Pobre Extremo</div>
            </article>
        </section>

        <section class="chart-card card">
            <h2 class="card-title">Distribución</h2>
            <hr />
            <div class="chart">
                <div class="bar no-poor" style="height:60%"></div>
                <div class="bar poor" style="height:32%"></div>
                <div class="bar extreme" style="height:8%"></div>
            </div>
            <div class="chart-labels">
                <span>No Pobre</span>
                <span>Pobre</span>
                <span>Pobre Extremo</span>
            </div>
        </section>

        <section class="additional-stats">
            <article class="card">
                <div class="card-title">Total Registrados</div>
                <div class="card-sub">255 Personas</div>
            </article>
            <article class="card">
                <div class="card-title">Actualizaciones Hoy</div>
                <div class="card-sub">12 Nuevos</div>
            </article>
            <article class="card">
                <div class="card-title">Usuarios Activos</div>
                <div class="card-sub">5 Administradores</div>
            </article>
        </section>
    </main>
</body>

<!-- script para el navbar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.body-overlay');
        const toggleBtn = document.querySelector('.btn-toggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                if (window.innerWidth <= 768) overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
    });
</script>

</html>