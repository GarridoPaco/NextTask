/**
 * Constantes de control sobre la vista en la que nos encontramos.
 * `generalView` se establece en `false` para indicar que no estamos en la vista general.
 * `kanbanView` se establece en `true` para indicar que estamos en la vista de kanban.
 */
const generalView = false;
const kanbanView = true;

/**
 * Función para cerrar todos los modales mostrados.
 */
function closeModal() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(function (modal) {
        modal.style.display = 'none';
    });

}

/**
 * Función asincrónica para mostrar las tareas en la vista kanban.
 * Se obtienen las tareas, colaboradores, asignaciones y el usuario actual del proyecto,
 * y se muestra la información de las tareas en las columnas correspondientes.
 */
async function showTasksKanban() {
    const loadingOverlay = document.getElementById('loading-overlay');
    try {
        const tasks = await getTasks();
        const collaborators = await getCollaborators();
        const assignments = await getAssign();
        const user = await getUser();
        const project = await getProject();
        selectCollaborators(collaborators);

        // Botón para mostrar el modal para añadir tarea
        const newTaskBtns = document.querySelectorAll('#addTask');
        newTaskBtns.forEach(function (newTaskBtn) {
            newTaskBtn.onclick = function () {
                newTaskModal(user, project);
            }
        });

        // Limpiar las listas de tareas en cada columna
        const tasksListToDo = document.querySelector('#tasksListToDo');
        tasksListToDo.innerHTML = "";
        const tasksListInProgress = document.querySelector('#tasksListInProgress');
        tasksListInProgress.innerHTML = "";
        const tasksListFinish = document.querySelector('#tasksListFinish');
        tasksListFinish.innerHTML = "";

        // Mapear las tareas a las columnas correspondientes según su estado
        const promises = tasks.map(task => {
            switch (task.status) {
                case "0":
                    return taskBin(task, assignments, collaborators, tasksListToDo, user, project);
                case "1":
                    return taskBin(task, assignments, collaborators, tasksListInProgress, user, project);
                case "2":
                    return taskBin(task, assignments, collaborators, tasksListFinish, user, project);
            }
        });

        // Esperar a que todas las promesas se resuelvan antes de continuar
        Promise.all(promises).then(() => {
            showContentTask();
            taskActionsMenu();
            // Ahora puedo seleccionar los checkbox agregados al DOM
            const checkTasks = document.querySelectorAll('.checkTask');

            // Manejar el evento click en los checkbox de las tareas
            checkTasks.forEach(checkTask => {
                checkTask.addEventListener('click', async function () {
                    // Obtener el ID de la tarea asociada al checkbox
                    const taskId = this.closest('.taskContainer').dataset.taskId;

                    // Encontrar la tarea correspondiente en el array de tareas
                    const task = tasks.find(task => task.id === taskId);

                    // Aumentar en 1 la propiedad 'status'
                    task.status = (task.status + 1) % 3; // Esto asume que 'status' puede ser 0, 1 o 2

                    // Actualizar la tarea en la base de datos y mostrar nuevamente las tareas
                    await updateTask(task);
                    await showTasksKanban();
                });
            });
            loadingOverlay.style.display = 'none';
        });
    } catch (error) {
        // Manejar errores mostrando un mensaje al usuario y redireccionándolo si es necesario
        loadingOverlay.style.display = 'none';
        Swal.fire({
            title: "No se ha podido cargar el proyecto",
            text: "Inténtelo de nuevo en unos instantes",
            icon: "error",
            confirmButtonColor: "#0075beff"
        }).then(() => {
            // Redireccionar al usuario a su página de inicio en caso de error
            window.location.href = "/dashboard";
        });
        console.log(error);
    }
}
// Mostrar las tareas en la vista kanban al cargar la página
showTasksKanban();