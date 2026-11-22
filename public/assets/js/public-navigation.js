/* Navegación accesible de submenús: toque, teclado y clic */
(() => {
  const nav = document.querySelector('.public-site .header-nivel3');
  if (!nav) return;

  const triggers = nav.querySelectorAll('.dropdown > a[aria-controls]');

  const closeDropdown = (item) => {
    item.classList.remove('open');
    const trigger = item.querySelector(':scope > a[aria-controls]');
    if (trigger) trigger.setAttribute('aria-expanded', 'false');

    item.querySelectorAll('.dropdown.open').forEach((child) => {
      child.classList.remove('open');
      const childTrigger = child.querySelector(':scope > a[aria-controls]');
      if (childTrigger) childTrigger.setAttribute('aria-expanded', 'false');
    });
  };

  triggers.forEach((trigger) => {
    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      const item = trigger.parentElement;
      const shouldOpen = !item.classList.contains('open');

      const siblings = Array.from(item.parentElement.children)
        .filter((sibling) => sibling !== item && sibling.classList.contains('dropdown'));
      siblings.forEach(closeDropdown);

      if (shouldOpen) {
        item.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
      } else {
        closeDropdown(item);
      }
    });
  });

  document.addEventListener('click', (event) => {
    if (!nav.contains(event.target)) {
      nav.querySelectorAll('.dropdown.open').forEach(closeDropdown);
    }
  });

  nav.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      const openItem = event.target.closest('.dropdown.open');
      if (openItem) {
        const trigger = openItem.querySelector(':scope > a[aria-controls]');
        closeDropdown(openItem);
        if (trigger) trigger.focus();
      }
    }
  });
})();
