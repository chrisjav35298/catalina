// document.addEventListener("DOMContentLoaded", function () {
//     const loader = document.getElementById("loader");
//     const hero = document.querySelector('.hero-section');

//     // Detectar si estamos en móvil
//     const isMobile = window.matchMedia("(max-width: 768px)").matches;

//     // Seleccionamos imagen según el tamaño de pantalla
//     const backgroundImage = isMobile 
//         ? '/img/bg-nuevo.webp' 
//         : '/img/header-bg-webp.webp';

//     const img = new Image();
//     img.src = backgroundImage;

//     img.onload = function () {
//         hero.style.backgroundImage = `url('${img.src}')`;

//         if (loader) {
//             loader.style.opacity = "0";
//             setTimeout(() => loader.style.display = "none", 300);
//         }
//     };

//     // Fallback de 4 segundos
//     setTimeout(() => {
//         if (loader && loader.style.display !== "none") {
//             loader.style.opacity = "0";
//             loader.style.display = "none";
//         }
//     }, 4000);
// });

document.addEventListener("DOMContentLoaded", function () {
    const loader = document.getElementById("loader");
    const hero = document.querySelector('.hero-section');

    const isMobile = window.matchMedia("(max-width: 768px)").matches;
    const backgroundImage = isMobile 
        ? '/img/bg-nuevo.webp' 
        : '/img/header-bg-webp.webp';

    const img = new Image();
    img.src = backgroundImage;

    // Si la imagen ya está cargada (usualmente desde caché), aplicar directamente
    if (img.complete) {
        hero.style.backgroundImage = `url('${img.src}')`;
        if (loader) {
            loader.style.display = "none";
        }
    } else {
        img.onload = function () {
            hero.style.backgroundImage = `url('${img.src}')`;
            if (loader) {
                loader.style.opacity = "0";
                setTimeout(() => loader.style.display = "none", 200);
            }
        };

        // Fallback de seguridad
        setTimeout(() => {
            if (loader && loader.style.display !== "none") {
                loader.style.opacity = "0";
                loader.style.display = "none";
            }
        }, 3000);
    }
});


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


// JavaScript para agregar efecto más perceptible
document.addEventListener("DOMContentLoaded", () => {
    const section = document.getElementById("mision");

    // Usa IntersectionObserver para activar la animación
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Retrasa el inicio del efecto
                    setTimeout(() => {
                        entry.target.classList.add("fade-in");
                    }, 300); // Retraso de 300ms para mayor visibilidad
                }
            });
        }, 
        { threshold: 0.2 }
    );

    observer.observe(section);
});

// Agrega animación con CSS más lenta y notoria
const style = document.createElement('style');
style.innerHTML = `
    #mision {
        opacity: 0;
        transform: translateY(80px); /* Mayor desplazamiento para más impacto */
        transition: all 1.2s ease-out; /* Efecto más lento */
    }

    #mision.fade-in {
        opacity: 1;
        transform: translateY(0);
    }
`;
document.head.appendChild(style);