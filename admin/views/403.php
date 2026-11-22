<div class="error-page error-403">

    <div class="error-icon">
        <i class="fas fa-lock"></i>
    </div>

    <div class="error-code">403</div>

    <h2>Acceso no autorizado</h2>

    <p>
        No tienes permisos para acceder a este módulo
        o recurso.
    </p>

    <a href="index.php?page=<?= Auth::role() === 'LIGA' ? 'liga' : 'dashboard' ?>"
       class="btn-error">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>

</div>

