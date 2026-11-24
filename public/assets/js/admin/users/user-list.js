// JavaScript del listado y administración de usuarios.
// Se conserva el comportamiento existente de la vista.

document.addEventListener('change', function (e) {

        if (!e.target.classList.contains('toggle-user')) return;

        const checkbox = e.target;
        const id = checkbox.dataset.id;
        const estado = checkbox.checked ? 1 : 0;
        const badge = document.querySelector(`#status-${id}`);


        fetch('index.php?page=users&action=toggleState', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&estado=${estado}`
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) {
                checkbox.checked = !checkbox.checked;
                alert('Error al cambiar estado');
            }
            else{
                if (estado == 1){
                    badge.textContent = 'ACTIVO';
                    badge.classList.remove('inactive');
                    badge.classList.add('active');
                }
                else{
                    badge.textContent = 'INACTIVO';
                    badge.classList.remove('active');
                    badge.classList.add('inactive');
                }
            }
        })
        .catch(() => {
            checkbox.checked = !checkbox.checked;
            alert('Error de conexión');
        });

    });

document.addEventListener('DOMContentLoaded', () => 
    {
        const loader = document.getElementById('loader-overlay');
        const tbody = document.getElementById('tabla-body');
        const pagina = document.getElementById('pagina');
        const limite = document.querySelector('#paginacion select#limite');
        const paginacion = document.getElementById('paginacion');
        const inputFiltro = document.getElementById("inputFiltro");
        const filtroEstado = document.getElementById("filtroEstado");
        const filtroRol = document.getElementById("filtroRol");
        const filtroLiga = document.getElementById("filtroLiga");

        let timer = null;

        const escapeHTML = value => String(value ?? '').replace(/[&<>\"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',\"'\":'&#39;'}[char]));\n\n        function mostrarSkeleton(filas = 5, columnas = 7){
        tbody.innerHTML = '';
        for(let i=0;i<filas;i++){
            const tr = document.createElement('tr');
            for(let j=0;j<columnas;j++){
                const td = document.createElement('td');
                td.innerHTML = `<div class="skeleton"> </div>`;
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }
        }

        function renderPaginacion(totalRegistros, paginaActual, limite)
        {
            const info = document.querySelector('#paginacion .info-registros');
            const center = document.querySelector('#paginacion .paginacion-center');
            const totalPaginas = Math.ceil(totalRegistros / limite);

            info.textContent = `Mostrando ${(paginaActual-1)*limite+1} a ${Math.min(paginaActual*limite,totalRegistros)} de ${totalRegistros}`;
            center.innerHTML = '';

            if(totalPaginas<=1) return;

            const crearBoton = (text, action, disabled=false, active=false)=>{
            const btn = document.createElement('button');
            btn.textContent = text;
            btn.onclick = action;
            btn.classList.add('btn','btn-nav');
            if(disabled) btn.classList.add('disabled');
            if(active) btn.classList.add('active');
            return btn;
            }

            center.appendChild(crearBoton('« Primera', ()=> irPagina(1), paginaActual===1));
            center.appendChild(crearBoton('‹ Anterior', ()=> irPagina(paginaActual-1), paginaActual===1));

            const rango=2;
            for(let i=Math.max(1,paginaActual-rango); i<=Math.min(totalPaginas,paginaActual+rango); i++){
            center.appendChild(crearBoton(i, ()=> irPagina(i), false, i===paginaActual));
            }

            center.appendChild(crearBoton('Siguiente ›', ()=> irPagina(paginaActual+1), paginaActual===totalPaginas));
            center.appendChild(crearBoton('Última »', ()=> irPagina(totalPaginas), paginaActual===totalPaginas));
        }

        const aplicarFiltros = ()=>
        {
        clearTimeout(timer);
        loader.style.display = 'block';   // 🔹 mostrar overlay
        mostrarSkeleton();

        timer = setTimeout(()=>
        {
            const params = new URLSearchParams({
                filtro: inputFiltro?.value.trim() || '',
                estado: filtroEstado?.value || '',
                rol: filtroRol?.value || '',
                liga: filtroLiga?.value || '',
                pagina: pagina?.value || '1',
                limite: limite?.value || '10'
            });

            fetch(`index.php?page=ajax&action=usersFilter&${params.toString()}`)
            .then(res => res.json())
            .then(data=>
            {
                const usuarios = data.usuarios?.usuarios || [];
                const total = data.usuarios?.total || 0;

                tbody.innerHTML = '';       // limpiar skeleton
                loader.style.display = 'none';  // ocultar overlay

                if(!usuarios.length){
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No se encontraron resultados</td></tr>`;
                    
                    const info = document.querySelector('#paginacion .info-registros');
                    const center = document.querySelector('#paginacion .paginacion-center');

                    if (info) info.textContent = 'Mostrando 0 resultados';
                    if (center) center.innerHTML = '';
                    
                    return;
                }

               tbody.innerHTML = '';

                usuarios.forEach(u => {

                    const avatar = u.avatar ? `/afec/public/assets/img/avatars/${u.avatar}` : `/afec/public/assets/img/avatars/default.png`;

                    tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="user-cell">
                                <img class="user-avatar" src="${avatar}" alt="Avatar">
                                <div class="user-info">
                                    <span class="user-name">${u.nombre}</span>
                                    <span class="user-alias">@${u.usuario}</span>
                                </div>
                            </div>
                        </td>
                        <td>${escapeHTML(u.rol_nombre ?? 'Sin rol')}</td>
                        <td>
                            <div>${u.correo}</div>
                            <small class="text-muted">${u.telefono ?? ''}</small>
                        </td>

                        <td>${escapeHTML(u.liga ?? '-')}</td>
                        <td>${escapeHTML(u.equipo ?? '-')}</td>

                        <td class="text-center">
                            <span id="status-${u.id}" 
                                class="user-status ${u.estado === 'activo' ? 'active' : 'inactive'}">
                                ${u.estado === 'activo' ? 'ACTIVO' : 'INACTIVO'}
                            </span>
                        </td>

                        <td class="text-center">
                            <label class="switch">
                                <input type="checkbox"
                                    class="toggle-user"
                                    data-id="${u.id}"
                                    ${u.estado === 'activo' ? 'checked' : ''}>
                                <span class="slider"></span>
                            </label>

                            <a href="index.php?page=users&action=edit&id=${u.id}" 
                            class="btn-icon edit"
                            title="Editar usuario">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    width="16" height="16" 
                                    viewBox="0 0 24 24" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    stroke-width="2" 
                                    stroke-linecap="round" 
                                    stroke-linejoin="round">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    `;
                });

                renderPaginacion(total, parseInt(pagina.value), parseInt(limite.value));
            })
            .catch(err=>{
                console.error(err);
                loader.style.display = 'none';
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Error al cargar datos</td></tr>`;
            });
        },300);
        }

        window.irPagina = (pag)=>{
        pagina.value = pag;
        aplicarFiltros();
        }

        inputFiltro?.addEventListener('keyup', aplicarFiltros);
        filtroEstado?.addEventListener('change', aplicarFiltros);
        filtroRol?.addEventListener('change', aplicarFiltros);
        filtroLiga?.addEventListener('change', aplicarFiltros);

        aplicarFiltros(); // carga inicial
    });
