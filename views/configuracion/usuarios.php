<div class="contenedor-95">
    <div class="ui grid stackable">
        <!-- Columna izquierda: TABLA -->
        <div class="twelve wide column">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <div class="ui action input fluid">
                        <input type="text" id="buscar-usuarios"
                            placeholder="Buscar por nombre, usuario, rol o estado (activo/inactivo)...">
                        <button class="ui icon button" id="btn-limpiar-busqueda" title="Limpiar">
                            <i class="close icon"></i>
                        </button>
                    </div>
                    <div class="pt-2">
                        <span class="ui tiny grey text" id="resultado-busqueda"></span>
                    </div>
                </div>
            </div>

            <table class="ui celled striped compact selectable table" id="tbl-usuarios-simple">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th class="one wide">Estado</th>
                        <th class="two wide">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr data-id="<?= htmlspecialchars($usuario->id) ?>">
                            <td><?= htmlspecialchars($usuario->nombre) ?></td>
                            <td><?= htmlspecialchars($usuario->usuario) ?></td>
                            <td>
                                <div class="ui label">
                                    <i class="id badge icon"></i>
                                    <?= htmlspecialchars($usuario->rol) ?>
                                </div>
                            </td>
                            <td>
                                <?php if ((int) $usuario->estado === 1): ?>
                                    <div class="ui green tiny label">Activo</div>
                                <?php else: ?>
                                    <div class="ui red tiny label">Inactivo</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="ui tiny buttons">
                                    <?php if ((int) $usuario->estado === 1): ?>
                                        <a class="ui orange icon button"
                                            href="<?= BASE_URL ?>/configuracion/usuarios-inhabilitar?id=<?= urlencode($usuario->id) ?>">
                                            <i class="ban icon"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="ui green icon button"
                                            href="<?= BASE_URL ?>/configuracion/usuarios-habilitar?id=<?= urlencode($usuario->id) ?>">
                                            <i class="check icon"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="#" class="ui blue icon button btn-editar-usuario"
                                        data-id="<?= htmlspecialchars($usuario->id) ?>"
                                        data-usuario="<?= htmlspecialchars($usuario->usuario) ?>"
                                        data-nombre="<?= htmlspecialchars($usuario->nombre) ?>"
                                        data-rol="<?= htmlspecialchars($usuario->rol) ?>"
                                        data-estado="<?= (int) $usuario->estado ?>">
                                        <i class="edit icon"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Columna derecha: FORMULARIO CREAR -->
        <div class="four wide column">
            <div class="ui top attached header">
                <i class="plus icon"></i>
                <div class="content">Nuevo usuario</div>
            </div>
            <div class="ui attached segment">
                <form class="ui form" id="frm-nuevo-usuario">
                    <input type="hidden" name="id" id="usuario_id">

                    <div class="field required">
                        <label>Usuario</label>
                        <input type="text" name="usuario" id="f_usuario" placeholder="usuario.ejemplo" required>
                    </div>

                    <div class="field required">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="f_nombre" placeholder="Nombre completo" required>
                    </div>
                    <div class="field required create-only" id="wrap_password">
                        <label>Contraseña</label>
                        <input type="password" name="password" id="f_password" placeholder="••••••••" required>
                    </div>


                    <!-- SOLO los 2 selects dentro de two fields -->
                    <div class="two fields">
                        <div class="field required">
                            <label>Rol</label>
                            <select name="rol" id="f_rol" class="ui dropdown" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="SUPERVISOR">SUPERVISOR</option>
                                <option value="TELECOBRO">TELECOBRO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fuera de two fields -->
                    <div class="field">
                        <div class="ui checkbox">
                            <input type="checkbox" name="estado" id="f_estado" value="1" checked>
                            <label>Usuario activo</label>
                        </div>
                    </div>

                    <div class="ui buttons">
                        <button type="submit" class="ui green button" id="btn-guardar">
                            <i class="save icon"></i> Guardar
                        </button>
                        <div class="or"></div>
                        <button type="button" class="ui button" id="btn-cancelar">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ====== Helpers ======
        const hasJQ = typeof window.jQuery !== 'undefined';
        if (hasJQ) {
            try {
                $('.ui.dropdown').dropdown();
                $('.ui.checkbox').checkbox();
            } catch (e) { console.warn('Init Fomantic falló:', e); }
        }

        // Polyfill mínimo por si el navegador no tiene CSS.escape
        const cssEscape = window.CSS && CSS.escape ? CSS.escape : (sel) => String(sel).replace(/"/g, '\\"');

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        const tbody = document.querySelector('#tbl-usuarios-simple tbody');
        const form = document.getElementById('frm-nuevo-usuario');

        const hId = document.getElementById('usuario_id');
        const fUsuario = document.getElementById('f_usuario');
        const fNombre = document.getElementById('f_nombre');
        const fRol = document.getElementById('f_rol');
        const fEstado = document.getElementById('f_estado');
        const fPassword = document.getElementById('f_password');
        const wrapPassword = document.getElementById('wrap_password');
        const btnGuardar = document.getElementById('btn-guardar');
        const btnCancelar = document.getElementById('btn-cancelar');

        let editMode = false;

        function goEditMode() {
            editMode = true;
            if (wrapPassword) wrapPassword.style.display = 'none';
            if (fPassword) { fPassword.required = false; fPassword.value = ''; }
            btnGuardar.innerHTML = '<i class="save icon"></i> Actualizar';
            btnGuardar.classList.remove('green');
            btnGuardar.classList.add('blue');
        }

        function goCreateMode() {
            editMode = false;
            if (wrapPassword) wrapPassword.style.display = '';
            if (fPassword) { fPassword.required = true; fPassword.value = ''; }
            btnGuardar.innerHTML = '<i class="save icon"></i> Guardar';
            btnGuardar.classList.remove('blue');
            btnGuardar.classList.add('green');
        }

        function resetForm() {
            form.reset();
            hId.value = '';
            if (hasJQ) {
                $('#f_rol').dropdown('clear');
                $('.ui.checkbox').checkbox('check');
            } else {
                fRol.value = '';
                fEstado.checked = true;
            }
            document.querySelectorAll('#tbl-usuarios-simple tbody tr').forEach(tr => tr.classList.remove('active'));
            goCreateMode();
        }

        function updateEditBtnDataset(btn, u) {
            btn.setAttribute('data-id', String(u.id));
            btn.setAttribute('data-usuario', u.usuario ?? '');
            btn.setAttribute('data-nombre', u.nombre ?? '');
            btn.setAttribute('data-rol', u.rol ?? '');
            btn.setAttribute('data-estado', String(Number(u.estado) === 1 ? 1 : 0));
        }

        // ====== CLICK Editar (cargar datos al formulario) ======
        tbody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-editar-usuario');
            if (!btn) return;
            e.preventDefault();

            const { id, usuario, nombre, rol, estado } = btn.dataset;

            hId.value = id || '';
            fUsuario.value = usuario || '';
            fNombre.value = nombre || '';

            if (hasJQ) {
                $('#f_rol').dropdown('set selected', rol || '');
                $('#f_estado').prop('checked', Number(estado) === 1);
                if (Number(estado) === 1) $('.ui.checkbox').checkbox('check');
                else $('.ui.checkbox').checkbox('uncheck');
            } else {
                fRol.value = rol || '';
                fEstado.checked = Number(estado) === 1;
            }

            goEditMode();

            document.querySelectorAll('#tbl-usuarios-simple tbody tr').forEach(tr => tr.classList.remove('active'));
            btn.closest('tr').classList.add('active');
        });

        // ====== SUBMIT (Crear / Actualizar) ======
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const fd = new FormData(form);
            fd.set('estado', fEstado.checked ? '1' : '0');

            if (editMode) {
                // En edición NO enviar password
                fd.delete('password');
            } else {
                // En creación exigir password
                const pass = (fd.get('password') || '').toString().trim();
                if (!pass) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo requerido',
                        text: 'La contraseña es requerida para crear el usuario.',
                        confirmButtonColor: '#f1c40f'
                    });
                    fPassword?.focus();
                    return;
                }
            }

            try {
                const resp = await fetch('<?= BASE_URL ?>/configuracion/usuarios-guardar', { method: 'POST', body: fd });
                const ctype = resp.headers.get('content-type') || '';

                if (!resp.ok) {
                    const payload = ctype.includes('application/json') ? JSON.stringify(await resp.json(), null, 2) : await resp.text();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error HTTP',
                        html: `<p>Código: ${resp.status}</p><pre style="text-align:left;white-space:pre-wrap">${escapeHtml(payload)}</pre>`,
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                const json = ctype.includes('application/json') ? await resp.json() : { ok: false, message: 'Respuesta no JSON' };
                if (!json.ok) {
                    Swal.fire({ icon: 'error', title: 'Error', text: json.message || 'No se pudo guardar.' });
                    return;
                }

                const u = json.data; // {id, usuario, nombre, rol, estado}

                if (editMode) {
                    // ====== Actualizar fila existente (incluye Acciones) ======
                    const row = document.querySelector(`#tbl-usuarios-simple tbody tr[data-id="${cssEscape(String(u.id))}"]`);
                    if (row) {
                        // celdas 1..4
                        row.querySelector('td:nth-child(1)').textContent = u.nombre;
                        row.querySelector('td:nth-child(2)').textContent = u.usuario;

                        const rolLabel = row.querySelector('td:nth-child(3) .ui.label');
                        if (rolLabel) rolLabel.innerHTML = `<i class="id badge icon"></i> ${escapeHtml(u.rol)}`;

                        const estadoTd = row.querySelector('td:nth-child(4)');
                        estadoTd.innerHTML = Number(u.estado) === 1
                            ? '<div class="ui green tiny label">Activo</div>'
                            : '<div class="ui red tiny label">Inactivo</div>';

                        // Acciones (botón habilitar/inhabilitar + botón editar con datasets frescos)
                        const acciones = row.querySelector('td:nth-child(5) .ui.tiny.buttons');
                        if (acciones) {
                            const userId = String(u.id);
                            const esActivo = Number(u.estado) === 1;

                            acciones.innerHTML = `
                            ${esActivo
                                    ? `<a class="ui orange icon button" href="<?= BASE_URL ?>/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(userId)}"><i class="ban icon"></i></a>`
                                    : `<a class="ui green icon button" href="<?= BASE_URL ?>/configuracion/usuarios-habilitar?id=${encodeURIComponent(userId)}"><i class="check icon"></i></a>`
                                }
                            <a href="#" class="ui blue icon button btn-editar-usuario"
                                data-id="${escapeHtml(userId)}"
                                data-usuario="${escapeHtml(u.usuario)}"
                                data-nombre="${escapeHtml(u.nombre)}"
                                data-rol="${escapeHtml(u.rol)}"
                                data-estado="${esActivo ? 1 : 0}">
                                <i class="edit icon"></i>
                            </a>
                            `;
                        }

                        // Por si conservas el mismo botón editar:
                        const editBtn = row.querySelector('.btn-editar-usuario');
                        if (editBtn) updateEditBtnDataset(editBtn, u);
                    }
                } else {
                    // ====== Insertar nueva fila (arriba) ======
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-id', String(u.id));

                    const esActivo = Number(u.estado) === 1;
                    tr.innerHTML = `
                    <td>${escapeHtml(u.nombre)}</td>
                    <td>${escapeHtml(u.usuario)}</td>
                    <td><div class="ui label"><i class="id badge icon"></i> ${escapeHtml(u.rol)}</div></td>
                    <td>${esActivo ? '<div class="ui green tiny label">Activo</div>' : '<div class="ui red tiny label">Inactivo</div>'}</td>
                    <td>
                        <div class="ui tiny buttons">
                        ${esActivo
                            ? `<a class="ui orange icon button" href="<?= BASE_URL ?>/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(String(u.id))}"><i class="ban icon"></i></a>`
                            : `<a class="ui green icon button" href="<?= BASE_URL ?>/configuracion/usuarios-habilitar?id=${encodeURIComponent(String(u.id))}"><i class="check icon"></i></a>`
                        }
                        <a href="#" class="ui blue icon button btn-editar-usuario"
                            data-id="${escapeHtml(String(u.id))}"
                            data-usuario="${escapeHtml(u.usuario)}"
                            data-nombre="${escapeHtml(u.nombre)}"
                            data-rol="${escapeHtml(u.rol)}"
                            data-estado="${esActivo ? 1 : 0}">
                            <i class="edit icon"></i>
                        </a>
                        </div>
                    </td>
                    `;
                    tbody.prepend(tr);
                }

                resetForm();

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: json.message || (editMode ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente'),
                    confirmButtonColor: '#3085d6'
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un problema en la comunicación con el servidor.',
                    confirmButtonColor: '#d33'
                });
            }
        });

        // Cancelar → volver a “Nuevo”
        btnCancelar?.addEventListener('click', resetForm);

        // Click fuera del formulario → volver a “Nuevo”
        document.addEventListener('click', function (e) {
            const clickedInsideForm = e.target.closest('#frm-nuevo-usuario');
            const clickedEditBtn = e.target.closest('.btn-editar-usuario');
            if (clickedInsideForm || clickedEditBtn) return;
            resetForm();
        });

        // ====== BÚSQUEDA RÁPIDA ======
        const inputBuscar = document.getElementById('buscar-usuarios');
        const btnLimpiar = document.getElementById('btn-limpiar-busqueda');
        const lblResultado = document.getElementById('resultado-busqueda');

        function normaliza(str) {
            return String(str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function filtrarTabla(qRaw) {
            const q = normaliza(qRaw.trim());
            let visibles = 0;

            const buscaActivo = ['activo', 'activos'].includes(q);
            const buscaInactivo = ['inactivo', 'inactivos'].includes(q);

            tbody.querySelectorAll('tr').forEach(tr => {
                const tds = tr.querySelectorAll('td');
                const nombre = normaliza(tds[0]?.innerText || '');
                const usuario = normaliza(tds[1]?.innerText || '');
                const rol = normaliza(tds[2]?.innerText || '');
                const estado = normaliza(tds[3]?.innerText || '');

                let match = true;
                if (q) {
                    if (buscaActivo) match = estado.includes('activo');
                    else if (buscaInactivo) match = estado.includes('inactivo');
                    else {
                        match = (
                            nombre.includes(q) ||
                            usuario.includes(q) ||
                            rol.includes(q) ||
                            estado.includes(q)
                        );
                    }
                }

                tr.style.display = match ? '' : 'none';
                if (match) visibles++;
            });

            const total = tbody.querySelectorAll('tr').length;
            if (!q) {
                lblResultado.textContent = `Mostrando ${visibles} de ${total} usuarios.`;
            } else {
                lblResultado.textContent = `Coincidencias: ${visibles} de ${total}.`;
            }
        }

        let tDebounce;
        inputBuscar?.addEventListener('input', () => {
            clearTimeout(tDebounce);
            tDebounce = setTimeout(() => filtrarTabla(inputBuscar.value), 150);
        });
        btnLimpiar?.addEventListener('click', () => {
            inputBuscar.value = '';
            filtrarTabla('');
            inputBuscar.focus();
        });

        filtrarTabla('');

        // ====== Delegación: click en habilitar/inhabilitar ======
        tbody.addEventListener('click', async function (e) {
            const a = e.target.closest('a');
            if (!a) return;

            const href = a.getAttribute('href') || '';
            const isInhabilitar = href.includes('<?= BASE_URL ?>/configuracion/usuarios-inhabilitar');
            const isHabilitar = href.includes('<?= BASE_URL ?>/configuracion/usuarios-habilitar');
            if (!isInhabilitar && !isHabilitar) return;

            e.preventDefault();

            const row = a.closest('tr');
            const userId = row?.getAttribute('data-id');
            if (!userId) return;

            const { isConfirmed } = await Swal.fire({
                icon: 'question',
                title: isInhabilitar ? 'Inhabilitar usuario' : 'Habilitar usuario',
                text: isInhabilitar ? 'El usuario no podrá acceder al sistema. ¿Continuar?' : 'El usuario podrá acceder al sistema. ¿Continuar?',
                showCancelButton: true,
                confirmButtonText: isInhabilitar ? 'Sí, inhabilitar' : 'Sí, habilitar',
                cancelButtonText: 'Cancelar'
            });
            if (!isConfirmed) return;

            try {
                const url = isInhabilitar
                    ? `<?= BASE_URL ?>/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(userId)}`
                    : `<?= BASE_URL ?>/configuracion/usuarios-habilitar?id=${encodeURIComponent(userId)}`;

                const resp = await fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                const ctype = resp.headers.get('content-type') || '';
                if (!resp.ok) {
                    const payload = ctype.includes('application/json') ? JSON.stringify(await resp.json(), null, 2) : await resp.text();
                    Swal.fire({ icon: 'error', title: 'Error', html: `<pre style="text-align:left;white-space:pre-wrap">${escapeHtml(payload)}</pre>` });
                    return;
                }
                if (!ctype.includes('application/json')) {
                    Swal.fire({ icon: 'error', title: 'Respuesta inesperada', text: 'El servidor no devolvió JSON.' });
                    return;
                }

                const json = await resp.json(); // { ok:bool, estado: 0|1, message?:string }
                if (!json.ok) {
                    Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: json.message || '' });
                    return;
                }

                // Actualizar UI (estado + acciones + datasets del botón editar)
                const nuevoEstado = Number(json.estado);
                row.querySelector('td:nth-child(4)').innerHTML = nuevoEstado === 1
                    ? '<div class="ui green tiny label">Activo</div>'
                    : '<div class="ui red tiny label">Inactivo</div>';

                const accionesTd = row.querySelector('td:nth-child(5) .ui.tiny.buttons');
                const rolText = (row.querySelector('td:nth-child(3) .ui.label')?.textContent || '').trim(); // mantener rol visible
                const nombreText = (row.querySelector('td:nth-child(1)')?.textContent || '').trim();
                const usuarioText = (row.querySelector('td:nth-child(2)')?.textContent || '').trim();

                accionesTd.innerHTML = `
        ${nuevoEstado === 1
                        ? `<a class="ui orange icon button" href="<?= BASE_URL ?>/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(userId)}"><i class="ban icon"></i></a>`
                        : `<a class="ui green icon button" href="<?= BASE_URL ?>/configuracion/usuarios-habilitar?id=${encodeURIComponent(userId)}"><i class="check icon"></i></a>`
                    }
                        <a href="#" class="ui blue icon button btn-editar-usuario"
                        data-id="${escapeHtml(userId)}"
                        data-usuario="${escapeHtml(usuarioText)}"
                        data-nombre="${escapeHtml(nombreText)}"
                        data-rol="${escapeHtml(rolText)}"
                        data-estado="${nuevoEstado}">
                        <i class="edit icon"></i>
                        </a>
                    `;

                Swal.fire({
                    icon: 'success',
                    title: 'Hecho',
                    text: json.message || (nuevoEstado === 1 ? 'Usuario habilitado' : 'Usuario inhabilitado')
                });
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo completar la acción.' });
            }
        });

        // Inicia en modo crear
        goCreateMode();
    });
</script>