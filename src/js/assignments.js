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
        showAlert(result.message, result.type);

        if (result.type === 'exito') {
            const assignmentsModal = document.querySelector('#assignmentsModal');
            assignmentsModal.style.display = 'none';

            // Despliego la info adicional de la tarea
            const taskContainer = document.querySelector(`.taskContainer[data-task-id="${task.id}"]`);
            const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
            taskContentContainer.classList.add('active');

            // Ancla
            const taskAssignContainer = document.querySelector(`.taskAssignContainer[data-task_id="${task.id}"]`);
            taskAssignContainer.style.display = 'block';
            const taskCollaboratorsContainer = taskAssignContainer.querySelector('.taskCollaboratorsContainer');
            const assignment = { 'user_id': collaborator_id, 'task_id': task.id, 'deadline': task.deadline };

            viewAssignment(user, project, task, assignment, collaborators, taskCollaboratorsContainer);

            infoAction.fire({
                icon: "success",
                title: result.message
            });
            // let tasks = await getTasks();
            // if (generalView) {
            //     showTasks(tasks,session_id, project);
            // }
            // if (kanbanView) {
            //     showTasksKanban();
            // }
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido realizar la asignación'
        });
        console.log(error);
    }
};

// Consulta para eliminar asignaciones
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
        //showAlert(result.message, result.type);

        if (result.type === 'exito') {
            // Despliego la info adicional de la tarea
            const taskContainer = document.querySelector(`.taskContainer[data-task-id="${assignment.task_id}"]`);
            const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
            taskContentContainer.classList.add('active');

            // Oculto el contenedor del colaborador eliminado
            const taskCollaborator = taskContainer.querySelector(`.taskCollaborator[data-collaborator-id="${assignment.user_id}"]`);
            taskCollaborator.remove();

            // Si no hay colaboradores oculto el contenedor de colaboraciones
            const taskAssignContainer = document.querySelector(`.taskAssignContainer[data-task_id="${assignment.task_id}"]`);
            const taskCollaboratorContainer = taskAssignContainer.querySelector('.taskCollaboratorsContainer');
            if (taskCollaboratorContainer.children.length === 0) taskAssignContainer.style.display = "none";

            infoAction.fire({
                icon: "success",
                title: result.message
            });
            // let tasks = await getTasks();
            // if (generalView) {
            //     showTasks(tasks, session_id, project);
            // }
            // if (kanbanView) {
            //     showTasksKanban();
            // }
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido eliminar el colaborador'
        });
        console.log(error);
    }

}

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
        const dinamicCollaborator = { 'id': assignmentOption.value, 'name': nameParts[0], 'last_name': nameParts.slice(1).join(" ")};
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

    if (filterCollaborators.length === (assignmentOptions.length)) {
        Swal.fire({
            title: "Asignar tarea",
            text: "No hay más colaboradores disponibles",
            icon: "warning",
            confirmButtonColor: "#0075beff"
        });
        return;
    }

    const assignmentsModal = document.querySelector('#assignmentsModal');
    assignmentsModal.style.display = 'flex';

    // Utilizo una función anónima como manejador de eventos
    const handleAssignmentClick = function (e) {
        e.preventDefault();

        const collaborador_id = document.querySelector('#selectAssignment').value;

        addAssign(collaborador_id, user, project, task, dinamicCollaborators);
        assignmentsModal.style.display = 'none';
        // Elimino el manejador de eventos después de su ejecución
        addAssignmentsBtn.removeEventListener('click', handleAssignmentClick);
    };
    addAssignmentsBtn.addEventListener('click', handleAssignmentClick);
}

function viewAssignment(user, project, task, assignment, collaborators, anchor) {

    if (assignment.task_id === task.id) {
        const taskCollaborator = document.createElement('DIV');
        taskCollaborator.classList.add('taskCollaborator');
        taskCollaborator.dataset.collaboratorId = assignment.user_id;

        const collaboratorContainer = document.createElement('DIV');
        collaboratorContainer.classList.add('collaboratorContainer');

        const collaborator = collaborators.find((element) => element.id === assignment.user_id);

        const collaboratorName = document.createElement('P');
        collaboratorName.classList.add('collaboratorName');
        collaboratorName.textContent = `${collaborator.name} ${collaborator.last_name}`;


        // const collaboratorImg = document.createElement('IMG');
        // collaboratorImg.classList.add('collaboratorImg');
        // collaboratorImg.src = `build/img/${collaborator.image}.jpg`;


        const deleteCollaboratorBtn = document.createElement('IMG');
        deleteCollaboratorBtn.src = 'build/img/delete_icon.svg';
        deleteCollaboratorBtn.title = 'Eliminar colaborador';
        deleteCollaboratorBtn.classList.add('deleteCollaboratorBtn');

        // Ocultar/mostrar el botón de eliminar colaborador
        if (user.id !== project.user_id) deleteCollaboratorBtn.style.display = 'none';
        deleteCollaboratorBtn.classList.add('actionImg');
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
                    deleteAssign(assignment);
                }
            });
        };

        // collaboratorContainer.appendChild(collaboratorImg);
        collaboratorContainer.appendChild(collaboratorName);
        taskCollaborator.appendChild(collaboratorContainer);
        taskCollaborator.appendChild(deleteCollaboratorBtn);
        anchor.appendChild(taskCollaborator)
    }
}