(function () {
    // Recargar la página al regresar desde la caché de navegación.
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const btnToggle = document.getElementById('btnToggle');
    const btnFull = document.getElementById('btnFull');

    function setSidebarCollapsed(collapsed) {
        if (!sidebar || !main) return;

        sidebar.classList.toggle('collapsed', collapsed);
        main.classList.toggle('collapsed', collapsed);
        sidebar.setAttribute('aria-hidden', collapsed ? 'true' : 'false');
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function () {
            setSidebarCollapsed(!sidebar.classList.contains('collapsed'));
        });
    }

    if (btnFull) {
        btnFull.addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function () {});
            } else {
                document.exitFullscreen().catch(function () {});
            }
        });
    }

    // En pantallas pequeñas, cerrar el menú al hacer clic fuera de él.
    document.addEventListener('click', function (event) {
        if (!sidebar || !btnToggle || window.innerWidth >= 900 || sidebar.classList.contains('collapsed')) {
            return;
        }

        const path = event.composedPath();
        const insideSidebar = path.includes(sidebar);
        const insideToggle = path.includes(btnToggle);

        if (!insideSidebar && !insideToggle) {
            setSidebarCollapsed(true);
        }
    });
})();
