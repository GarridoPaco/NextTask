// Menú mobile
const mobileMenuBtn = document.querySelector('#mobileMenuBtn');
const mobileCloseBtn = document.querySelector('#mobileCloseBtn');
const sidebar = document.querySelector('.sidebar');
if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function () {
        sidebar.classList.add('show');
        console.log('menu mobile');
    });
}
if (mobileCloseBtn) {
    mobileCloseBtn.addEventListener('click', function () {
        sidebar.classList.add('hide');
        sidebar.classList.remove('show');
        sidebar.classList.remove('hide');
    })
}

// Elimina la clase de mostrar, en un tamaño de tablet y mayores
const anchoPantalla = document.body.clientWidth;

window.addEventListener('resize', function () {
    const anchoPantalla = document.body.clientWidth;
    if (anchoPantalla >= 768) {
        sidebar.classList.remove('show');
    }
})

// Estilos para la barra de usuario
const navViews = document.querySelector('#navViews');
const barra = document.querySelector('.barra');
if (!navViews) {
    barra.style.borderRadius = '1rem';
}
// Botón para cerrar el modal
const closeModalBtns = document.querySelectorAll('.closeModal');
closeModalBtns.forEach(function (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeModal);
});

// Función para formatear las fechas
function formatDate(date) {
    // Formatear la fecha en español
    const dateObj = new Date(date);
    const month = dateObj.getMonth();
    const day = dateObj.getDate();
    const year = dateObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, month, day));

    const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
    return fechaUTC.toLocaleDateString('es-Mx', optionsDate);
}


// Función para cerrar los modales de los formularios
function closeModal() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(function (modal) {
        modal.style.display = 'none';
    });

}

// Función para desplegar la info de la tarea
function showContentTask() {
    const toggleBtns = document.querySelectorAll('.toggleBtn');
    toggleBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const content = this.nextElementSibling;
            content.classList.toggle('active');

            // Cambiar el src del icono dependiendo de si la clase active está presente o no
            if (content.classList.contains('active')) {
                btn.src = 'build/img/up_arrow_icon.svg';
            } else {
                btn.src = 'build/img/down_arrow_icon.svg';
            }
        });
    });
}

// Función para mostrar ocultar los botones de acciones de las tareas
function taskActionsMenu() {
    const taskActionsBtns = document.querySelectorAll('.taskActionsBtn');
    taskActionsBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const taskActions = this.nextElementSibling;
            taskActions.classList.toggle('active');

            // Cambiar el src del icono dependiendo de si la clase active está presente o no
            if (taskActions.classList.contains('active')) {
                btn.src = 'build/img/right_arrow_icon.svg';
            } else {
                btn.src = 'build/img/dots_three_circle_icon.svg';
            }
        });
    });
}


// Muestra un mensaje en la interfaz
function showAlert(mensaje, tipo) {
    // Previene la creación de multiples alertas
    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia) {
        alertaPrevia.remove();
    }


    const alerta = document.createElement('DIV');
    alerta.classList.add('alerta', tipo);
    alerta.textContent = mensaje;

    // Inserta la alerta despues del legend
    const legend = document.querySelector('legend');
    legend.parentElement.insertBefore(alerta, legend.nextElementSibling);

    // Eliminar la alerta después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}

// Función para generar el contenedor de las tareas
async function taskBin(tasks, task, assignments, collaborators, ancla, user, project) {
    return new Promise(async (resolve) => {
        const containerTask = document.createElement('LI');
        containerTask.dataset.taskId = task.id;
        containerTask.classList.add('taskContainer');

        // Checkbox para cambiar el estado de la tarea
        const checkTask = document.createElement('input');
        checkTask.type = 'checkbox';
        checkTask.classList.add('checkTask');
        checkTask.disabled = true;

        // Cabecera de la tarea
        const taskHeader = document.createElement('DIV');
        taskHeader.classList.add('taskHeader');
        taskHeader.appendChild(checkTask);

        // Nombre de la tarea
        const taskName = document.createElement('P');
        taskName.textContent = task.title;
        taskName.classList.add('taskName');
        taskHeader.appendChild(taskName);

        // Botón para desplegar/ocultar el contenido de la tarea
        const toggleBtn = document.createElement('IMG');
        toggleBtn.classList.add('toggleBtn');
        toggleBtn.classList.add('actionImg');
        toggleBtn.alt = 'Desplegar contenido';
        toggleBtn.title = 'Mostrar información adicional';
        toggleBtn.src = 'build/img/down_arrow_icon.svg';

        // Contenedor del contenido de la tarea
        const taskContentContainer = document.createElement('DIV');
        taskContentContainer.classList.add('taskContentContainer');

        // Información varia de la tarea
        const taskInfo = document.createElement('DIV');
        taskInfo.classList.add('taskInfo');

        // Descripción de la tarea
        const taskDescription = document.createElement('DIV');
        taskDescription.classList.add('taskDescription');

        const taskDescriptionIcon = document.createElement('IMG');
        taskDescriptionIcon.classList.add('taskDescriptionIcon');
        taskDescriptionIcon.alt = 'Descripción de la tarea';
        taskDescriptionIcon.src = 'build/img/info_icon.svg';
        taskDescription.appendChild(taskDescriptionIcon);

        const taskDescriptionText = document.createElement('P');
        taskDescriptionText.classList.add('taskDescriptionText');
        if (task.description === '') {
            taskDescriptionText.textContent = 'Sin descripción';
        } else {
            taskDescriptionText.textContent = task.description;
        }
        taskDescription.appendChild(taskDescriptionText);

        taskInfo.appendChild(taskDescription);

        // Prioridad de la tarea
        const taskPriority = document.createElement('DIV');
        taskPriority.classList.add('taskPriority');

        const taskPriorityColor = document.createElement('DIV');
        taskPriorityColor.classList.add('taskPriorityColor');
        const optionsPriorityColor = ['#82E0AA', '#F7DC6F', '#F1948A'];
        taskPriorityColor.style.backgroundColor = optionsPriorityColor[task.priority];
        taskPriority.appendChild(taskPriorityColor);

        const taskPriorityText = document.createElement('P');
        taskPriorityText.classList.add('taskPriorityText');
        const optionsPriorityText = ['Baja', 'Normal', 'Alta'];
        taskPriorityText.textContent = `Prioridad ${optionsPriorityText[task.priority]}`;
        taskPriority.appendChild(taskPriorityText);

        taskInfo.appendChild(taskPriority);

        // Estado de la tarea
        const taskStatus = document.createElement('DIV');
        taskStatus.classList.add('taskStatus');

        const taskStatusIcon = document.createElement('IMG');
        taskStatusIcon.classList.add('taskStatusIcon');
        const optionsIconStatus = ['build/img/pendiente_icon.svg',
            'build/img/progress_icon.svg',
            'build/img/complete_icon.svg'];
        taskStatusIcon.alt = 'Imagen del estado de la tarea';
        taskStatusIcon.src = optionsIconStatus[task.status];
        taskStatus.appendChild(taskStatusIcon);

        const taskStatusText = document.createElement('P');
        taskStatusText.classList.add('taskStatusText');
        const optionsStatusText = ['Pendiente', 'En Progreso', 'Finalizada'];
        taskStatusText.textContent = `${optionsStatusText[task.status]}`;
        taskStatus.appendChild(taskStatusText);

        taskInfo.appendChild(taskStatus);

        // Contenedor del menú de las acciones de las tareas
        const taskActionsContainer = document.createElement('DIV');
        taskActionsContainer.classList.add('taskActionsContainer');

        // Menú de opciones de las tareas
        const taskActions = document.createElement('DIV');
        taskActions.classList.add('taskActions');

        // Botón para mostrar los botones de las acciones
        const taskActionsBtn = document.createElement('IMG');
        taskActionsBtn.title = "Menú de acciones";
        taskActionsBtn.src = 'build/img/dots_three_circle_icon.svg';
        taskActionsBtn.classList.add('taskActionsBtn');
        taskActionsBtn.classList.add('actionImg');

        // Botón de editar tarea
        const editTaskBtn = document.createElement('IMG');
        editTaskBtn.title = "Editar tarea";
        editTaskBtn.src = 'build/img/edit_icon.svg';
        editTaskBtn.classList.add('editTaskBtn');
        editTaskBtn.classList.add('actionImg');
        editTaskBtn.onclick = function () {
            editTaskModal(tasks, task, user, project);
        };

        // Botón de eliminar tarea
        const deleteTaskBtn = document.createElement('IMG');
        deleteTaskBtn.title = "Eliminar tarea";
        deleteTaskBtn.src = 'build/img/delete_icon.svg';
        deleteTaskBtn.classList.add('deleteTaskBtn');
        deleteTaskBtn.classList.add('actionImg');
        deleteTaskBtn.onclick = function () {
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar la tarea?",
                text: "Esta operación no es reversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Borrar"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    if (await deleteTask(task)) {
                        let tasks = await getTasks();
                        if (generalView) {
                            showTasks(tasks, user, project);
                            showCalendar(tasks, user, project);
                        }
                        if (kanbanView) {
                            showTasksKanban();
                        }
                        // tasks = tasks.filter(deletedTask => deletedTask.id !== task.id);
                        // if (generalView) {
                        //     showTasks(tasks, user, project);
                        //     showCalendar(tasks, user, project);
                        // }
                        // if (kanbanView) {
                        //     showTasksKanban();
                        // }
                    }
                }
            });
        };

        // Botón de asignar
        const assignTaskBtn = document.createElement('IMG');
        assignTaskBtn.title = "Asignar tarea";
        assignTaskBtn.src = 'build/img/assign_icon.svg';
        assignTaskBtn.classList.add('assignTaskBtn');
        assignTaskBtn.classList.add('actionImg');
        assignTaskBtn.onclick = function () {
            assignmentsModal(task, user, project);
        };

        // Botón de añadir comentario
        const commentTaskBtn = document.createElement('IMG');
        commentTaskBtn.title = "Añadir comentario";
        commentTaskBtn.src = 'build/img/comment_icon.svg';
        commentTaskBtn.classList.add('commentTaskBtn');
        commentTaskBtn.classList.add('actionImg');
        commentTaskBtn.onclick = function () {
            newCommentsModal(task.id, user, project);
        };

        // Contenedor de las asignaciones
        const taskAssignContainer = document.createElement('DIV');
        taskAssignContainer.classList.add('taskAssignContainer');
        taskAssignContainer.dataset.task_id = task.id;

        const taskAssignTitle = document.createElement('H4');
        taskAssignTitle.classList.add('taskAssignTitle');
        taskAssignTitle.textContent = 'Asignado a:';
        const taskCollaboratorsContainer = document.createElement('DIV');
        taskCollaboratorsContainer.classList.add('taskCollaboratorsContainer');
        taskAssignContainer.appendChild(taskAssignTitle);
        taskAssignContainer.appendChild(taskCollaboratorsContainer);

        // Comprobación de si la tarea tiene alguna asignación para imprimir el contenedor de las asignaciones
        let taskCollaborators = new Array;
        if (assignments.some(function (assignment) { return assignment.task_id === task.id; })) {
            assignments.forEach(assignment => {
                if (assignment.task_id === task.id) {
                    taskCollaborators.push(assignment.user_id);
                }
                viewAssignment(user, project, task, assignment, collaborators, taskCollaboratorsContainer);
            });
        } else {
            taskAssignContainer.style.display = 'none';
        }

        // Contenedor de los comentarios
        const taskCommentsContainer = document.createElement('DIV');
        taskCommentsContainer.classList.add('taskComments');

        const comments = await getComments(task.id);

        if (comments.length) {
            const taskCommentsTitle = document.createElement('H4');
            taskCommentsTitle.classList.add('taskCommentsTitle');
            taskCommentsTitle.textContent = 'Comentarios:';
            taskCommentsContainer.appendChild(taskCommentsTitle);

            comments.forEach(comment => {
                viewComment(comment, taskCommentsContainer, user, project);
            });
        }

        taskActions.appendChild(deleteTaskBtn);
        taskActions.appendChild(editTaskBtn);
        taskActions.appendChild(assignTaskBtn);
        taskActions.appendChild(commentTaskBtn);
        taskActionsContainer.appendChild(taskActionsBtn);
        taskActionsContainer.appendChild(taskActions);
        containerTask.appendChild(taskActionsContainer);

        containerTask.appendChild(taskHeader);
        containerTask.appendChild(toggleBtn);
        containerTask.appendChild(taskContentContainer);
        taskContentContainer.appendChild(taskInfo);
        taskContentContainer.appendChild(taskAssignContainer);
        taskContentContainer.appendChild(taskCommentsContainer);
        ancla.appendChild(containerTask);

        // Validación para mostrar las botones de acción de la tarea
        if (user.id !== project.user_id) {
            taskActionsContainer.style.display= 'none';
            deleteTaskBtn.style.display = 'none';
            editTaskBtn.style.display = 'none';
            commentTaskBtn.style.display = 'none';
            assignTaskBtn.style.display = 'none';
            const addTaskBtns = document.querySelectorAll('#addTask');
            addTaskBtns.forEach(addTaskBtn => {
                addTaskBtn.style.display = 'none';
            });
        }
        // Validación que el usuario sea colaborador de la tarea
        if (taskCollaborators.includes(user.id) || project.user_id === user.id) {
            checkTask.disabled = false;
            taskActionsContainer.style.display= 'block';
            editTaskBtn.style.display = 'block';
            commentTaskBtn.style.display = 'block';
        }

        // Visualización de los checkbox dependiendo de la vista y del estado
        if (generalView) checkTask.style.display = 'none';

        if (task.status == 2) {
            checkTask.checked = true;
            checkTask.disabled = true;
        }
        // Resolve la Promesa después de agregar el elemento al DOM
        resolve(containerTask);
    });
}