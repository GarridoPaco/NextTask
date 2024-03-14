// Constantes de control sobre la vista en la que nos encontramos
const generalView = true;
const kanbanView = false;

async function main() {
    try {
        const loadingOverlay = document.getElementById('loading-overlay');

        loadingOverlay.style.display = 'flex';

        let project = await getProject();
        let collaborators = await getCollaborators();
        let users = await getUsers();
        let tasks = await getTasks();
        let user = await getUser();
        
        await showProject(project);
        await showTasks(tasks, user, project);
        await showCollaborators(project, users, collaborators);
        await showUsers(users, collaborators);
        await showCalendar(tasks, user, project);

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
            newTaskModal(tasks, user, project);
        }

        // Botón para mostrar el modal de los colaboradores
        let openModalCollaborators = document.querySelector('#openModalCollaborators');
        // Agregar el primer manejador de eventos
        openModalCollaborators.addEventListener('click', function () {
            collaboratorsModal(users, project);
        });

        // Agregar el segundo manejador de eventos
        // openModalCollaborators.addEventListener('click', function () {
        //     showUsers(users, collaborators);
        // });

        // Botón para enviar una invitación
        const invitationBtn = document.getElementById('invitationBtn');
        invitationBtn.onclick = function (e) {
            e.preventDefault();
            invitation();
        }
        loadingOverlay.style.display = 'none';
    } catch (error) {
        loadingOverlay.style.display = 'none';
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido cargar la pagina'
        });
        console.log(error);
    }
}
main();

