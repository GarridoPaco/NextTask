// Filtros de búsqueda de tareas asignadas
const collaborations = document.querySelectorAll('.collaborationBin');
const filters = document.querySelectorAll('#filters input[type="radio');

// Si se selecciona algún filtro se filtran las tareas
filters.forEach(radio => {
    radio.addEventListener('input', filterTasks);
});

// Función que muestra/oculta las tareas asignadas según su estado
function filterTasks(e) {
    const filter = e.target.value;

    switch (filter) {
        case '0':
            collaborations.forEach(collaboration => {
                const collaborationStatus = collaboration.querySelector('.collaborationStatus').textContent;
                if (collaborationStatus.includes('Pendiente')) {
                    collaboration.style.display = 'grid';
                } else {
                    collaboration.style.display = 'none';
                }
            });
            break;
        case '1':
            collaborations.forEach(collaboration => {
                const collaborationStatus = collaboration.querySelector('.collaborationStatus').textContent;
                if (collaborationStatus.includes('En progreso')) {
                    collaboration.style.display = 'grid';
                } else {
                    collaboration.style.display = 'none';
                }
            });
            break;
        case '2':
            collaborations.forEach(collaboration => {
                const collaborationStatus = collaboration.querySelector('.collaborationStatus').textContent;
                if (collaborationStatus.includes('Finalizada')) {
                    collaboration.style.display = 'grid';
                } else {
                    collaboration.style.display = 'none';
                }
            });
            break;
        default:
            collaborations.forEach(collaboration => {
                collaboration.style.display = 'grid';
            });
            break;
    }
}