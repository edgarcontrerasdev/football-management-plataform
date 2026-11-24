
<div class="users-list-page">
<div class="admin-page-header mb-4">
    <div>
        <h3 class="admin-page-title mb-1">Usuarios del sistema</h3>
        <p class="admin-page-description mb-0">Administración de cuentas, roles y accesos</p>
    </div>
</div>

<div class="wrap-toolbar">

    <form method="get" class="users-filters">

        <input type="hidden" name="pagina" id="pagina" value="1">
        <input type="hidden" name="limite" id="limiteFiltro" value="10">

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
                    <th>CUENTA</th>
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
                    <td> <?= htmlspecialchars($user['rol_nombre'] ?? 'Sin rol'); ?> </td>
                    <td>
                    <div>@<?= htmlspecialchars($user['usuario']) ?></div>
                    <small class="text-muted">ID <?= (int) $user['id'] ?></small>
                    </td>
                    <td> <?= htmlspecialchars($user['liga'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> </td>
                    <td> <?= htmlspecialchars($user['equipo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> </td>
                    <td class="text-center">
                        <span id="status-<?= $user['id'] ?>" class="user-status 
                        <?= $user['estado'] === 'activo' ? 'active' : 'inactive' ?>">
                            <?= $user['estado'] === 'activo' ? 'ACTIVO' : 'INACTIVO' ?>
                        </span>
                    </td>

                    <td class="text-center">
                    <label class="switch">
                        <input type="checkbox" class="toggle-user" data-id="<?= $user['id'] ?>"
                        <?= $user['estado'] === 'activo' ? 'checked' : '' ?>>
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
                <td colspan="7" class="users-empty-cell">No hay resultados</td>
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
</div>
</div>

<?php startSection('scripts'); ?>
<script src="<?= BASE_URL ?>/public/assets/js/admin/users/user-list.js"></script>
<?php endSection('scripts'); ?>
