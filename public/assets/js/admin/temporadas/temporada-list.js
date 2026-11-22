document.addEventListener('DOMContentLoaded',function(e){
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');

        popoverTriggerList.forEach(function (el) {
            new bootstrap.Popover(el);
        });
    });

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-cerrar');
        
        if(!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Deseas cerrar definitivamente la temporada?',
            html: `
                    <p class="mb-3">
                        La temporada cambiará al estado
                        <strong>Cerrada</strong>.
                    </p>
                    <div class="alert alert-warning text-start mb-0">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        Antes de continuar, confirma que:
                        <ul class="mt-2 mb-0">
                            <li>Todos los torneos han concluido.</li>
                            <li>No existen partidos pendientes.</li>
                            <li>Las estadísticas y sanciones están actualizadas.</li>
                            <li>La temporada está lista para archivarse.</li>
                        </ul>
                    </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Sí, finalizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });

    });


    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-activar');

        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Activar temporada?',
            html: 'Al activar esta temporada, la temporada activa actual pasará a estado <b>Finalizada</b>.',
            showCancelButton: true,
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete');

        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('href');

        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar temporada?',
            html: 'Al eliminar se perderan los datos previamente guardados.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-finalizar');

        if(!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('href');
        Swal.fire({
            icon:               'warning',
            title:              '¿Finalizar temporada?',
            html:               'Al finalizar la temporada pasara de <b>ACTIVA</b> </br> a estado <b>FINALIZADO</> lo cual permitirá concluir procesos pendientes pero ya no permitirá </br> crear nuevos torneos ni afiliar a esta temporada.',
            showCancelbutton:   'true',
            confirmButtonText:  'Sí, finalizar',
            cancelButtonText:   'Cancelar',
            confirmButtonColor: '#0033ff'
        }).then((result) =>{
            if(result.isConfirmed){
                window.location.href = url;
            }
        });
    });
