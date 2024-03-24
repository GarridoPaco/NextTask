/**
 * Agrega una asignación de tarea para un colaborador específico.
 * 
 * Construye una solicitud HTTP con los datos de la asignación.
 * Realiza una solicitud POST a la API de asignaciones para crear la asignación.
 * Muestra una alerta con el resultado de la asignación.
 * - Si la asignación se realiza con éxito:
 * - Oculta el modal de asignaciones.
 * - Muestra la información adicional de la tarea.
 * - Muestra el contenedor de asignaciones para la tarea.
 * - Crea un objeto de asignación.
 * - Muestra la asignación en la vista de la tarea.
 * - Muestra un mensaje de éxito.
 * Si la asignación falla, muestra un mensaje de error y registra el error en la consola.
 * 
 * @param {number} collaborator_id - ID del colaborador al que se asignará la tarea.
 * @param {object} user - Objeto que representa al usuario actual.
 * @param {object} project - Objeto que representa el proyecto al que pertenece la tarea.
 * @param {object} task - Objeto que representa la tarea a asignar.
 * @param {Array} collaborators - Arreglo de objetos que representan los colaboradores disponibles.
 */
async function addAssign(collaborator_id, user, project, task, collaborators) {
    // Construir la petición
    const data = new FormData();
    data.append('user_id', collaborator_id);
    data.append('task_id', task.id);
    data.append('deadline', task.deadline);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/assignment`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            // Ocultar el modal de asignaciones
            const assignmentsModal = document.querySelector('#assignmentsModal');
            assignmentsModal.style.display = 'none';

            // Mostrar la información adicional de la tarea
            const taskContainer = document.querySelector(`.taskContainer[data-task-id="${task.id}"]`);
            const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
            taskContentContainer.classList.add('active');

            // Mostrar el contenedor de asignaciones para la tarea
            const taskAssignContainer = document.querySelector(`.taskAssignContainer[data-task_id="${task.id}"]`);
            taskAssignContainer.style.display = 'block';

            // Crear un objeto de asignación
            const assignment = { 'user_id': collaborator_id, 'task_id': task.id, 'deadline': task.deadline };

            // Mostrar la asignación en la vista de la tarea
            const taskCollaboratorsContainer = taskAssignContainer.querySelector('.taskCollaboratorsContainer');
            viewAssignment(user, project, task, assignment, collaborators, taskCollaboratorsContainer);

            // Mostrar un mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        // Mostrar un mensaje de error si la asignación falla
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido realizar la asignación'
        });
        console.log(error);
    }
};

/**
 * Elimina una asignación de tarea para un colaborador específico.
 * 
 * Construye una solicitud HTTP con los datos de la asignación y la envía a la API de eliminación de asignaciones.
 * Si la eliminación se realiza con éxito:
 * - Muestra la información adicional de la tarea.
 * - Oculta el contenedor del colaborador eliminado.
 * - Si no hay más colaboradores, oculta el contenedor de asignaciones.
 * - Muestra un mensaje de éxito.
 * Si la eliminación falla, muestra un mensaje de error y registra el error en la consola.
 * 
 * @param {object} assignment - Objeto que representa la asignación de tarea a eliminar.
 *                            Tiene las propiedades 'user_id' (ID del colaborador) y 'task_id' (ID de la tarea).
 */
async function deleteAssign(assignment) {
    // Construir la petición
    const data = new FormData();
    data.append('user_id', assignment.user_id);
    data.append('task_id', assignment.task_id);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/assignment/delete`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            // Mostrar la información adicional de la tarea
            const taskContainer = document.querySelector(`.taskContainer[data-task-id="${assignment.task_id}"]`);
            const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
            taskContentContainer.classList.add('active');

            // Ocultar el contenedor del colaborador eliminado
            const taskCollaborator = taskContainer.querySelector(`.taskCollaborator[data-collaborator-id="${assignment.user_id}"]`);
            taskCollaborator.remove();

            // Si no hay colaboradores ocultar el contenedor de colaboraciones
            const taskAssignContainer = document.querySelector(`.taskAssignContainer[data-task_id="${assignment.task_id}"]`);
            const taskCollaboratorContainer = taskAssignContainer.querySelector('.taskCollaboratorsContainer');
            if (taskCollaboratorContainer.children.length === 0) taskAssignContainer.style.display = "none";

            // Muestra un mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        // Muestra un mensaje de error si la eliminación falla
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido eliminar el colaborador'
        });
        console.log(error);
    }
}

/**
 * Abre el modal de asignaciones para una tarea específica y configura las opciones disponibles de colaboradores.
 * 
 * Restablece el estado del select de asignación.
 * Recopila los nombres e IDs de los colaboradores añadidos dinámicamente al modal de asignaciones para evitar nuevas llamadas al servidor.
 * Filtra los colaboradores ya asignados a la tarea para no mostrarlos como opciones en el select.
 * Deshabilita las opciones de colaboradores ya asignados en el select.
 * Si no hay colaboradores disponibles, muestra una alerta.
 * Abre el modal de asignaciones y agrega un manejador de eventos para la asignación de tarea.
 * 
 * @param {object} task - Objeto que representa la tarea para la que se abrirá el modal de asignaciones.
 * @param {object} user - Objeto que representa al usuario actual.
 * @param {object} project - Objeto que representa el proyecto al que pertenece la tarea.
 */
function assignmentsModal(task, user, project) {
    // Restablecer el estado del select
    document.getElementById("selectAssignment").selectedIndex = 0;

    // Saco los nombres e ids de los colaboradores añadidos dinámicamente al modal de asignaciones,
    // por si se han hecho modificaciones y evitar tener que hacer una nueva llamada al servidor
    let dinamicCollaborators = [];
    const assignmentOptions = document.querySelectorAll('.assignmentOption');
    assignmentOptions.forEach(assignmentOption => {
        assignmentOption.style.display = 'block';
        const completeName = assignmentOption.textContent;
        const nameParts = completeName.split(" ");
        const dinamicCollaborator = { 'id': assignmentOption.value, 'name': nameParts[0], 'last_name': nameParts.slice(1).join(" ") };
        dinamicCollaborators.push(dinamicCollaborator);

    });

    // Busco los colaboradores ya asignados a la tarea para no darlos como opción en el select
    const taskAssignContainer = document.querySelector(`.taskAssignContainer[data-task_id="${task.id}"]`);
    const taskCollaborators = taskAssignContainer.querySelectorAll('.taskCollaborator');
    let collaboratorsId = [];
    taskCollaborators.forEach(taskCollaborator => {
        collaboratorsId.push(taskCollaborator.dataset.collaboratorId);
    });

    let filterCollaborators = dinamicCollaborators.filter(function (collaborator) {
        return collaboratorsId.includes(collaborator.id);
    });

    // Deshabilito aquellos colaboradores ya asignados a la tarea
    assignmentOptions.forEach(assignmentOption => {
        if (filterCollaborators.some(function (filterCollaborator) {
            return assignmentOption.value === filterCollaborator.id;
        })) {
            assignmentOption.style.display = 'none';
        }
    });

    // Si no hay colaboradores disponibles, muestra una alerta y finaliza la función
    if (filterCollaborators.length === (assignmentOptions.length)) {
        Swal.fire({
            title: "Asignar tarea",
            text: "No hay más colaboradores disponibles",
            icon: "warning",
            confirmButtonColor: "#0075beff"
        });
        return;
    }

    // Abre el modal de asignaciones y agrega un manejador de eventos para la asignación de tarea
    const assignmentsModal = document.querySelector('#assignmentsModal');
    assignmentsModal.style.display = 'flex';

    // Utilizo una función anónima como manejador de eventos
    const handleAssignmentClick = function (e) {
        e.preventDefault();

        const collaborador_id = document.querySelector('#selectAssignment').value;
        // Agrego la asignación de tarea
        addAssign(collaborador_id, user, project, task, dinamicCollaborators);
        // Oculto el modal de asignaciones
        assignmentsModal.style.display = 'none';
        // Elimino el manejador de eventos después de su ejecución
        addAssignmentsBtn.removeEventListener('click', handleAssignmentClick);
    };
    // Agrega un manejador de eventos para el botón de asignación
    addAssignmentsBtn.addEventListener('click', handleAssignmentClick);
}

/**
 * Muestra una asignación de tarea en la vista de la tarea actual, 
 * incluyendo el nombre del colaborador y un botón para eliminar la asignación si es aplicable.
 * 
 * Verifica si la asignación pertenece a la tarea actual.
 * Crea un contenedor de colaborador y un botón para eliminar la asignación.
 * Oculta el botón de eliminar si el usuario actual no es el propietario del proyecto.
 * Agrega un evento de clic al botón de eliminar para confirmar la eliminación de la asignación.
 * 
 * @param {object} user - Objeto que representa al usuario actual.
 * @param {object} project - Objeto que representa el proyecto al que pertenece la tarea.
 * @param {object} task - Objeto que representa la tarea actual.
 * @param {object} assignment - Objeto que representa la asignación de la tarea.
 * @param {Array} collaborators - Arreglo de objetos que representan los colaboradores disponibles.
 * @param {HTMLElement} anchor - Elemento HTML en el que se agregarán los detalles de la asignación.
 */
function viewAssignment(user, project, task, assignment, collaborators, anchor) {

    if (assignment.task_id === task.id) {
        // Crear un contenedor para el colaborador
        const taskCollaborator = document.createElement('DIV');
        taskCollaborator.classList.add('taskCollaborator');
        taskCollaborator.dataset.collaboratorId = assignment.user_id;

        // Crear un contenedor para el nombre del colaborador
        const collaboratorContainer = document.createElement('DIV');
        collaboratorContainer.classList.add('collaboratorContainer');

        // Encontrar el colaborador correspondiente
        const collaborator = collaborators.find((element) => element.id === assignment.user_id);

        // Crear un elemento de texto para mostrar el nombre del colaborador
        const collaboratorName = document.createElement('P');
        collaboratorName.classList.add('collaboratorName');
        collaboratorName.textContent = `${collaborator.name} ${collaborator.last_name}`;

        // Crear un botón para eliminar la asignación
        const deleteCollaboratorBtn = document.createElement('IMG');
        deleteCollaboratorBtn.src = 'build/img/delete_icon.svg';
        deleteCollaboratorBtn.title = 'Eliminar colaborador';
        deleteCollaboratorBtn.classList.add('deleteCollaboratorBtn');

        // Ocultar el botón de eliminar si el usuario no es el propietario del proyecto
        if (user.id !== project.user_id) deleteCollaboratorBtn.style.display = 'none';
        deleteCollaboratorBtn.classList.add('actionImg');

        // Agregar un evento de clic al botón de eliminar
        deleteCollaboratorBtn.onclick = function () {
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar la asignación?",
                text: "Esta operación no es reversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Borrar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Eliminar la asignación
                    deleteAssign(assignment);
                }
            });
        };

        // Agregar el nombre del colaborador al contenedor
        collaboratorContainer.appendChild(collaboratorName);
        // Agregar el contenedor del colaborador y el botón de eliminar al contenedor principal
        taskCollaborator.appendChild(collaboratorContainer);
        taskCollaborator.appendChild(deleteCollaboratorBtn);
        // Agregar el contenedor principal al elemento HTML especificado
        anchor.appendChild(taskCollaborator)
    }
}