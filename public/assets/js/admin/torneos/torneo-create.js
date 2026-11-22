let paso = 0;
window.wizardTieneTorneo = false;

document.getElementById('btnSalirWizard').onclick = () => {
    let titulo = "¿Deseas salir del asistente?";
    let texto = '';
    let icono = 'warning';
    if (paso<=1){
        if(window.wizardTieneTorneo){
            texto = "Si sales ahora el torneo se conservará en estado planeado y podrás retomarlo mas tarde o eliminarlo.";
        }else
          texto = "Saldrás del asistente. Aún no se iniciado ningún torneo."
    }else{
        texto = "Si sales ahora el torneo se conservará en estado planeado y podrás retomarlo mas tarde o eliminarlo.";
    }
    Swal.fire({
        title: titulo,
        text: texto,
        icon: icono,
        showCancelButton: true,
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php?page=torneos';
        }
    });
};

document.getElementById('btnVerResumen').addEventListener('click', () => {
    document.getElementById('res_nombre').innerText =
    document.getElementById('nombre_torneo').value;

    document.getElementById('res_tipo').innerText =
    document.getElementById('tipo_torneo').value;

    document.getElementById('res_liga').innerText =
    document.getElementById('liga_id').selectedOptions[0]?.text || '—';

    document.getElementById('res_temporada').innerText =
    document.getElementById('temporada_id').selectedOptions[0]?.text || '—';

    document.getElementById('res_inscripcion').innerText =
    document.getElementById('inscripcion').value || '0';

    document.getElementById('res_fianza').innerText =
    document.getElementById('fianza').value || '0';

    document.getElementById('res_pago').innerText =
    document.getElementById('requiere_pagos').checked ? 'Sí' : 'No';

    document.getElementById('res_modalidad').innerText =
    document.getElementById('modalidad').value;

    document.getElementById('res_liguilla').innerText =
    document.getElementById('tiene_liguilla').checked ? 'Sí' : 'No';

    new bootstrap.Modal(
        document.getElementById('modalResumenTorneo')
    ).show();
});

function mostrarPaso() {
    const steps = document.querySelectorAll('.wizard-step');

    steps.forEach(div => {
        div.classList.add('d-none');
        div.classList.remove('wizard-animado');
    });

    const pasoActual = document.querySelector(`.wizard-step[data-step="${paso}"]`);
    pasoActual.classList.remove('d-none');

    const stepHeader = paso === 0 ? 0 : paso;
    document.getElementById('stepActual').innerText = stepHeader;

    if (paso === 0) {
        setTimeout(() => {
            pasoActual.classList.add('wizard-animado');
        }, 50);
    }

    document.getElementById('btnAnterior').style.display = paso <= 1 ? 'none' : 'inline-block';
    document.getElementById('btnSiguiente').style.display = paso === 0 ? 'none' : 'inline-block';
    document.getElementById('btnSiguiente').innerText = paso === 6 ? 'Guardar' : 'Siguiente';
}

function guardarStep1() {
    const data = new FormData();
    data.append('nombre_torneo', document.getElementById('nombre_torneo').value);
    data.append('tipo_torneo', document.getElementById('tipo_torneo').value);
    data.append('liga_id', document.getElementById('liga_id').value);
    data.append('temporada_id', document.getElementById('temporada_id').value);

    fetch('index.php?page=torneos&action=guardarStep1', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            paso = 2;
            window.wizardTieneTorneo = true;
            mostrarPaso();
        } else {
            alert('Error al guardar torneo');
        }
    })
    .catch(() => alert('Error de conexión'));
}

function guardarStep2(){
    const data = new FormData();
    const requierePagos = document.getElementById('requiere_pagos');
    const permiteInvitados = document.getElementById('permiteInvitados');

    data.append('inscripcion', document.getElementById('inscripcion').value);
    data.append('fianza', document.getElementById('fianza').value);
    data.append('moneda', document.getElementById('moneda').value);

    if (requierePagos && requierePagos.checked) {
        data.append('requiere_pagos', 1);
    }

    if (permiteInvitados && permiteInvitados.checked) {
        data.append('permite_invitados', 1);
    }

    fetch('index.php?page=torneos&action=guardarStep2', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        console.log('RESPUESTA STEP 2:', res);
        if (res.success) {
            paso = 3;
            mostrarPaso();
        } else {
            alert('Error al guardar el paso 2');
        }
    })
    .catch(err => {
        console.log(err);
        alert('Error de conexión');
    });
}

function guardarStep3()
{
    const data = new FormData();
    data.append('modalidad', document.getElementById('modalidad').value);
    if(document.getElementById('tiene_liguilla').checked){
        data.append('tiene_liguilla', 1);
    }
    if(document.getElementById('segunda_vuelta_puntos').checked){
        data.append('segunda_vuelta_puntos', 1);
    }
    if(document.getElementById('permite_inconcluso').checked){
        data.append('permite_inconcluso',1);
    }
    fetch('index.php?page=torneos&action=guardarStep3', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            paso = 4;
            mostrarPaso();
        } else {
            alert(res.message || 'Error al guardar formato deportivo');
        }
    })
    .catch(() => alert('Error de conexión'));
}

function guardarStep4()
{
    const tipoBloques = document.querySelector(
        'input[name="tipo_bloques"]:checked'
    )?.value;

    const data = new FormData();
    data.append('tipo_bloques', tipoBloques);
    data.append(
        'comparte_bloques',
        document.getElementById('comparte_bloques').checked ? 1 : 0
    );

    fetch('index.php?page=torneos&action=guardarStep4', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            paso = 5;
            mostrarPaso();
        } else {
            alert(res.message || 'Error al guardar Step 4');
        }
    })
    .catch(() => alert('Error de conexión'));
}

document.getElementById('btnSiguiente').onclick = () => {
    if (paso === 1) {
        guardarStep1();
        return;
    }

    if(paso === 2){
        guardarStep2();
        return;
    }

    if(paso == 3){
        guardarStep3();
        return;
    }

    if (paso < 6) paso++;
    mostrarPaso();
};

document.getElementById('btnAnterior').onclick = () => {
    if (paso > 1) paso--;
    mostrarPaso();
};

document.getElementById('btnComenzar')?.addEventListener('click', () => {
    paso = 1;
    mostrarPaso();
});

mostrarPaso();
