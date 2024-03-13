async function showTasks(tasks, user, project) {
    let collaborators = await getCollaborators();
    let assignments = await getAssign();

    const tasksList = document.querySelector('#tasks-list');
    tasksList.innerHTML = '';
    if (tasks.length === 0) {
        const textNoTasks = document.createElement('P');
        textNoTasks.textContent = '¡Es hora de dar un paso adelante en tu proyecto! Agrega nuevas tareas para alcanzar tus objetivos. ¡Cada tarea te acerca un paso más hacia el éxito! Haz clic en el botón de abajo para empezar a añadir tareas ahora mismo.';
        textNoTasks.classList.add('no-tasks');
        tasksList.appendChild(textNoTasks);
        return;
    }

    const promises = tasks.map(task => {
        return taskBin(tasks, task, assignments, collaborators, tasksList, user, project);
    });

    // Esperamos a que se resuelvan las promesas de la función taskBin
    Promise.all(promises).then(() => {
        showContentTask();
        taskActionsMenu();
    });
}

// async function viewTask(task, session_id, project) {
//     let collaborators = await getCollaborators();
//     let assignments = await getAssign();
// }

function newTaskModal(tasks, user, project) {
    const taskModal = document.querySelector('#taskModal');
    taskModal.style.display = 'flex';

    const addTaskBtn = document.querySelector('#addTaskBtn');
    addTaskBtn.textContent = 'Enviar';

    document.querySelector('#modalLegend').textContent = 'Nueva tarea';
    cleanForm();

    // Indico que la fecha de entrega de la tarea sea como máximo la fecha de entrega del proyecto
    const taskDeadline = taskModal.querySelector('#taskDeadline');
    taskDeadline.max = project.deadline;

    // Utilizo una función anónima como manejador de eventos
    const handleAddTaskClick = async function (e) {
        e.preventDefault();
        let newTask = [];
        const taskTitle = document.querySelector('#taskTitle').value.trim();
        if (taskTitle === '') {
            // Si el nombre de la tarea está vacio se muestra un error
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        const taskDescription = document.querySelector('#taskDescription').value.trim();
        const taskDeadline = document.querySelector('#taskDeadline').value.trim();
        const taskPriority = document.querySelector('#taskPriority').value.trim();
        const taskStatus = document.querySelector('#taskStatus').value.trim();

        newTask = { 'title': taskTitle, 'description': taskDescription, 'deadline': taskDeadline, 'priority': taskPriority || "0", 'status': taskStatus, 'project_id': project.id };

        if (await addTask(newTask)) {
            let tasks = await getTasks();
            if (generalView) {
                showTasks(tasks, user, project);
                showCalendar(tasks, user, project);
            }
            if (kanbanView) {
                showTasksKanban();
            }
        }

        // const newTaskId = await addTask(newTask)
        // if (newTaskId !== false) {
        //     newTask.id = newTaskId;
        //     tasks.push(newTask);
        // }


        // Elimino el manejador de eventos después de su ejecución
        addTaskBtn.removeEventListener('click', handleAddTaskClick);
    };
    addTaskBtn.addEventListener('click', handleAddTaskClick);
}

function editTaskModal(tasks, task, user, project) {
    const taskModal = document.querySelector('#taskModal');
    taskModal.style.display = 'flex';

    const editTaskBtn = document.querySelector('#addTaskBtn');
    editTaskBtn.textContent = 'Actualizar tarea';

    document.querySelector('#modalLegend').textContent = 'Editar tarea';

    cleanForm();

    let taskTitle = document.querySelector('#taskTitle');
    taskTitle.value = task.title;
    let taskDescription = document.querySelector('#taskDescription');
    taskDescription.value = task.description;
    let taskDeadline = document.querySelector('#taskDeadline');
    taskDeadline.value = task.deadline;
    taskDeadline.max = project.deadline;
    let taskPriority = document.querySelector('#taskPriority');
    taskPriority.value = task.priority;
    let taskStatus = document.querySelector('#taskStatus').value = task.status;

    // Deshabilito los campos que el usuario no debería modificar al editar la tarea cuando la tiene asignada
    if (user.id !== project.user_id) {
        taskTitle.setAttribute('readonly', true);
        taskDescription.setAttribute('readonly', true);
        taskDeadline.setAttribute('readonly', true);
        taskPriority.disabled = true;
    }

    const handleAddTaskClick = async function (e) {
        e.preventDefault();
        taskTitle = document.querySelector('#taskTitle').value.trim();
        if (taskTitle === '') {
            // Si el nombre de la tarea está vacio se muestra un error
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        taskDescription = document.querySelector('#taskDescription').value.trim();
        taskDeadline = document.querySelector('#taskDeadline').value.trim();
        taskPriority = document.querySelector('#taskPriority').value.trim();
        taskStatus = document.querySelector('#taskStatus').value.trim();
        task = {
            'id': task.id,
            'project_id': task.project_id,
            'status': taskStatus,
            'title': taskTitle,
            'description': taskDescription,
            'deadline': taskDeadline,
            'priority': taskPriority
        };
        if (await updateTask(task)) {
            let tasks = await getTasks();
            if (generalView) {
                showTasks(tasks, user, project);
                showCalendar(tasks, user, project);
            }
            if (kanbanView) {
                showTasksKanban();
            }
            // const taskToUpdate = tasks.find(element => element.id === task.id);
            // // Si se encuentra el array asociativo
            // if (taskToUpdate) {
            //     // Actualiza los valores de los campos necesarios
            //     Object.assign(taskToUpdate, task);

            // } else {
            //     console.log('No se encontró el elemento con el id especificado.');
            // }
        }

        editTaskBtn.removeEventListener('click', handleAddTaskClick);
    };
    editTaskBtn.addEventListener('click', handleAddTaskClick);
}


// Limpia el formulario
function cleanForm() {
    document.querySelector('#taskTitle').value = '';
    document.querySelector('#taskDescription').value = '';
    document.querySelector('#taskDeadline').value = '';
    document.querySelector('#taskPriority').value = '';
}

// Consulta para añadir tarea
async function addTask(task) {
    // Construir la petición
    const data = new FormData();
    data.append('title', task.title);
    data.append('description', task.description);
    data.append('deadline', task.deadline);
    data.append('priority', task.priority);
    data.append('status', task.status);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/task`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            const taskModal = document.querySelector('#taskModal');
            taskModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            return true;
            return result.task_id;
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido añadir la tarea"
        });
        console.log(error);
        return false;
    }

}

// Consulta para actualizar tarea
async function updateTask(task) {
    // Construir la petición
    const data = new FormData();
    data.append('id', task.id);
    data.append('project_id', task.project_id);
    data.append('status', task.status);
    data.append('title', task.title);
    data.append('description', task.description);
    data.append('deadline', task.deadline);
    data.append('priority', task.priority);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/task/update`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            const taskModal = document.querySelector('#taskModal');
            taskModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            return true;
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar la tarea"
        });
        console.log(error);
        return false;
    }

}

// Consulta para eliminar tarea
async function deleteTask(task) {
    // Construir la petición
    const data = new FormData();
    data.append('id', task.id);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/task/delete`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            const taskModal = document.querySelector('#taskModal');
            taskModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            return true;
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar la tarea"
        });
        console.log(error);
        return false;
    }

}