<h3>Editar Liga: <?= htmlspecialchars($liga['nombre']); ?></h3>

<form action="index.php?c=ligas&a=update&id=<?= $liga['id']; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Nombre de la Liga</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($liga['nombre']); ?>" required>
    </div>

    <div class="form-group">
        <label>Estado</label>
        <select name="estado" class="form-control">
            <option value="activa" <?= $liga['estado']=='activa'?'selected':''; ?>>Activa</option>
            <option value="inactiva" <?= $liga['estado']=='inactiva'?'selected':''; ?>>Inactiva</option>
            <option value="suspendida" <?= $liga['estado']=='suspendida'?'selected':''; ?>>Suspendida</option>
        </select>
    </div>

    <div class="form-group">
        <label>Logo actual:</label>
        <?php if($liga['logo']): ?>
            <img src="uploads/ligas/<?= $liga['logo']; ?>" alt="Logo" width="100">
        <?php endif; ?>
        <input type="file" name="logo" class="form-control">
    </div>

    <!-- El resto de campos iguales que create con valores prellenados -->
    <!-- ... -->
    
    <button type="submit" class="btn btn-primary">Actualizar Liga</button>
</form>
