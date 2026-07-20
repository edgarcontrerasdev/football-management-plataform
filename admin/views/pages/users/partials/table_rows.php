<?php if (!empty($users)):         
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
                <td colspan="7" style="text-align:center;">No hay usuarios registrados.</td>
            </tr>
        <?php endif; ?>