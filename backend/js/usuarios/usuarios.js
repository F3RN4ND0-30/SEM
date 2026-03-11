// === URL de la API (Global) ===
const API = "../../backend/php/usuarios/fcs_usuarios.php";

$(document).ready(function () {
    console.log("Módulo Usuarios SEM conectado a la BD");

    // Inicializar DataTable
    const tabla = $("#tablaUsuarios").DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
        ajax: {
            url: `${API}?action=listar`,
            dataSrc: function (json) {
                if (!json || !json.data) {
                    console.error("Respuesta inválida del servidor:", json);
                    mostrarAlertaInterna("Error al cargar usuarios", "danger");
                    return [];
                }
                return json.data;
            },
        },
        columns: [
            { data: "IdUsuario" },
            { data: "Nombres" },
            { data: "Ape_Pat" },
            { data: "Ape_Mat" },
            { data: "DNI" },
            { data: "Correo" },
            { data: "tipo_nombre" }, // Nombre del tipo de usuario
            {
                data: "Estado",
                render: (d) =>
                    d == 1
                        ? '<span class="usuarios-badge-activo">Activo</span>'
                        : '<span class="usuarios-badge-inactivo">Inactivo</span>',
            },
            {
                data: null,
                render: (r) => `
          <div class="usuarios-btn-group">
              <button class="usuarios-btn-accion usuarios-btn-editar" 
                onclick="editarUsuario(${r.IdUsuario})" 
                title="Editar"><i class="material-icons">edit</i></button>
              <button class="usuarios-btn-accion usuarios-btn-resetear" 
                onclick="cambiarPasswordUsuario(${r.IdUsuario})" 
                title="Cambiar Contraseña"><i class="material-icons">lock_reset</i></button>
              <button class="usuarios-btn-accion usuarios-btn-toggle ${r.Estado ? "" : "activar"
                    }" 
                onclick="toggleUsuario(${r.IdUsuario}, ${r.Estado})" 
                title="${r.Estado ? "Desactivar" : "Activar"}">
                <i class="material-icons">${r.Estado ? "person_off" : "person_add"
                    }</i></button>
          </div>`,
            },
        ],
        order: [[0, "desc"]],
        responsive: true,
    });

    // Cargar tipos de usuario al abrir modal de crear usuario
    $("#modalCrearUsuario").on("show.bs.modal", function () {
        cargarTiposUsuario("#tipo_usuario");
    });

    // Resetear formulario al cerrar modal de crear usuario
    $("#modalCrearUsuario").on("hidden.bs.modal", function () {
        $("#formCrearUsuario")[0].reset();
        $("#formCrearUsuario .is-invalid").removeClass("is-invalid");
    });

    // Guardar nuevo usuario
    $("#btnGuardarUsuario").click(() => {
        if (validarFormulario("#formCrearUsuario")) guardarUsuario();
    });

    // Actualizar usuario
    $("#btnActualizarUsuario").click(() => {
        if (validarFormulario("#formEditarUsuario")) actualizarUsuario();
    });

    // Generar contraseña automática
    $("#btnGenerarPass").click(function () {
        const caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%";
        let password = "";

        for (let i = 0; i < 10; i++) {
            password += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }

        $("#pass").val(password).attr("type", "text");
    });

    // Guardar nueva contraseña
    $("#btnGuardarPassword").click(() => {
        const nueva = $("#nueva_password").val();
        const confirmar = $("#confirmar_password").val();
        if (!nueva || !confirmar)
            return mostrarAlertaModal("#modalPasswordUsuario", "Complete todos los campos", "warning");
        if (nueva !== confirmar)
            return mostrarAlertaModal("#modalPasswordUsuario", "Las contraseñas no coinciden", "danger");

        fetchPost("password", {
            id_usuario: $("#pass_id_usuario").val(),
            nueva,
        }).then(() => {
            $("#modalPasswordUsuario").modal("hide");
            recargarTabla();
        });
    });
});

/* === FUNCIONES AJAX === */
function fetchGet(action) {
    return fetch(`${API}?action=${action}`)
        .then((r) => r.json())
        .catch((err) => {
            console.error("Error en fetchGet:", err);
            mostrarAlertaInterna("Error de conexión con el servidor", "danger");
        });
}

function fetchPost(action, data) {
    return fetch(`${API}?action=${action}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    })
        .then((r) => r.json())
        .then((res) => {
            if (res.status === "success")
                mostrarAlertaInterna(res.message, "success");
            else mostrarAlertaInterna(res.message, "danger");
            return res;
        })
        .catch((err) => {
            console.error("Error en fetchPost:", err);
            mostrarAlertaInterna("Error de conexión con el servidor", "danger");
        });
}

function recargarTabla() {
    $("#tablaUsuarios").DataTable().ajax.reload(null, false);
}

/* === CRUD === */
function guardarUsuario() {
    const data = {
        Nombres: $("#nombres").val(),
        Ape_Pat: $("#ape_pat").val(),
        Ape_Mat: $("#ape_mat").val(),
        DNI: $("#dni").val(),
        Correo: $("#correo").val(),
        Pass: $("#pass").val(),
        IdTipoUsuario: $("#tipo_usuario").val(),
    };
    fetchPost("crear", data).then(() => {
        $("#modalCrearUsuario").modal("hide");
        recargarTabla();
    });
}

function editarUsuario(id) {
    fetchGet(`obtener&id=${id}`).then((res) => {
        if (res.status === "success") {
            const u = res.data;
            $("#edit_id_usuario").val(u.IdUsuario);
            $("#edit_nombres").val(u.Nombres);
            $("#edit_ape_pat").val(u.Ape_Pat);
            $("#edit_ape_mat").val(u.Ape_Mat);
            $("#edit_dni").val(u.DNI);
            $("#edit_correo").val(u.Correo);
            cargarTiposUsuario("#edit_tipo_usuario", u.IdTipoUsuario);
            $("#edit_estado").val(u.Estado);
            $("#modalEditarUsuario").modal("show");
        }
    });
}

function actualizarUsuario() {
    const data = {
        IdUsuario: $("#edit_id_usuario").val(),
        Nombres: $("#edit_nombres").val(),
        Ape_Pat: $("#edit_ape_pat").val(),
        Ape_Mat: $("#edit_ape_mat").val(),
        Correo: $("#edit_correo").val(),
        IdTipoUsuario: $("#edit_tipo_usuario").val(),
        Estado: $("#edit_estado").val(),
    };
    fetchPost("editar", data).then(() => {
        $("#modalEditarUsuario").modal("hide");
        recargarTabla();
    });
}

function cambiarPasswordUsuario(id) {
    $("#pass_id_usuario").val(id);
    $("#formPasswordUsuario")[0].reset();
    $("#modalPasswordUsuario").modal("show");
}

function toggleUsuario(id, estado) {
    if (confirm(`¿Desea ${estado ? "desactivar" : "activar"} este usuario?`)) {
        fetchPost("toggle", { IdUsuario: id, Estado: estado ? 0 : 1 }).then(() => recargarTabla());
    }
}

/* === UTILITARIOS === */
function cargarTiposUsuario(selector, seleccionado = "") {
    fetchGet("tipos").then((res) => {
        if (res.status === "success") {
            const tipos = res.data;
            $(selector).html('<option value="">Seleccionar tipo...</option>');
            tipos.forEach((t) => {
                $(selector).append(`<option value="${t.IdTipoUsuario}" ${seleccionado == t.IdTipoUsuario ? "selected" : ""}>${t.Descripcion}</option>`);
            });
        }
    });
}

function validarFormulario(selector) {
    let valido = true;
    $(`${selector} [required]`).each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
            valido = false;
        } else $(this).removeClass("is-invalid");
    });
    return valido;
}

/* === ALERTAS === */
function mostrarAlertaInterna(msg, tipo) {
    const cont = $(".container-fluid.p-4");
    cont.find(".usuarios-alert").remove();
    cont.prepend(`<div class="alert alert-${tipo} usuarios-alert">${msg}</div>`);
    setTimeout(() => cont.find(".usuarios-alert").fadeOut(), 4000);
}

function mostrarAlertaModal(modal, msg, tipo) {
    $(`${modal} .usuarios-alert`).remove();
    $(`${modal} .modal-body`).prepend(`<div class="alert alert-${tipo} usuarios-alert">${msg}</div>`);
    setTimeout(() => $(`${modal} .usuarios-alert`).fadeOut(), 4000);
}