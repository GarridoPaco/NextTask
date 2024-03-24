/**
 * Muestra la información del proyecto en la interfaz de usuario.
 * @param {Object} project - Objeto que contiene la información del proyecto.
 */
async function showProject(project) {
    // Establecer el nombre del proyecto en el elemento HTML correspondiente
    document.querySelector('#projectName').textContent = project.name;
    // Establecer la descripción del proyecto en el elemento HTML correspondiente
    document.querySelector('#projectDescription').textContent = project.description;
    // Establecer la fecha límite del proyecto en el elemento HTML correspondiente, formateada correctamente
    document.querySelector('#projectDeadline').textContent = formatDate(project.deadline);
}

/**
 * Abre el modal para editar la información del proyecto.
 * @param {Array} tasks - Lista de tareas asociadas al proyecto.
 * @param {Object} user - Objeto que representa al usuario que realiza la edición.
 * @param {Object} project - Objeto que contiene la información del proyecto a editar.
 */
async function editModalProject(tasks, user, project) {
    // Mostrar el modal de edición del proyecto
    const projectModal = document.querySelector('#projectModal');
    projectModal.style.display = 'flex';

    // Configurar el botón de edición del proyecto en el modal
    const editProjectBtn = document.querySelector('#addProjectBtn');
    editProjectBtn.textContent = 'Actualizar proyecto';

    // Establecer el título del modal
    document.querySelector('#modalLegendProject').textContent = 'Actualizar Proyecto';

    // Obtener y establecer los valores actuales del proyecto en los campos del formulario
    let projectNameInput = document.querySelector('#projectNameInput').value = project.name;
    let projectDescriptionInput = document.querySelector('#projectDescriptionInput').value = project.description;
    let projectDeadlineInput = document.querySelector('#projectDeadlineInput').value = project.deadline;

    // Manejador de evento para la acción de actualización del proyecto
    const handleAddTaskClick = function (e) {
        e.preventDefault();
        // Obtener los nuevos valores del proyecto desde los campos del formulario
        projectNameInput = document.querySelector('#projectNameInput').value.trim();
        // Validar que el nombre del proyecto no esté vacío
        if (projectNameInput === '') {
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        projectDescriptionInput = document.querySelector('#projectDescriptionInput').value.trim();
        projectDeadlineInput = document.querySelector('#projectDeadlineInput').value.trim();
        // Crear el objeto de proyecto actualizado con los nuevos valores
        const projectEdit = {
            'id': project.id,
            'user_id': project.user_id,
            'name': projectNameInput,
            'description': projectDescriptionInput,
            'deadline': projectDeadlineInput
        };
        // Llamar a la función para actualizar el proyecto
        updateProject(tasks, user, projectEdit);
        // Eliminar el manejador de eventos después de su ejecución
        editProjectBtn.removeEventListener('click', handleAddTaskClick);
    };
    // Agregar el manejador de evento al botón de actualización del proyecto
    editProjectBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Consulta a la API para actualizar la información del proyecto.
 * @param {Array} tasks - Lista de tareas asociadas al proyecto.
 * @param {Object} user - Objeto que representa al usuario que realiza la actualización.
 * @param {Object} project - Objeto que contiene la información actualizada del proyecto.
 */
async function updateProject(tasks, user, project) {
    // Construir los datos para la petición
    const data = new FormData();
    data.append('id', project.id);
    data.append('user_id', project.user_id);
    data.append('name', project.name);
    data.append('description', project.description);
    data.append('deadline', project.deadline);
    data.append('url', getUrlProject());

    try {
        // Realizar la petición POST para actualizar el proyecto
        const url = `${location.origin}/api/project/update`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        // Verificar si la actualización fue exitosa
        if (result.type === 'exito') {
            // Ocultar el modal de edición del proyecto
            const projectModal = document.querySelector('#projectModal');
            projectModal.style.display = 'none';
            // Mostrar una notificación de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            // Actualizar la visualización del proyecto y el calendario
            showProject(project);
            showCalendar(tasks, user, project);
        }
    } catch (error) {
        // Mostrar una notificación de error si la actualización falla
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar el proyecto"
        });
        console.log(error);
    }
}

/**
 * Consulta a la API para eliminar el proyecto actual.
 */
async function deleteProject() {
    // Construir la petición
    const data = new FormData();
    data.append('url', getUrlProject());

    try {
        // Realizar la petición POST para eliminar el proyecto
        const url = `${location.origin}/api/project/delete`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        // Verificar si la eliminación fue exitosa
        if (result.type === 'exito') {
            // Ocultar el modal de la tarea y redirigir a la página de inicio del usuario
            const taskModal = document.querySelector('#taskModal');
            taskModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            window.location.href = '/dashboard';
        }
    } catch (error) {
        // Mostrar una notificación de error si la eliminación falla
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar el proyecto"
        });
        console.log(error);
    }
}