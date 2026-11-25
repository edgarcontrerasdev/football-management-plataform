document.addEventListener('change', event => {
    if (!event.target.classList.contains('toggle-user')) return;
    const checkbox = event.target;
    const id = checkbox.dataset.id;
    const active = checkbox.checked;
    const badge = document.getElementById('status-' + id);
    fetch('index.php?page=users&action=toggleState', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({id, estado: active ? '1' : '0'})
    }).then(response => response.json()).then(result => {
        if (!result.success) throw new Error('No fue posible cambiar el estado.');
        if (badge) {
            badge.textContent = active ? 'ACTIVO' : 'INACTIVO';
            badge.classList.toggle('active', active);
            badge.classList.toggle('inactive', !active);
        }
    }).catch(() => {
        checkbox.checked = !active;
        alert('No fue posible cambiar el estado del usuario.');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('loader-overlay');
    const tbody = document.getElementById('tabla-body');
    const pagina = document.getElementById('pagina');
    const limite = document.querySelector('#paginacion select#limite');
    const filtro = document.getElementById('inputFiltro');
    const estado = document.getElementById('filtroEstado');
    const rol = document.getElementById('filtroRol');
    const liga = document.getElementById('filtroLiga');
    if (!tbody || !pagina || !limite) return;

    const escapeHTML = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));

    const renderPagination = (total, current, pageSize) => {
        const info = document.querySelector('#paginacion .info-registros');
        const center = document.querySelector('#paginacion .paginacion-center');
        const pages = Math.ceil(total / pageSize);
        if (info) info.textContent = total ? `Mostrando ${(current - 1) * pageSize + 1} a ${Math.min(current * pageSize, total)} de ${total}` : 'Mostrando 0 resultados';
        if (!center) return;
        center.innerHTML = '';
        if (pages <= 1) return;
        const add = (label, target, disabled = false, active = false) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.className = 'btn btn-nav' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
            button.disabled = disabled;
            button.addEventListener('click', () => load(target));
            center.appendChild(button);
        };
        add('« Primera', 1, current === 1);
        add('‹ Anterior', current - 1, current === 1);
        for (let page = Math.max(1, current - 2); page <= Math.min(pages, current + 2); page++) add(String(page), page, false, page === current);
        add('Siguiente ›', current + 1, current === pages);
        add('Última »', pages, current === pages);
    };

    const load = page => {
        pagina.value = String(page);
        if (loader) loader.style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Cargando usuarios…</td></tr>';
        const params = new URLSearchParams({
            filtro: filtro?.value.trim() || '',
            estado: estado?.value || '',
            rol: rol?.value || '',
            liga: liga?.value || '',
            pagina: pagina.value,
            limite: limite.value || '10'
        });
        fetch('index.php?page=ajax&action=usersFilter&' + params.toString(), {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin'
        })
            .then(async response => {
                const raw = await response.text();
                let data;
                try {
                    data = JSON.parse(raw);
                } catch (error) {
                    throw new Error(`El servidor respondió con HTTP ${response.status} y no devolvió JSON: ${raw.slice(0, 350)}`);
                }
                if (!response.ok || data.success === false) {
                    throw new Error(data.mensaje || `Error HTTP ${response.status}`);
                }
                return data;
            })
            .then(data => {
                const result = data.usuarios || {};
                const users = Array.isArray(result) ? result : (result.usuarios || []);
                const total = Number(data.total ?? result.total ?? users.length);
                if (!users.length) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No se encontraron usuarios.</td></tr>';
                    renderPagination(0, 1, Number(limite.value));
                    return;
                }
                tbody.innerHTML = users.map(user => {
                    const id = Number(user.id);
                    const active = user.estado === 'activo';
                    const avatarName = user.avatar ? String(user.avatar).split('/').pop() : 'default.png';
                    const avatar = '/afec/public/assets/img/avatars/' + encodeURIComponent(avatarName);
                    const managerRole = tbody.dataset.managerRole || '';
                    const canManage = managerRole === 'ADMIN' || (managerRole === 'ORGANISMO' && ['LIGA', 'EQUIPO'].includes(String(user.rol_clave || '').toUpperCase()));
                    const actions = canManage
                        ? `<label class="switch"><input type="checkbox" class="toggle-user" data-id="${id}" ${active ? 'checked' : ''}><span class="slider"></span></label><a href="index.php?page=users&action=edit&id=${id}" class="btn-icon edit" title="Editar usuario"><i class="fa-solid fa-pen"></i></a>`
                        : '<span class="text-muted small">Cuenta de administración</span>';
                    return `<tr>
                        <td><div class="user-cell"><img class="user-avatar" src="${avatar}" alt="Avatar"><div class="user-info"><span class="user-name">${escapeHTML(user.nombre)}</span><span class="user-alias">@${escapeHTML(user.usuario)}</span></div></div></td>
                        <td>${escapeHTML(user.rol_nombre || 'Sin rol')}</td>
                        <td>${escapeHTML(user.organismo || '-')}</td>
                        <td><div>@${escapeHTML(user.usuario)}</div><small class="text-muted">ID ${id}</small></td>
                        <td>${escapeHTML(user.liga || '-')}</td>
                        <td>${escapeHTML(user.equipo || '-')}</td>
                        <td class="text-center"><span id="status-${id}" class="user-status ${active ? 'active' : 'inactive'}">${active ? 'ACTIVO' : 'INACTIVO'}</span></td>
                        <td class="text-center">${actions}</td>
                    </tr>`;
                }).join('');
                renderPagination(total, Number(pagina.value), Number(limite.value));
            })
            .catch(error => {
                console.error('Error al cargar usuarios:', error);
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error al cargar usuarios: ${escapeHTML(error.message)}</td></tr>`;
            })
            .finally(() => { if (loader) loader.style.display = 'none'; });
    };

    let debounce;
    filtro?.addEventListener('input', () => { clearTimeout(debounce); debounce = setTimeout(() => load(1), 250); });
    [estado, rol, liga, limite].forEach(control => control?.addEventListener('change', () => load(1)));
    load(1);
});
