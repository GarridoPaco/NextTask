/**
 * Muestra las tareas en la interfaz de usuario.
 * @param {Array} tasks - Lista de tareas a mostrar.
 * @param {Object} user - Información del usuario.
 * @param {Object} project - Información del proyecto actual.
 */
async function showTasks(tasks, user, project) {
    // Obtener la lista de colaboradores y asignaciones
    let collaborators = await getCollaborators();
    let assignments = await getAssign();
    // Elemento HTML que contiene la lista de tareas
    const tasksList = document.querySelector('#tasks-list');
    tasksList.innerHTML = '';
    /**
     * Verificar si no hay tareas disponibles, 
     * se muestra un mensaje al usuario
     */
    if (tasks.length === 0) {
        const textNoTasks = document.createElement('P');
        textNoTasks.textContent = '¡Es hora de dar un paso adelante en tu proyecto! Agrega nuevas tareas para alcanzar tus objetivos. ¡Cada tarea te acerca un paso más hacia el éxito! Haz clic en el botón de abajo para empezar a añadir tareas ahora mismo.';
        textNoTasks.classList.add('no-tasks');
        tasksList.appendChild(textNoTasks);
        return;
    }
    // Mapear cada tarea a una promesa que ejecuta la función taskBin
    const promises = tasks.map(task => {
        return taskBin(task, assignments, collaborators, tasksList, user, project);
    });

    // Esperar a que se resuelvan todas las promesas
    Promise.all(promises).then(() => {
        showContentTask();
        taskActionsMenu();
    });
}

/**
 * Abre el modal para añadir una nueva tarea.
 * @param {Object} user - Información del usuario actual.
 * @param {Object} project - Información del proyecto actual.
 */
function newTaskModal(user, project) {
    // Mostrar el modal para añadir tarea
    const taskModal = document.querySelector('#taskModal');
    taskModal.style.display = 'flex';

    // Configurar el botón de añadir tarea
    const addTaskBtn = document.querySelector('#addTaskBtn');
    addTaskBtn.textContent = 'Enviar';

    // Configurar el título del modal
    document.querySelector('#modalLegend').textContent = 'Nueva tarea';
    // Limpiar el formulario
    cleanForm();

    // Establecer la fecha máxima de entrega de la tarea como la fecha de entrega del proyecto
    const taskDeadline = taskModal.querySelector('#taskDeadline');
    taskDeadline.max = project.deadline;

    // Manejador de eventos para el botón de añadir tarea
    const handleAddTaskClick = async function (e) {
        e.preventDefault();
        let newTask = [];

        // Obtener los valores del formulario
        const taskTitle = document.querySelector('#taskTitle').value.trim();
        if (taskTitle === '') {
            // Mostrar un error si el nombre de la tarea está vacío
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        const taskDescription = document.querySelector('#taskDescription').value.trim();
        const taskDeadline = document.querySelector('#taskDeadline').value.trim();
        if (taskDeadline === '') {
            // Mostrar un error si la fecha de la tarea está vacía
            showAlert('Introduce una fecha de entrega', 'error');
            return;
        }
        const taskPriority = document.querySelector('#taskPriority').value.trim();
        const taskStatus = document.querySelector('#taskStatus').value.trim();

        // Crear el objeto de la nueva tarea
        newTask = { 'title': taskTitle, 'description': taskDescription, 'deadline': taskDeadline, 'priority': taskPriority || "0", 'status': taskStatus, 'project_id': project.id };

        // Añadir la tarea a través de la función addTask
        if (await addTask(newTask)) {
            // Actualizar la lista de tareas y el calendario según la vista actual
            let tasks = await getTasks();
            if (generalView) {
                showTasks(tasks, user, project);
                showCalendar(tasks, user, project);
            }
            if (kanbanView) {
                showTasksKanban();
            }
        }

        // Eliminar el manejador de eventos después de su ejecución
        addTaskBtn.removeEventListener('click', handleAddTaskClick);
    };
    addTaskBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Abre el modal para editar la información de una tarea.
 * @param {Object} task - Objeto que contiene la información de la tarea a editar.
 * @param {Object} user - Objeto que representa al usuario que realiza la edición.
 * @param {Object} project - Objeto que contiene la información del proyecto al que pertenece la tarea.
 */
function editTaskModal(task, user, project) {
    // Mostrar el modal de edición de la tarea
    const taskModal = document.querySelector('#taskModal');
    taskModal.style.display = 'flex';

    // Configurar el botón de edición de la tarea en el modal
    const editTaskBtn = document.querySelector('#addTaskBtn');
    editTaskBtn.textContent = 'Actualizar tarea';

    // Establecer el título del modal
    document.querySelector('#modalLegend').textContent = 'Editar tarea';

    // Limpiar el formulario del modal
    cleanForm();

    // Establecer los valores actuales de la tarea en los campos del formulario
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

    // Deshabilitar los campos que el usuario que no es propietario del proyecto no debe modificar al editar la tarea
    if (user.id !== project.user_id) {
        taskTitle.setAttribute('readonly', true);
        taskDescription.setAttribute('readonly', true);
        taskDeadline.setAttribute('readonly', true);
        taskPriority.disabled = true;
    }

    // Manejador de evento para la acción de actualización de la tarea
    const handleAddTaskClick = async function (e) {
        e.preventDefault();
        // Obtener los nuevos valores de la tarea desde los campos del formulario
        taskTitle = document.querySelector('#taskTitle').value.trim();
        // Validar que el nombre de la tarea no esté vacío
        if (taskTitle === '') {
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        taskDescription = document.querySelector('#taskDescription').value.trim();
        taskDeadline = document.querySelector('#taskDeadline').value.trim();
        taskPriority = document.querySelector('#taskPriority').value.trim();
        taskStatus = document.querySelector('#taskStatus').value.trim();
        // Crear el objeto de tarea actualizada con los nuevos valores
        task = {
            'id': task.id,
            'project_id': task.project_id,
            'status': taskStatus,
            'title': taskTitle,
            'description': taskDescription,
            'deadline': taskDeadline,
            'priority': taskPriority
        };
        // Actualizar la tarea y realizar las acciones correspondientes según la vista
        if (await updateTask(task)) {
            let tasks = await getTasks();
            if (generalView) {
                showTasks(tasks, user, project);
                showCalendar(tasks, user, project);
            }
            if (kanbanView) showTasksKanban();
        }
        // Eliminar el manejador de eventos después de su ejecución
        editTaskBtn.removeEventListener('click', handleAddTaskClick);
    };
    // Agregar el manejador de evento al botón de actualización de la tarea
    editTaskBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Limpia los campos del formulario en el modal de tarea.
 */
function cleanForm() {
    document.querySelector('#taskTitle').value = '';
    document.querySelector('#taskDescription').value = '';
    document.querySelector('#taskDeadline').value = '';
    document.querySelector('#taskPriority').value = '';
}

/**
 * Realiza una consulta para añadir una nueva tarea al proyecto.
 * @param {Object} task - Objeto que contiene la información de la tarea a añadir.
 * @returns {boolean} - Devuelve true si la tarea se añade correctamente.
 */
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
            // Devuelve true si la tarea se añade correctamente
            return true;
            // También puedes devolver el ID de la tarea añadida si es relevante
            // return result.task_id;
        }
    } catch (error) {
        // En caso de error, muestra una alerta y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido añadir la tarea"
        });
        console.log(error);
        return false;
    }
}

/**
 * Realiza una consulta para actualizar la información de una tarea.
 * @param {Object} task - Objeto que contiene la información actualizada de la tarea.
 * @returns {boolean} - Devuelve true si la tarea se actualiza correctamente.
 */
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
            // Devuelve true si la tarea se actualiza correctamente
            return true;
        }
    } catch (error) {
        // En caso de error, muestra una alerta y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar la tarea"
        });
        console.log(error);
        return false;
    }
}

/**
 * Realiza una consulta para eliminar una tarea.
 * @param {Object} task - Objeto que contiene la información de la tarea a eliminar.
 * @returns {boolean} - Devuelve true si la tarea se elimina correctamente.
 */
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
            // Devuelve true si la tarea se elimina correctamente
            return true;
        }
    } catch (error) {
        // En caso de error, muestra una alerta y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar la tarea"
        });
        console.log(error);
        return false;
    }
}