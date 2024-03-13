// Botón para mostrar el modal para añadir proyecto
const newProjectBtn = document.querySelector('.addProject');
newProjectBtn.addEventListener('click', function() {
    const projectModal = document.querySelector('#projectModal');
    projectModal.style.display = 'flex';
});

// Alertas utilizando sweetAlert2 para el listado de proyectos
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

// const deleteProjectBtns = document.querySelectorAll('#deleteProjectBtn');
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
