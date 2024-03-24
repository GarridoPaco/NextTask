/**
 * Botón para mostrar el modal para añadir proyecto.
 * Agrega un event listener al botón de clase 'addProject' 
 * para mostrar el modal de proyecto cuando se hace clic en él.
 */
const newProjectBtn = document.querySelector('.addProject');
newProjectBtn.addEventListener('click', function() {
    const projectModal = document.querySelector('#projectModal');
    projectModal.style.display = 'flex';
});

/**
 * Alertas utilizando SweetAlert2 para el listado de proyectos.
 * Muestra alertas visuales utilizando SweetAlert2 basadas en las clases CSS 
 * 'error' o 'exito' de las alertas en la lista de proyectos.
 */
const projectsList = document.querySelector('.projectsList');
const projectsAlerts = projectsList.querySelectorAll('.alerta');
projectsAlerts.forEach( alert => {
    if (alert.classList.contains('error')){
        infoAction.fire({
            icon: "error",
            title: alert.textContent
        });
    }
    if (alert.classList.contains('exito')){
        infoAction.fire({
            icon: "success",
            title: alert.textContent
        });
    }
});

/**
 * Formularios de eliminación de proyectos.
 * Maneja la eliminación de proyectos. Al hacer clic en el botón de eliminación, 
 * muestra un diálogo de confirmación utilizando SweetAlert2. Si se confirma, 
 * el formulario de eliminación se envía.
 */
const deleteProjectForms = document.querySelectorAll('#deleteProjectForm');

deleteProjectForms.forEach(deleteProjectForm => {
    const deleteProjectBtn = deleteProjectForm.querySelector('#deleteProjectBtn');
    deleteProjectBtn.onclick = function (e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Estás seguro que quieres eliminar la tarea?",
            text: "Esta operación no es reversible",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Borrar"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteProjectForm.submit();
            }
        });
    };
    
});
