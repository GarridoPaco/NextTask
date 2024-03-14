// Constantes de control sobre la vista en la que nos encontramos
const generalView = false;
const kanbanView = true;
function closeModal() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(function (modal) {
        modal.style.display = 'none';
    });

}
async function showTasksKanban() {
    const loadingOverlay = document.getElementById('loading-overlay');
    try {
        const tasks = await getTasks();
        const collaborators = await getCollaborators();
        selectCollaborators(collaborators);
        const assignments = await getAssign();
        const user = await getUser();
        const project = await getProject();

        // Botón para mostrar el modal para añadir tarea
        const newTaskBtns = document.querySelectorAll('#addTask');
        newTaskBtns.forEach(function (newTaskBtn) {
            newTaskBtn.onclick = function () {
                newTaskModal(tasks, user, project);
            }
        });

        const tasksListToDo = document.querySelector('#tasksListToDo');
        tasksListToDo.innerHTML = "";
        const tasksListInProgress = document.querySelector('#tasksListInProgress');
        tasksListInProgress.innerHTML = "";
        const tasksListFinish = document.querySelector('#tasksListFinish');
        tasksListFinish.innerHTML = "";

        const promises = tasks.map(task => {
            switch (task.status) {
                case "0":
                    return taskBin(tasks, task, assignments, collaborators, tasksListToDo, user, project);
                case "1":
                    return taskBin(tasks, task, assignments, collaborators, tasksListInProgress, user, project);
                case "2":
                    return taskBin(tasks, task, assignments, collaborators, tasksListFinish, user, project);
            }
        });
        // Espero a que todas las Promesas se resuelvan
        Promise.all(promises).then(() => {
            showContentTask();
            taskActionsMenu();
            // Ahora puedo seleccionar los checkbox agregados al DOM
            const checkTasks = document.querySelectorAll('.checkTask');

            checkTasks.forEach(checkTask => {
                checkTask.addEventListener('click', async function () {
                    // Obtener el ID de la tarea asociada al checkbox
                    const taskId = this.closest('.taskContainer').dataset.taskId;

                    // Encontrar la tarea correspondiente en el array de tareas
                    const task = tasks.find(task => task.id === taskId);

                    // Aumentar en 1 la propiedad 'status'
                    task.status = (task.status + 1) % 3; // Esto asume que 'status' puede ser 0, 1 o 2

                    await updateTask(task);
                    await showTasksKanban();
                });
            });
            loadingOverlay.style.display = 'none';
        });
    } catch (error) {
        loadingOverlay.style.display = 'none';
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido cargar la pagina'
        });
        console.log(error);
    }
}
showTasksKanban();