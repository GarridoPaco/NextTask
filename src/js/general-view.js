/**
 * Constantes de control sobre la vista en la que nos encontramos.
 * 
 * Estas constantes se utilizan para controlar la vista actual de la aplicación.
 * `generalView` indica si la vista actual es la vista general.
 * `kanbanView` indica si la vista actual es la vista de kanban.
 */
const generalView = true;
const kanbanView = false;

/**
 * Función principal que se ejecuta al cargar la página.
 * 
 * Esta función realiza varias tareas al cargar la página, incluyendo la obtención de datos del proyecto,
 * colaboradores, usuarios y tareas, así como la visualización de estos datos en la interfaz de usuario.
 * También gestiona eventos de botones y elementos interactivos en la interfaz de usuario.
 */
async function main() {
    const loadingOverlay = document.getElementById('loading-overlay');
    try {

        loadingOverlay.style.display = 'flex';

        // Obtener datos del proyecto, colaboradores, usuarios y tareas
        let project = await getProject();
        let collaborators = await getCollaborators();
        let users = await getUsers();
        let tasks = await getTasks();
        let user = await getUser();

        // Mostrar el proyecto, tareas, colaboradores, usuarios y calendario en la interfaz de usuario
        await showProject(project);
        await showTasks(tasks, user, project);
        await showCollaborators(project, users, collaborators);
        await showUsers(users, collaborators);
        await showCalendar(tasks, user, project);

        // Configurar eventos de los botones y elementos interactivos
        // Botón para mostrar el modal para editar el proyecto
        const editProjectBtn = document.querySelector('.editProjectBtn');
        editProjectBtn.onclick = function () {
            editModalProject(tasks, user, project);
        }

        // Botón para eliminar el proyecto
        const deleteProjectBtn = document.querySelector('.deleteProjectBtn');
        deleteProjectBtn.onclick = function () {
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar el proyecto?",
                text: "Esta operación no es reversible",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Borrar"
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteProject();
                }
            });

        }

        // Botón para mostrar el modal para añadir tarea
        const newTaskBtn = document.querySelector('#addTask');
        newTaskBtn.onclick = function () {
            newTaskModal(user, project);
        }

        // Botón para mostrar el modal de los colaboradores
        let openModalCollaborators = document.querySelector('#openModalCollaborators');
        openModalCollaborators.addEventListener('click', function () {
            collaboratorsModal(users, project);
        });

        // Botón para enviar una invitación
        const invitationBtn = document.getElementById('invitationBtn');
        invitationBtn.onclick = function (e) {
            e.preventDefault();
            invitation();
        }
        loadingOverlay.style.display = 'none';
    } catch (error) {
        // En caso de error, muestra un mensaje de error y redirecciona al usuario a su página de inicio
        loadingOverlay.style.display = 'none';
        Swal.fire({
            title: "No se ha podido cargar el proyecto",
            text: "Inténtelo de nuevo en unos instantes",
            icon: "error",
            confirmButtonColor: "#0075beff"
        }).then(() => {
            window.location.href = "/dashboard";
        });
        
        // Difuminado del fondo al mostrar el mensaje de alerta
        const alertBackground = document.querySelector('.swal2-container');
        alertBackground.style.backdropFilter = "blur(4px)";
        console.log(error);
    }
}
main();

