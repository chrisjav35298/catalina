// Obtener referencia al botón
const scrollTopButton = document.getElementById('scrollTopButton');

// Mostrar/ocultar el botón dependiendo de la posición de scroll
window.onscroll = function () {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        scrollTopButton.style.display = 'block';
    } else {
        scrollTopButton.style.display = 'none';
    }
};

// Función para subir al inicio
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
