
<div class="users-list-page">
<h3 class="users-title">Usuarios del sistema</h3>

<div class="wrap-toolbar">

    <form method="get" class="users-filters">

        <input type="hidden" name="pagina" id="pagina" value="1">
        <input type="hidden" name="limite" id="limite" value="10">

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="filtro" id="inputFiltro" placeholder="Buscar por nombre o usuario" value="<?= htmlspecialchars($_GET['filtro'] ?? ''); ?>">
        </div>

        <select name="filtroRol" id="filtroRol"  class="form-select">
            <option value="">Todos los roles</option>
            <?php foreach($roles as $rol): ?>
            <option value="<?= $rol['id']; ?>"><?= htmlspecialchars($rol['nombre']) ;?></option>
            <?php endforeach; ?>
        </select>

        <select id="filtroLiga" class="form-select">
            <option value="">Todas las ligas</option>
            <?php foreach($ligas as $liga): ?>
                <option value="<?= $liga['id'] ?>">
                    <?= htmlspecialchars($liga['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="filtroEstado" id="filtroEstado" class="form-select">
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>

        <a href="index.php?page=users" class="btn btn-primary">Limpiar</a>
    </form>
    <a href="index.php?page=users&action=create" class="btn btn-create">
        <i class="fas fa-plus"></i>Crear usuario
    </a>
</div>

<div id="loader" class="users-loader">
       <!-- Overlay loader -->
    <div id="loader-overlay" class="users-loader-overlay">
        <div class="spinner"></div>
    </div>
</div>


<div class="row card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th>USUARIO</th>
                    <th>ROL</th>
                    <th>CONTACTO</th>
                    <th>LIGA</th>
                    <th>EQUIPO</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                <?php 
                if (!empty($users)):         
                foreach ($users as $user): 
                    $avatar = $user['avatar'] ? '/afec/public/assets/img/avatars/'.$user['avatar']: '/afec/public/assets/img/avatars/default.png';
                    ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <img class="user-avatar" src="<?= $avatar ?>" alt="Avatar">
                            <div class="user-info">
                                <span class="user-name">
                                    <?= htmlspecialchars($user['nombre']) ?>
                                </span>
                                <span class="user-alias">
                                    @<?= htmlspecialchars($user['usuario']) ?>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td> <?= htmlspecialchars($user['rol'] ?? 'Sin rol'); ?> </td>
                    <td>
                    <div> <?= htmlspecialchars($user['correo']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($user['telefono']); ?></small>
                    </td>
                    <td> <?= $user['liga'] ?? '-'; ?> </td>
                    <td> <?=  $user['equipo'] ?? '-'; ?> </td>
                    <td class="text-center">
                        <span id="status-<?= $user['id'] ?>" class="user-status 
                        <?= $user['estado'] ? 'active' : 'inactive' ?>">
                            <?= $user['estado'] ? 'ACTIVO' : 'INACTIVO' ?>
                        </span>
                    </td>

                    <td class="text-center">
                    <label class="switch">
                        <input type="checkbox" class="toggle-user" data-id="<?= $user['id'] ?>"
                        <?= $user['estado'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>

                    <a href="index.php?page=users&action=edit&id=<?= $user['id'] ?>" class="btn-icon edit" title="Editar usuario">
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
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                <td colspan="5" class="users-empty-cell">No hay resultados</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="paginacion">
    <div class="paginacion-left">
        <span class="info-registros">Mostrando 1 a 10 de 156 registros</span>
    </div>
    <div class="paginacion-center">
        <!-- Aquí irán los botones de página generados por JS -->
    </div>
    <div class="paginacion-right">
        <span>Mostrar</span>
        <select id="limite">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        <span>registros</span>
    </div>
</div></div>





<script>
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

</script>

<script>
   document.addEventListener('DOMContentLoaded', () => 
    {
        const loader = document.getElementById('loader-overlay');
        const tbody = document.getElementById('tabla-body');
        const pagina = document.getElementById('pagina');
        const limite = document.getElementById('limite');
        const paginacion = document.getElementById('paginacion');
        const inputFiltro = document.getElementById("inputFiltro");
        const filtroEstado = document.getElementById("filtroEstado");
        const filtroRol = document.getElementById("filtroRol");
        const filtroLiga = document.getElementById("filtroLiga");

        let timer = null;

        function mostrarSkeleton(filas = 5, columnas = 7){
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
                        <td>${u.rol ?? 'Sin rol'}</td>
                        <td>
                            <div>${u.correo}</div>
                            <small class="text-muted">${u.telefono ?? ''}</small>
                        </td>

                        <td>${u.liga ?? '-'}</td>
                        <td>${u.equipo ?? '-'}</td>

                        <td class="text-center">
                            <span id="status-${u.id}" 
                                class="user-status ${u.estado == 1 ? 'active' : 'inactive'}">
                                ${u.estado == 1 ? 'ACTIVO' : 'INACTIVO'}
                            </span>
                        </td>

                        <td class="text-center">
                            <label class="switch">
                                <input type="checkbox"
                                    class="toggle-user"
                                    data-id="${u.id}"
                                    ${u.estado == 1 ? 'checked' : ''}>
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


</script>