(function () {
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) window.location.reload();
    });

    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const btnToggle = document.getElementById('btnToggle');
    const btnFull = document.getElementById('btnFull');
    const isMobile = () => window.innerWidth < 900;

    function setSidebarOpen(open) {
        if (!sidebar || !main) return;

        if (isMobile()) {
            sidebar.classList.toggle('show', open);
            sidebar.classList.remove('collapsed');
        } else {
            sidebar.classList.remove('show');
            sidebar.classList.toggle('collapsed', !open);
            main.classList.toggle('collapsed', !open);
        }

        sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (btnToggle) btnToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function () {
            const open = isMobile()
                ? !sidebar.classList.contains('show')
                : sidebar.classList.contains('collapsed');
            setSidebarOpen(open);
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

    document.addEventListener('click', function (event) {
        if (!sidebar || !btnToggle || !isMobile() || !sidebar.classList.contains('show')) return;
        if (!sidebar.contains(event.target) && !btnToggle.contains(event.target)) {
            setSidebarOpen(false);
        }
    });

    window.addEventListener('resize', function () {
        if (!sidebar || !main) return;
        if (isMobile()) {
            sidebar.classList.remove('collapsed');
            main.classList.remove('collapsed');
            sidebar.classList.remove('show');
            sidebar.setAttribute('aria-hidden', 'true');
            if (btnToggle) btnToggle.setAttribute('aria-expanded', 'false');
        } else {
            sidebar.classList.remove('show');
            sidebar.classList.remove('collapsed');
            main.classList.remove('collapsed');
            sidebar.setAttribute('aria-hidden', 'false');
            if (btnToggle) btnToggle.setAttribute('aria-expanded', 'true');
        }
    });
})();