(function () {
    'use strict';
    const preview = document.getElementById('escudoPreview');
    const overlay = document.querySelector('.escudo-overlay');
    const input = document.getElementById('inputEscudo');
    const actions = document.querySelector('.escudo-actions');
    const btnCancel = document.getElementById('btnCancelarEscudo');
    const btnConfirm = document.getElementById('btnConfirmarEscudo');
    let originalShield = preview ? preview.src : '';
    let selectedShield = null;
    let shieldLocked = false;
    if (overlay && input && preview && actions) {
        overlay.addEventListener('click', function () { if (!shieldLocked) input.click(); });
        input.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            shieldLocked = true; selectedShield = file;
            const reader = new FileReader();
            reader.onload = function (loadEvent) {
                preview.src = loadEvent.target.result;
                preview.classList.add('animate__animated', 'animate__pulse');
                actions.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });
    }
    if (btnCancel && preview && input && actions) {
        btnCancel.addEventListener('click', function () {
            preview.src = originalShield; actions.classList.add('d-none'); input.value = '';
            selectedShield = null; shieldLocked = false;
        });
    }
    if (btnConfirm && preview && actions) {
        btnConfirm.addEventListener('click', function () {
            if (!selectedShield) return;
            if (typeof Swal === 'undefined') return;
            Swal.fire({ title: '¿Guardar nuevo escudo?', text: 'El escudo del equipo sera actualizado', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, guardar', cancelButtonText: 'Cancelar', confirmButtonColor: '#0d6efd' }).then(function (result) {
                if (!result.isConfirmed) return;
                const teamId = document.querySelector('.equipos-detalle-page')?.dataset.equipoId;
                if (!teamId) { Swal.fire({ icon: 'error', title: 'Error', text: 'No se identificó el equipo.' }); return; }
                const data = new FormData(); data.append('equipo_id', teamId); data.append('escudo', selectedShield);
                Swal.fire({ title: 'Guardando escudo...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
                fetch('index.php?page=equipos&action=guardarEscudo', { method: 'POST', body: data })
                    .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
                    .then(function (response) {
                        if (!response.success) { Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo guardar el escudo' }); return; }
                        originalShield = preview.src; selectedShield = null; shieldLocked = false; actions.classList.add('d-none');
                        Swal.fire({ icon: 'success', title: 'Escudo actualizado', text: 'El escudo se guardó correctamente', timer: 1800, showConfirmButton: false });
                    })
                    .catch(function () { Swal.fire({ icon: 'error', title: 'Error de comunicación', text: 'No se pudo contactar al servidor' }); });
            });
        });
    }
    const btnNewAffiliation = document.getElementById('btnNuevaAfiliacion');
    const searchInput = document.getElementById('inputBusqueda');
    if (btnNewAffiliation) btnNewAffiliation.addEventListener('click', function () {
        const modalElement = document.getElementById('modalBuscarAfiliado');
        if (modalElement && typeof bootstrap !== 'undefined') bootstrap.Modal.getOrCreateInstance(modalElement).show();
    });
    if (searchInput) searchInput.addEventListener('keyup', function () {
        const value = this.value; if (value.length < 5) return;
        fetch('index.php?page=afiliaciones&action=buscar&term=' + encodeURIComponent(value))
            .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
            .then(function (response) {
                const container = document.getElementById('resultadoBusqueda'); if (!container) return;
                if (!response.encontrado) {
                    container.innerHTML = '<div class="alert alert-warning">No existe afiliado<br><button class="btn btn-sm btn-primary mt-2" onclick="crearNuevo(\'' + value + '\')">Crear nuevo</button></div>'; return;
                }
                if (response.ya_afiliado) { container.innerHTML = '<div class="alert alert-danger">Ya está afiliado en esta temporada</div>'; return; }
                container.innerHTML = '<div class="card p-2"><strong>' + response.nombre + '</strong><br><small>' + response.curp + '</small><button class="btn btn-success btn-sm mt-2" onclick="afiliar(' + response.id + ')">Afiliar a equipo</button></div>';
            }).catch(function (error) { console.error('Error al buscar afiliado:', error); });
    });
})();