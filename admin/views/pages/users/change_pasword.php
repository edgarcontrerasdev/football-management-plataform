<div class="container mt-4">
    <h2>Cambiar contraseña: <?= htmlspecialchars($user['usuario']) ?></h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Nueva contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-info">Guardar nueva contraseña</button>
        <a href="index.php?page=users" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
