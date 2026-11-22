(function () {
    function renderWithAnimation(wrapper, html) {
        wrapper.classList.add('table-fade-exit-active');

        setTimeout(function () {
            wrapper.innerHTML = html;
            wrapper.classList.remove('table-fade-exit-active');
            wrapper.classList.add('table-fade-enter');

            setTimeout(function () {
                wrapper.classList.add('table-fade-enter-active');

                setTimeout(function () {
                    wrapper.classList.remove('table-fade-enter');
                    wrapper.classList.remove('table-fade-enter-active');
                }, 350);
            }, 10);
        }, 150);
    }

    function showLoader(wrapper) {
        wrapper.innerHTML = `
            <div class="table-ajax-loader table-ajax-loader--light">
                <div class="spinner"></div>
                <p>Cargando...</p>
            </div>
        `;
    }

    async function loadData(wrapper, url) {
        showLoader(wrapper);
        await new Promise(function (resolve) {
            setTimeout(resolve, 150);
        });

        try {
            const response = await fetch(url + '&ajax=1');
            if (!response.ok) throw new Error('HTTP ' + response.status);

            const html = await response.text();
            setTimeout(function () {
                renderWithAnimation(wrapper, html);
            }, 300);
        } catch (error) {
            console.error('Error al cargar la tabla:', error);
            wrapper.innerHTML = '<p>Error al cargar datos</p>';
        }
    }

    // Paginación AJAX: el listener delegado también funciona después de actualizar la tabla.
    document.addEventListener('click', function (event) {
        const link = event.target.closest('#table-wrapper .pagination a, #table-wrapper #paginacion a');
        if (!link) return;

        event.preventDefault();

        const wrapper = link.closest('#table-wrapper');
        if (!wrapper) return;

        wrapper.innerHTML = `
            <div class="table-ajax-loader">
                <div class="spinner"></div>
                <p>Cargando...</p>
            </div>
        `;

        requestAnimationFrame(function () {
            fetch(link.getAttribute('href') + '&ajax=1')
                .then(function (response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.text();
                })
                .then(function (html) {
                    setTimeout(function () {
                        wrapper.innerHTML = html;
                    }, 250);
                })
                .catch(function (error) {
                    console.error('Error al cambiar de página:', error);
                    wrapper.innerHTML = '<p>Error al cargar datos</p>';
                });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.wrap-toolbar form');
        const wrapper = document.getElementById('table-wrapper');

        if (form && wrapper) {
            const searchInput = form.querySelector('input[name="buscar"]');
            let timeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        const params = new URLSearchParams(new FormData(form));
                        loadData(wrapper, '?' + params.toString());
                    }, 400);
                });
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const params = new URLSearchParams(new FormData(form));
                loadData(wrapper, '?' + params.toString());
            });
        }
    });

    // Indicadores visuales de carga usados por formularios de tablas existentes.
    const formFiltro = document.querySelector('#formFiltro');
    if (formFiltro) {
        formFiltro.addEventListener('submit', function () {
            const tableData = document.querySelector('.table-data');
            const skeleton = document.querySelector('.table-skeleton');

            if (tableData) tableData.style.display = 'none';
            if (skeleton) skeleton.style.display = 'table-row-group';
        });
    }

    const toolbarForm = document.getElementById('toolbarForm');
    if (toolbarForm) {
        toolbarForm.addEventListener('submit', function () {
            const toolbar = document.querySelector('.wrap-toolbar');
            if (toolbar) toolbar.classList.add('toolbar-loading');
        });
    }
})();
