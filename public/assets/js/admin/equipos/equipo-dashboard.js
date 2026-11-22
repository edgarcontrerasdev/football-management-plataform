document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'));

    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            var tab = new bootstrap.Tab(triggerEl);
            tab.show();
        });
    });
});
