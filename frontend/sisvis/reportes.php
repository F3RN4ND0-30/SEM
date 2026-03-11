<?php
// frontend/sisvis/reportes.php
session_start();
require_once __DIR__ . '/../../backend/db/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userName    = $_SESSION['user_name'] ?? 'Usuario';
$userInitial = strtoupper(substr($userName, 0, 1));
$userType    = $_SESSION['user_type'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reportes — SEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../backend/css/navbar/navbar.css">
    <link rel="stylesheet" href="../../backend/css/sisvis/reportes.css">
    <link rel="icon" type="image/png" href="../../backend/img/logoPisco.png" />
</head>
<body>

    <?php include "../navbar/navbar.php"; ?>

    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <button id="toggleSidebar" class="topbar-toggle">☰</button>
            <div class="topbar-title">Reporte de <span>Empadronamiento</span></div>
            <div class="topbar-right">
                <span class="badge-tag">En vivo</span>
                <div class="user-chip">
                    <div class="user-avatar"><?= htmlspecialchars($userInitial) ?></div>
                    <?= htmlspecialchars($userName) ?>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="content">
        <div class="reportes-page">

            <!-- ENCABEZADO -->
            <div class="rp-head">
                <div class="rp-head-left">
                    <div class="rp-head-icon">📋</div>
                    <div class="rp-head-txt">
                        <h1>Reportes</h1>
                        <p>Filtra y exporta los registros de empadronamiento</p>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="panel filtros-panel">
                <div class="filtros-top">
                    <svg width="16" height="16" fill="none" stroke="#ff6b81" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <h2>Filtros de búsqueda</h2>
                    <span class="badge-tag" id="chip-activos">0 activos</span>
                </div>

                <div class="filtros-grid">
                    <div class="campo">
                        <label>Fecha inicio</label>
                        <input type="date" id="fi">
                    </div>
                    <div class="campo">
                        <label>Fecha fin</label>
                        <input type="date" id="ff">
                    </div>
                    <div class="campo">
                        <label>Empadronador</label>
                        <select id="em">
                            <option value="">— Todos —</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label>Tipo CSE</label>
                        <select id="tp">
                            <option value="">— Todos —</option>
                            <option value="NO POBRE">NO POBRE</option>
                            <option value="POBRE">POBRE</option>
                            <option value="POBRE EXTREMO">POBRE EXTREMO</option>
                        </select>
                    </div>
                </div>

                <div class="filtros-acciones">
                    <button class="btn btn-ghost" onclick="limpiar()">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 .49-3.64"/>
                        </svg>
                        Limpiar
                    </button>
                    <button class="btn btn-green" id="btn-excel" onclick="exportar('excel')" disabled>
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Exportar Excel
                    </button>
                    <button class="btn btn-red" id="btn-pdf" onclick="exportar('pdf')" disabled>
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        Exportar PDF
                    </button>
                    <button class="btn btn-primary" onclick="buscar()">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Buscar
                    </button>
                </div>
            </div>

            <!-- STAT CHIPS -->
            <div class="stat-chips" id="stat-chips" style="display:none">
                <div class="chip blue">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">Total</div>
                        <div class="chip-value" id="s-total">0</div>
                    </div>
                </div>
                <div class="chip green">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">No Pobre</div>
                        <div class="chip-value" id="s-nopobre">0</div>
                    </div>
                </div>
                <div class="chip yellow">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">Pobre</div>
                        <div class="chip-value" id="s-pobre">0</div>
                    </div>
                </div>
                <div class="chip red">
                    <div class="chip-dot"></div>
                    <div>
                        <div class="chip-label">Pobre Extremo</div>
                        <div class="chip-value" id="s-extremo">0</div>
                    </div>
                </div>
            </div>

            <!-- TABLA -->
            <div class="panel tabla-panel" id="tabla-panel" style="display:none">
                <div class="tabla-head-bar">
                    <h3>Resultados</h3>
                    <span class="pill blue" id="total-pill">0 registros</span>
                </div>

                <div class="tabla-scroll">
                    <table class="rp-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>DNI</th>
                                <th>Solicitante</th>
                                <th>Tipo CSE</th>
                                <th>Tipo Solicitud</th>
                                <th>Integrantes</th>
                                <th>Archivador</th>
                                <th>Empadronador</th>
                                <th>Fecha</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>

                <div class="estado-vacio" id="estado-vacio" style="display:none">
                    <div class="ev-icon">🔍</div>
                    <h4>Sin resultados</h4>
                    <p>No hay registros para los filtros aplicados.</p>
                </div>

                <div class="paginacion" id="paginacion" style="display:none">
                    <span class="pag-info" id="pag-info"></span>
                    <div class="pag-btns" id="pag-btns"></div>
                </div>
            </div>

        </div>
        </div><!-- /content -->
    </div><!-- /main -->

    <!-- Loading -->
    <div class="loading-overlay" id="loading">
        <div class="loading-box">
            <div class="spinner"></div>
            <p id="loading-txt">Cargando...</p>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-msg" id="toast"></div>

    <script src="../../backend/js/navbar/sidebar-toggle.js"></script>

    <script>
    const API  = '../../backend/php/empa/reportes_api.php';
    const EXPO = '../../backend/php/empa/';

    let pagina   = 1;
    const POR    = 25;
    let tPags    = 1;
    let hayDatos = false;

    document.addEventListener('DOMContentLoaded', () => {
        cargarEmpadronadores();
        ['fi','ff','em','tp'].forEach(id =>
            document.getElementById(id)?.addEventListener('change', actualizarChip)
        );
        // Cargar todos los datos al entrar
        buscar(1);
    });

    function getFiltros() {
        return {
            fecha_inicio: document.getElementById('fi').value,
            fecha_fin   : document.getElementById('ff').value,
            empadronador: document.getElementById('em').value,
            tipo_cse    : document.getElementById('tp').value,
        };
    }

    function actualizarChip() {
        const n = Object.values(getFiltros()).filter(Boolean).length;
        const el = document.getElementById('chip-activos');
        el.textContent = n + ' activo' + (n !== 1 ? 's' : '');
        el.style.background = n > 0 ? 'rgba(200,16,46,.2)' : '';
    }

    async function cargarEmpadronadores() {
        try {
            const r = await fetch(API + '?accion=empadronadores');
            const d = await r.json();
            if (!d.ok) return;
            const sel = document.getElementById('em');
            d.lista.forEach(nombre => {
                const o = document.createElement('option');
                o.value = o.textContent = nombre;
                sel.appendChild(o);
            });
        } catch {}
    }

    async function buscar(pag = 1) {
        pagina = pag;
        const f = getFiltros();

        if (f.fecha_inicio && f.fecha_fin && f.fecha_inicio > f.fecha_fin) {
            toast('La fecha inicio no puede ser mayor que la fecha fin.', 'err');
            return;
        }

        showLoading('Buscando registros...');

        const qs = new URLSearchParams({ ...f, pagina, por_pagina: POR });

        try {
            const [rD, rS] = await Promise.all([
                fetch(API + '?accion=listar&'       + qs),
                fetch(API + '?accion=estadisticas&' + new URLSearchParams(f)),
            ]);
            const datos = await rD.json();
            const stats = await rS.json();

            hideLoading();

            if (datos.error) { toast(datos.error, 'err'); return; }

            renderTabla(datos);
            renderStats(stats);

            tPags    = datos.total_paginas;
            hayDatos = datos.total > 0;

            document.getElementById('btn-excel').disabled = !hayDatos;
            document.getElementById('btn-pdf').disabled   = !hayDatos;
            document.getElementById('tabla-panel').style.display = 'block';
            document.getElementById('stat-chips').style.display  = 'grid';

        } catch (err) {
            hideLoading();
            toast('Error de conexión al servidor.', 'err');
            console.error(err);
        }
    }

    function renderTabla(data) {
        const tbody = document.getElementById('tbody');
        const empty = document.getElementById('estado-vacio');
        const pag   = document.getElementById('paginacion');
        const pill  = document.getElementById('total-pill');
        const base  = (pagina - 1) * POR;

        pill.textContent = data.total.toLocaleString('es-PE') +
            ' registro' + (data.total !== 1 ? 's' : '');

        tbody.innerHTML = '';

        if (!data.datos.length) {
            empty.style.display = 'block';
            pag.style.display   = 'none';
            return;
        }

        empty.style.display = 'none';
        pag.style.display   = 'flex';

        const cseClass = {
            'NO POBRE'     : 'badge-cse cse-np',
            'POBRE'        : 'badge-cse cse-p',
            'POBRE EXTREMO': 'badge-cse cse-pe',
        };

        tbody.innerHTML = data.datos.map((r, i) => `
            <tr>
                <td class="td-num">${base + i + 1}</td>
                <td class="td-id">${esc(r.IdEmpa)}</td>
                <td class="td-dni">${esc(r.dni)}</td>
                <td class="td-bold">${esc(r.solicitante)}</td>
                <td><span class="${cseClass[r.tipo_cse] || 'badge-cse cse-pe'}">${esc(r.tipo_cse)}</span></td>
                <td>${esc(r.tipo_solicitud)}</td>
                <td style="text-align:center">${esc(r.integrantes)}</td>
                <td style="text-align:center">${esc(r.archivador)}</td>
                <td>${esc(r.empadronador)}</td>
                <td class="td-date">${esc(r.fecha)}</td>
                <td class="td-obs" title="${esc(r.observaciones)}">${esc(r.observaciones)}</td>
            </tr>`).join('');

        renderPaginacion(data.total, data.total_paginas);
    }

    function renderPaginacion(total, totalPags) {
        const info = document.getElementById('pag-info');
        const btns = document.getElementById('pag-btns');
        const desde = (pagina - 1) * POR + 1;
        const hasta = Math.min(pagina * POR, total);

        info.textContent = `${desde}–${hasta} de ${total.toLocaleString('es-PE')}`;
        btns.innerHTML = '';

        const mkBtn = (label, disabled, onClick, active = false) => {
            const b = document.createElement('button');
            b.className = 'pbtn' + (active ? ' activo' : '');
            b.textContent = label;
            b.disabled = disabled;
            if (onClick && !disabled) b.onclick = onClick;
            return b;
        };

        btns.appendChild(mkBtn('‹', pagina === 1, () => buscar(pagina - 1)));
        rango(pagina, totalPags).forEach(p => {
            btns.appendChild(mkBtn(
                p === '…' ? '…' : p,
                p === '…',
                p !== '…' ? () => buscar(p) : null,
                p === pagina
            ));
        });
        btns.appendChild(mkBtn('›', pagina === totalPags || totalPags === 0, () => buscar(pagina + 1)));
    }

    function rango(actual, total) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
        if (actual <= 4) return [1, 2, 3, 4, 5, '…', total];
        if (actual >= total - 3) return [1, '…', total-4, total-3, total-2, total-1, total];
        return [1, '…', actual-1, actual, actual+1, '…', total];
    }

    function renderStats(data) {
        if (!data.ok) return;
        const s = data.stats;
        countUp(document.getElementById('s-total'),   s.total);
        countUp(document.getElementById('s-nopobre'), s.no_pobre);
        countUp(document.getElementById('s-pobre'),   s.pobre);
        countUp(document.getElementById('s-extremo'), s.extremo);
    }

    function countUp(el, target) {
        const t = parseInt(target) || 0;
        if (t === 0) { el.textContent = '0'; return; }
        let current = 0;
        const step = t / (1200 / 16);
        const timer = setInterval(() => {
            current = Math.min(current + step, t);
            el.textContent = Math.floor(current).toLocaleString('es-PE');
            if (current >= t) clearInterval(timer);
        }, 16);
    }

    function exportar(tipo) {
        if (!hayDatos) { toast('Primero realiza una búsqueda.', 'err'); return; }
        const qs  = new URLSearchParams(getFiltros()).toString();
        const arc = tipo === 'excel' ? 'exportar_excel.php' : 'exportar_pdf.php';
        if (tipo === 'excel') {
            window.location.href = EXPO + arc + '?' + qs;
            toast('Descargando Excel...', 'ok');
        } else {
            window.open(EXPO + arc + '?' + qs, '_blank');
            toast('Abriendo PDF...', 'ok');
        }
    }

    function limpiar() {
        ['fi','ff'].forEach(id => document.getElementById(id).value = '');
        ['em','tp'].forEach(id => document.getElementById(id).selectedIndex = 0);
        actualizarChip();
        buscar(1); // recarga todos los datos sin filtro
    }

    function esc(s) {
        if (s === null || s === undefined || s === '') return '<span style="color:#3a3f4f">—</span>';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function showLoading(msg) {
        document.getElementById('loading-txt').textContent = msg || 'Cargando...';
        document.getElementById('loading').classList.add('on');
    }
    function hideLoading() {
        document.getElementById('loading').classList.remove('on');
    }

    function toast(msg, tipo = '') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className   = 'toast-msg show' + (tipo ? ' ' + tipo : '');
        clearTimeout(t._t);
        t._t = setTimeout(() => t.classList.remove('show'), 3000);
    }

    document.addEventListener('keydown', ev => { if (ev.key === 'Enter') buscar(); });
    </script>

</body>
</html>