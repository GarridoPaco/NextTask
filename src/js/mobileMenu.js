// Gestión de menú movil de la página de inicio
const indexMobileMenuBtn = document.querySelector('#indexMobileMenuBtn');
const header = document.querySelector('header');
const headerNavigation = document.querySelector('.header-navigation');
const headerButtonGroup = document.querySelector('.header-button-group');

let screenWidth = window.innerWidth;

window.addEventListener('resize', function () {

    screenWidth = window.innerWidth;

    // Oculto el menú mobile cuando se redimensiona la pantalla
    header.classList.remove('show');

    // Control de la disposición del grupo de botones para evitar desbordamiento
    if(screenWidth < 1176) {
        headerButtonGroup.style.flexDirection = 'column';
    } else {
        headerButtonGroup.style.flexDirection = 'row';

    }

    // Si el ancho de pantalla es suficientemente pequeño oculto el menú de navegación y
    // los botones de acción
    if (screenWidth <= 1024) {
        headerNavigation.style.display = 'none';
        headerButtonGroup.style.display = 'none';
    } else {
        // Si no, permito que estén visibles
        headerNavigation.style.display = 'block';
        headerButtonGroup.style.display = 'flex';
    }
});

// Abre el menú móvil al hacer clic en el botón correspondiente
if (screenWidth <= 1024) {
    indexMobileMenuBtn.addEventListener('click', function () {
        header.classList.toggle('show');
        if (header.classList.contains('show')) {
            headerNavigation.style.display = 'block';
            headerButtonGroup.style.display = 'flex';
        } else {
            headerNavigation.style.display = 'none';
            headerButtonGroup.style.display = 'none';
        }
    });
}

