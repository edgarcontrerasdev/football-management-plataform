// Fecha en vivo
document.getElementById("live-date").innerText =
    new Date().toLocaleString("es-MX", {
        weekday: "long",
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });

// Toggle menú móvil
const btnMenu = document.getElementById("btnMenu");
const menu = document.getElementById("menu");
btnMenu.addEventListener("click", () => menu.classList.toggle("open"));
