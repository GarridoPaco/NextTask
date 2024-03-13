async function showProject(project) {
    document.querySelector('#projectName').textContent = project.name;
    document.querySelector('#projectDescription').textContent = project.description;
    document.querySelector('#projectDeadline').textContent = formatDate(project.deadline);
}


async function editModalProject(tasks, user, project) {

    const projectModal = document.querySelector('#projectModal');
    projectModal.style.display = 'flex';

    const editProjectBtn = document.querySelector('#addProjectBtn');
    editProjectBtn.textContent = 'Actualizar proyecto';

    document.querySelector('#modalLegendProject').textContent = 'Actualizar Proyecto';

    let projectNameInput = document.querySelector('#projectNameInput').value = project.name;
    let projectDescriptionInput = document.querySelector('#projectDescriptionInput').value = project.description;
    let projectDeadlineInput = document.querySelector('#projectDeadlineInput').value = project.deadline;

    const handleAddTaskClick = function (e) {
        e.preventDefault();
        projectNameInput = document.querySelector('#projectNameInput').value.trim();
        if (projectNameInput === '') {
            // Si el nombre del proyecto está vacio se muestra un error
            showAlert('El nombre es obligatorio', 'error');
            return;
        }
        projectDescriptionInput = document.querySelector('#projectDescriptionInput').value.trim();
        projectDeadlineInput = document.querySelector('#projectDeadlineInput').value.trim();
        projectEdit = {
            'id': project.id,
            'user_id': project.user_id,
            'name': projectNameInput,
            'description': projectDescriptionInput,
            'deadline': projectDeadlineInput
        };
        updateProject(tasks, user, projectEdit);
        editProjectBtn.removeEventListener('click', handleAddTaskClick);
    };
    editProjectBtn.addEventListener('click', handleAddTaskClick);
}

// Consulta para actualizar el proyecto
async function updateProject(tasks, user, project) {
    // Construir la petición
    const data = new FormData();
    data.append('id', project.id);
    data.append('user_id', project.user_id);
    data.append('name', project.name);
    data.append('description', project.description);
    data.append('deadline', project.deadline);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/project/update`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            const projectModal = document.querySelector('#projectModal');
            projectModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            showProject(project);
            showCalendar(tasks, user, project);
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar el proyecto"
        });
        console.log(error);
    }

}

// Consulta para eliminar el proyecto
async function deleteProject() {
    // Construir la petición
    const data = new FormData();
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/project/delete`;
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
            window.location.href = '/dashboard';
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar el proyecto"
        });
        console.log(error);
    }

}