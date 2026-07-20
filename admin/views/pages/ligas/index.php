
    <h3 class="users-title">Ligas</h3>

    
    <!-- KPIS CON INFO RELEVANTE -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="kpi-card success">
                <span class="kpi-title">TOTAL AFILIADOS A LIGA</span>
                <h3><?php echo '1,200'; ?></h3>
                <i class="bi bi-people"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card info">
                <span class="kpi-title">Cuerpo Tecnico Registrados</span>
                <h3><?= $kpis['dt'] ?? 0 ?></h3>
                <i class="bi bi-person-badge"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card warning">
                <span class="kpi-title">CREDENCIAELES PENDIENTES</span>
                <h3><?= $kpis['pendientes'] ?? 80 ?></h3>
                <i class="bi bi-clock"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card danger">
                <span class="kpi-title">AFILIACIONES RECHAZADAS</span>
                <h3><?= $kpis['rechazados'] ?? 25 ?></h3>
                <i class="bi bi-x-circle"></i>
            </div>
        </div>

    </div>


    <?php include ROOT_PATH.'/admin/views/components/table-toolbox.php'; ?>


    <?php include ROOT_PATH. '/admin/views/components/table-content.php'; ?>


<style>

    .table-avatar{
    display:flex;
    align-items:center;
    gap:10px;
    }

    .avatar{
    width:36px;
    height:36px;
    border-radius:50%;
    object-fit:cover;
    border:1px solid #ddd;
    }

    :root{
        /* 🎨 Colores principales */
        --primary: #1e40af;        /* Azul royal */
        --primary-dark: #1e3a8a;
        --primary-light: #3b82f6;

        /* Estados */
        --success: #16a34a;
        --danger:  #dc2626;
        --warning: #f59e0b;
        --info:    #0ea5e9;

        /* 🧱 Fondo y superficies */
        --bg-white:      #f8fafc;        /* Fondo general del sistema */
        --card:    #ffffff;        /* Cards, tablas, modales */
        --border:  #e5e7eb;

        /* 📝 Texto */
        --text:        #1f2937;
        --text-muted: #6b7280;
    }

    body{
        background-color: var(--bg);
        color:var(--text);
    }
    .btn{
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }

    .btn-primary{
        background: var(--primary);
        color: #fff;
    }

    .btn-secondary{
        background: var(--secondary);
        color: #fff;
    }

    .btn-danger{
        background: var(--danger);
        color: #fff;
    }

    .btn-warning{
        background: var(--warning);
        color: #000;
    }

    .switch{
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .switch input{
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider{
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 34px;
        transition: .3s;
    }

    .slider::before{
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
    }

    input:checked + .slider{
        background: #2ecc71;
    }

    input:checked + .slider::before{
        transform: translateX(20px);
    }

    table{
        table-layout: fixed;
        width:100%;
    }

    .status-label {
        font-weight: bold;
        padding: 2px 8px;
        border-radius: 12px;
        color: white;
        font-size: 0.9em;
    }

    .user-status{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 90px;           /* 🔑 MISMO TAMAÑO */
        height: 28px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        color: #fff;
        letter-spacing: .5px;
        transition: all .25s ease;
    }

    .user-status.active{
        background: var(--success);
        box-shadow: 0 0 0 1px rgba(46,204,113,.5);
    }

    .user-status.inactive{
        background: var(--danger);
        box-shadow: 0 0 0 1px rgba(231,76,60,.5);
    }

    .btn-icon{
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all .2s ease;
    }

    .btn-icon svg{
        width: 16px;
        height: 16px;
    }

    /* Edit */
    .btn-icon.edit{
        background: #f1f3f5;
        color: #2c3e50;
    }

    .btn-icon.edit:hover{
        background: var(--primary-dark);
        color: #fff;
    }


    .btn-create{
        background: #0d6efd;
        color: #fff;
        border-radius: 8px;
        padding: 8px 14px;
        display: flex;
        gap: 6px;
        align-items: center;
    }


    .users-title{
        color: var(--warning);
        margin-bottom: 10px;
    }
        

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e5e5;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #212529;
    }

    .user-alias {
        font-size: 14px;
        color: #6c757d;
    }

    .escudo-wrapper{
        height:50px;
        width:50px;
        background-color: #bec4ca;
        border-radius: 5px;
        padding: 2px;
    }
    .escudo{
        width:45px;
        height: 45px;
    }

    /* ================= PAGINACIÓN MODERNA ================= */

    #paginacion {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #0f172a, #1f2937);
        border-radius: 14px;
        color: #e5e7eb;
        font-size: 14px;
    }

    /* Contenedor central */
    #paginacion .paginacion-center {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ===== BOTÓN BASE ===== */
    #paginacion .btn {
        height: 38px;
        padding: 0 16px;
        border-radius: 999px; /* 🔥 PILL */
        border: none;
        background: rgba(255,255,255,0.08);
        color: #e5e7eb;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all .25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== BOTONES DE NAVEGACIÓN (‹ ›) ===== */
    #paginacion .btn-nav {
        min-width: 42px;
        font-size: 18px;
        font-weight: 600;
    }

    /* ===== BOTONES NUMÉRICOS ===== */
    #paginacion .btn-page {
        min-width: 38px;
    }

    /* ===== HOVER CON GRADIENTE ===== */
    #paginacion .btn:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        box-shadow: 0 6px 16px rgba(59,130,246,.45);
        transform: translateY(-1px);
    }

    /* ===== ACTIVO ===== */
    #paginacion .btn.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(37,99,235,.55);
        transform: none;
    }

    /* ===== DESHABILITADO ===== */
    #paginacion .btn:disabled {
        background: rgba(255,255,255,0.04);
        color: #9ca3af;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Texto informativo */
    #paginacion .info-registros {
        color: #cbd5e1;
    }

    /* Selector */
    #paginacion select {
        padding: 6px 10px;
        border-radius: 10px;
        border: none;
        background: #f8fafc;
        color: #1e40af;
        font-weight: 500;
    }

    .btn.disabled {
        opacity: 0.45;
        pointer-events: none;
        cursor: not-allowed;
    }

    /* Spinner */
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        animation: girar 1s linear infinite;
        margin: auto;
    }
    @keyframes girar { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }

    .skeleton {
        display: inline-block;
        height: 16px;
        width: 100%;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer { 0% { background-position: -200% 0;} 100% { background-position: 200% 0;} }


</style>