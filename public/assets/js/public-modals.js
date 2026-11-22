/* Gestión accesible de los modales públicos */
(() => {
  const definitions = [
    {
      modal: document.getElementById('modalSearch'),
      opener: document.getElementById('btnSearch'),
      closer: document.getElementById('closeSearch'),
      initial: () => document.querySelector('#modalSearch .search-input')
    },
    {
      modal: document.getElementById('modalLogin'),
      opener: document.getElementById('btnLogin'),
      closer: document.getElementById('closeLogin'),
      initial: () => document.querySelector('#modalLogin .input-login')
    }
  ];

  const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  definitions.forEach(({ modal, opener, closer, initial }) => {
    if (!modal || !opener || !closer) return;

    let previousFocus = null;

    const getFocusable = () => Array.from(modal.querySelectorAll(focusableSelector))
      .filter((element) => !element.hasAttribute('hidden') && element.getClientRects().length > 0);

    const close = () => {
      if (!modal.classList.contains('active')) return;
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
      modal.setAttribute('inert', '');
      if (previousFocus && document.contains(previousFocus)) {
        previousFocus.focus();
      } else {
        opener.focus();
      }
    };

    const open = () => {
      definitions.forEach((other) => {
        if (other.modal && other.modal !== modal && other.modal.classList.contains('active')) {
          other.closer?.click();
        }
      });

      previousFocus = document.activeElement;
      modal.removeAttribute('inert');
      modal.setAttribute('aria-hidden', 'false');
      modal.classList.add('active');
      requestAnimationFrame(() => {
        const target = initial() || getFocusable()[0];
        target?.focus();
      });
    };

    opener.addEventListener('click', open);
    closer.addEventListener('click', close);

    modal.addEventListener('click', (event) => {
      if (event.target === modal) close();
    });

    modal.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        event.preventDefault();
        close();
        return;
      }

      if (event.key !== 'Tab') return;
      const focusable = getFocusable();
      if (!focusable.length) {
        event.preventDefault();
        modal.focus();
        return;
      }

      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (event.shiftKey && (document.activeElement === first || !modal.contains(document.activeElement))) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && (document.activeElement === last || !modal.contains(document.activeElement))) {
        event.preventDefault();
        first.focus();
      }
    });
  });
})();
