window.Modal = (function(){

    const instances = {};

    function get(id){
        const el = document.getElementById(id);
        if(!el){
            console.error('Modal no encontrado:', id);
            return null;
        }

        if(!instances[id]){
            instances[id] = bootstrap.Modal.getOrCreateInstance(el);
        }

        return instances[id];
    }

    function open(id){
        const modal = get(id);
        if(!modal) return;

        //  limpiar antes de abrir
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(b => b.remove());
        document.body.classList.remove('modal-open');

        modal.show();
    }

    function close(id){
        const modal = get(id);
        if(!modal) return;

        const el = document.getElementById(id);

        //quitar focus antes de cerrar
        if(document.activeElement){
            document.activeElement.blur();
        }

        modal.hide();

        //limpieza extra
        setTimeout(() => {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(b => b.remove());
        }, 300);
    }

    function onClose(id, callback){
        const el = document.getElementById(id);
        if(!el) return;

        el.addEventListener('hidden.bs.modal', callback, { once: true });
    }

    return {
        open,
        close,
        onClose
    };

})();

  document.addEventListener('click', function(e){

        const btn = e.target.closest('.btn-cancelar-modal');

        if(!btn) return;

        const modalEl = btn.closest('.modal');

        if(!modalEl) return;

        const id = modalEl.id;

        if(id){
            Modal.close(id);
        }

    });
