(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        let cerrarForzado = false;
        const modal = document.getElementById('modalEquipos');
        const btnsAbrirEquipos = document.querySelectorAll('.btnAbrirEquipos');
        const checks = document.querySelectorAll('.equipo-check');
        const contador = document.getElementById('contadorSeleccionados');
        const btnGuardar = document.getElementById('btnGuardarEquipos');
        const badgeCambios = document.getElementById('badgeCambios');
        const btnRestaurar = document.getElementById('btnRestaurarCambios');
        const btnToggle = document.getElementById('btnToggleEquipos');
        const buscador = document.getElementById('buscadorEquipos');
        if (modal) modal.addEventListener('hide.bs.modal', function (event) {
            if (!hayCambios() || cerrarForzado) { cerrarForzado = false; return; }
            event.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Cambios sin guardar', text: 'Si sales en este momento, los cambios realizados no se guardaran', showCancelButton: true, cancelButtonText: 'Regresar y seguir editando', confirmButtonText: 'Salir sin guardar', confirmButtonColor: '#1E40AF' }).then(function (result) {
                if (result.isConfirmed) {
                    restaurarEstadoOriginal(); cerrarForzado = true;
                    const instance = bootstrap.Modal.getInstance(modal);
                    if (instance) instance.hide();
                }
            });
        });
        if (btnsAbrirEquipos.length && modal) btnsAbrirEquipos.forEach(function (button) {
            button.addEventListener('click', function () {
                bootstrap.Modal.getOrCreateInstance(modal).show();
                refrescarTodo();
            });
        });
        checks.forEach(function (check) {
            check.addEventListener('change', function () { actualizarEstadoVisual(this); refrescarTodo(); });
            actualizarEstadoVisual(check);
        });
        if (btnToggle) btnToggle.addEventListener('click', function () {
            const total = checks.length;
            const selected = Array.from(checks).filter(function (check) { return check.checked; }).length;
            const marcar = selected < total;
            checks.forEach(function (check) { check.checked = marcar; actualizarEstadoVisual(check); });
            refrescarTodo();
        });
        if (btnRestaurar) btnRestaurar.addEventListener('click', restaurarEstadoOriginal);
        if (buscador) buscador.addEventListener('keyup', function () {
            const texto = this.value.toLowerCase();
            document.querySelectorAll('.equipo-item').forEach(function (item) {
                item.style.display = item.innerText.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
        if (btnGuardar) btnGuardar.addEventListener('click', function () {
            const equipos = Array.from(document.querySelectorAll('.equipo-check:checked')).map(function (check) { return check.value; });
            if (!equipos.length) { Swal.fire('Atención', 'Selecciona al menos un equipo.', 'warning'); return; }
            const container = document.querySelector('.torneo-detalle-page');
            const torneoId = container ? container.dataset.torneoId : '';
            if (!torneoId) { Swal.fire('Error', 'No se identificó el torneo.', 'error'); return; }
            const data = new FormData(); data.append('torneo_id', torneoId);
            equipos.forEach(function (id) { data.append('equipos[]', id); });
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            fetch('index.php?page=torneos&action=asignarEquipos', { method: 'POST', body: data })
                .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
                .then(function (response) {
                    if (response.success) Swal.fire({ icon: 'success', title: 'Equipos guardados', timer: 1500, showConfirmButton: false }).then(function () { location.reload(); });
                    else Swal.fire('Error', response.msg || 'Error al guardar', 'error');
                })
                .catch(function () { Swal.fire('Error', 'Error de conexión', 'error'); })
                .finally(function () { btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar cambios'; refrescarTodo(); });
        });
        const formBloque = document.getElementById('formBloque');
        if (formBloque) formBloque.addEventListener('submit', function (event) {
            event.preventDefault();
            const form = this; const data = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...'; }
            fetch('index.php?page=torneos&action=guardarBloque', { method: 'POST', body: data })
                .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
                .then(function (response) {
                    if (response.success) Swal.fire({ icon: 'success', title: 'Bloque guardado', timer: 1300, showConfirmButton: false }).then(function () { location.reload(); });
                    else Swal.fire('Error', response.msg || 'No se pudo guardar', 'error');
                })
                .catch(function () { Swal.fire('Error', 'Error de conexión', 'error'); })
                .finally(function () { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Guardar bloque'; } });
        });
        function refrescarTodo() { actualizarContador(); actualizarBotonGuardar(); actualizarTextoToggle(); }
        function actualizarContador() { if (contador) contador.innerText = document.querySelectorAll('.equipo-check:checked').length; }
        function hayCambios() { return Array.from(checks).some(function (check) { return check.checked !== (check.dataset.original === '1'); }); }
        function actualizarBotonGuardar() {
            const cambios = hayCambios();
            if (badgeCambios) badgeCambios.classList.toggle('d-none', !cambios);
            if (btnRestaurar) btnRestaurar.classList.toggle('d-none', !cambios);
            if (btnGuardar) btnGuardar.disabled = !cambios;
        }
        function actualizarTextoToggle() {
            if (!btnToggle) return;
            const total = checks.length;
            const seleccionados = Array.from(checks).filter(function (check) { return check.checked; }).length;
            btnToggle.innerText = seleccionados === total ? 'Deseleccionar todos' : 'Seleccionar todos';
        }
        function actualizarEstadoVisual(check) {
            const card = check.closest('.equipo-card'); const item = check.closest('.equipo-item');
            const badge = item ? item.querySelector('.estado-equipo') : null;
            if (!card || !item || !badge) return;
            const original = check.dataset.original === '1'; const actual = check.checked;
            card.classList.remove('seleccionado', 'pendiente-alta', 'pendiente-baja'); item.classList.remove('inscrito');
            if (!original && !actual) { badge.className = 'badge estado-equipo bg-secondary'; badge.innerText = 'Disponible'; }
            if (original && actual) { card.classList.add('seleccionado'); item.classList.add('inscrito'); badge.className = 'badge estado-equipo bg-success'; badge.innerText = 'Inscrito'; }
            if (!original && actual) { card.classList.add('pendiente-alta'); badge.className = 'badge estado-equipo bg-warning text-dark'; badge.innerText = 'Se inscribirá'; }
            if (original && !actual) { card.classList.add('pendiente-baja'); badge.className = 'badge estado-equipo bg-danger'; badge.innerText = 'Se eliminará'; }
        }
        function restaurarEstadoOriginal() { checks.forEach(function (check) { check.checked = check.dataset.original === '1'; actualizarEstadoVisual(check); }); refrescarTodo(); }
        refrescarTodo();
    });
})();