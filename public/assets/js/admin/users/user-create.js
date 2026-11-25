document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formNewUser');
    const role = document.getElementById('rol_id');
    const orgBox = document.getElementById('boxOrganismo');
    const org = document.getElementById('organismo_id');
    const leagueBox = document.getElementById('boxLiga');
    const teamBox = document.getElementById('boxEquipo');
    const league = document.getElementById('liga_id');
    const team = document.getElementById('equipo_id');

    const roleKey = () => role?.selectedOptions[0]?.dataset.roleKey || '';
    const orgId = () => org?.value || '';

    const fill = (select, items, placeholder) => {
        if (!select) return;
        select.innerHTML = '';
        const first = document.createElement('option');
        first.value = '';
        first.textContent = placeholder;
        select.appendChild(first);
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nombre;
            select.appendChild(option);
        });
    };

    const loadLeagues = async () => {
        if (!league) return;
        fill(team, [], 'Selecciona un equipo');
        if (!orgId()) {
            fill(league, [], 'Selecciona primero un organismo');
            return;
        }
        fill(league, [], 'Cargando ligas...');
        const params = new URLSearchParams({page: 'ajax', action: 'ligas', organismo_id: orgId()});
        const response = await fetch('index.php?' + params.toString(), {headers: {'Accept': 'application/json'}});
        const items = await response.json();
        if (!response.ok || !Array.isArray(items)) throw new Error(items.mensaje || 'No fue posible cargar las ligas.');
        fill(league, items, 'Selecciona una liga');
    };

    const loadTeams = async () => {
        if (!team) return;
        fill(team, [], 'Selecciona un equipo');
        if (!league?.value) return;
        const params = new URLSearchParams({page: 'ajax', action: 'equipos', liga_id: league.value});
        if (orgId()) params.set('organismo_id', orgId());
        const response = await fetch('index.php?' + params.toString(), {headers: {'Accept': 'application/json'}});
        const items = await response.json();
        if (!response.ok || !Array.isArray(items)) throw new Error(items.mensaje || 'No fue posible cargar los equipos.');
        fill(team, items, 'Selecciona un equipo');
    };

    const sync = async () => {
        const key = roleKey();
        const needsOrg = ['ORGANISMO', 'LIGA', 'EQUIPO'].includes(key);
        const needsLeague = ['LIGA', 'EQUIPO'].includes(key);
        if (orgBox) orgBox.classList.toggle('d-none', !needsOrg);
        if (leagueBox) leagueBox.classList.toggle('d-none', !needsLeague);
        if (teamBox) teamBox.classList.toggle('d-none', key !== 'EQUIPO');
        if (org && org.tagName === 'SELECT') {
            org.required = needsOrg;
            const independentOption = org.querySelector('option[value="0"]');
            if (independentOption) independentOption.hidden = key === 'ORGANISMO';
            if (key === 'ORGANISMO' && org.value === '0') org.value = '';
        }
        if (league) league.required = needsLeague;
        if (team) team.required = key === 'EQUIPO';
        if (!needsOrg && org && org.tagName === 'SELECT') org.value = '';
        if (league) league.value = '';
        if (team) team.value = '';
        if (needsLeague) await loadLeagues();
    };

    role?.addEventListener('change', () => sync().catch(showError));
    org?.addEventListener('change', () => {
        if (league) league.value = '';
        if (team) team.value = '';
        if (['LIGA', 'EQUIPO'].includes(roleKey())) loadLeagues().catch(showError);
    });
    league?.addEventListener('change', () => loadTeams().catch(showError));

    function showError(error) {
        if (window.Swal) Swal.fire({icon: 'error', title: 'Error', text: error.message});
        else alert(error.message);
    }

    form?.addEventListener('submit', event => {
        event.preventDefault();
        const send = () => fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {'Accept': 'application/json'}
        }).then(async response => {
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.mensaje || 'No fue posible crear el usuario.');
            if (window.Swal) {
                await Swal.fire({icon: 'success', title: 'Usuario creado', text: result.mensaje});
            } else {
                alert(result.mensaje);
            }
            location.href = 'index.php?page=users';
        }).catch(showError);

        if (window.Swal) {
            Swal.fire({
                title: 'Crear cuenta',
                text: '¿Deseas guardar este usuario?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Crear usuario',
                cancelButtonText: 'Cancelar'
            }).then(result => { if (result.isConfirmed) send(); });
        } else if (confirm('¿Deseas crear este usuario?')) {
            send();
        }
    });

    sync().catch(showError);
});