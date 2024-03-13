function selectCollaborators(collaborators) {
    const selectAssignments = document.querySelector('#selectAssignment');

    selectAssignments.innerHTML = '<option value="" disabled selected style="display:none;">Seleccione un colaborador</option>';
    collaborators.forEach(collaborator => {

        // Select en el formulario para asignar tarea
        const assignmentOption = document.createElement('OPTION');
        assignmentOption.classList.add('assignmentOption');
        assignmentOption.value = collaborator.id;
        assignmentOption.textContent = collaborator.name + ' ' + collaborator.last_name;

        selectAssignments.appendChild(assignmentOption);
    });
}
async function showCollaborators(project, users, collaborators) {

    selectCollaborators(collaborators);
    const collaboratorsContainer = document.querySelector('#collaboratorsContainer');
    let collaboratorsList = document.querySelector('#collaboratorsList');
    collaboratorsList.innerHTML = "";
    if (collaborators.length === 0) {
        const textNoCollaborators = document.createElement('P');
        textNoCollaborators.textContent = 'No hay colaboradores asignados a este proyecto. Haz click en el botón de abajo para añadir colaboradores a tu proyecto.';
        textNoCollaborators.classList.add('no-collaborators');
        collaboratorsContainer.insertBefore(textNoCollaborators, collaboratorsList.nextSibling);
        return;
    }

    const textNoCollaborators = document.querySelector('.no-collaborators');
    if (textNoCollaborators) {
        textNoCollaborators.remove();
    }
    
    collaborators.forEach(collaborator => {
        // Lista de colaboradores
        const containerCollaborator = document.createElement('DIV');
        containerCollaborator.classList.add('containerCollaborator');

        const collaboratorInfo = document.createElement('DIV');
        collaboratorInfo.classList.add('collaboratorInfo');

        const collaboratorImg = document.createElement('IMG');
        collaboratorImg.classList.add('collaboratorImg');
        collaboratorImg.src = `build/img/${collaborator.image}.jpg`;


        const collaboratorName = document.createElement('P');
        collaboratorName.classList.add('collaboratorName');
        collaboratorName.textContent = collaborator.name + ' ' + collaborator.last_name;

        const deleteCollaboratorBtn = document.createElement('IMG');
        deleteCollaboratorBtn.title = "Eliminar colaborador";
        deleteCollaboratorBtn.src = 'build/img/delete_icon.svg';
        deleteCollaboratorBtn.classList.add('actionImg');
        deleteCollaboratorBtn.onclick = async function () {
            const assignments = await getAssign();
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar al colaborador?",
                text: "Esta operación no es reversible",
                icon: "warning",
                iconColor: "#0075beff",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Borrar"
            }).then((result) => {
                if (result.isConfirmed) {
                    assignments.forEach(assignment => {
                        if (assignment.user_id === collaborator.id && assignment.project_id === project.id) {
                            deleteAssign(assignment);
                        }
                    });
                    deleteCollaborator(users, collaborators, collaborator, project);
                }
            });
        };

        collaboratorsList.appendChild(containerCollaborator);
        containerCollaborator.appendChild(deleteCollaboratorBtn);
        containerCollaborator.appendChild(collaboratorInfo);
        collaboratorInfo.appendChild(collaboratorImg);
        collaboratorInfo.appendChild(collaboratorName);

    });
}

async function showUsers(users, collaborators) {

    var checkboxCollaborators = document.querySelector('#checkboxCollaborators');
    checkboxCollaborators.innerHTML = "";
    users.forEach(user => {
        // Checkbox en el formulario
        const checkboxCollaborator = document.createElement('DIV');
        checkboxCollaborator.classList.add('checkboxCollaborator');

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.name = "collaboratorCheckbox";
        checkbox.value = user.id;
        checkbox.id = `collaboratorCheckbox${user.id}`;
        // Utiliza el método find para buscar el objeto en el array
        let objetoEncontrado = collaborators.find(function (collaborador) {
            return collaborador.id === user.id;
        });

        // Verifica si se encontró el objeto
        if (objetoEncontrado) {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }

        const userLabel = document.createElement("label");
        userLabel.classList.add('userLabel');
        userLabel.htmlFor = `collaboratorCheckbox${user.id}`;

        const userImg = document.createElement('IMG');
        userImg.classList.add('userImg');
        userImg.src = `build/img/${user.image}.jpg`;

        const userName = document.createElement('P');
        userName.classList.add('userName');
        userName.textContent = `${user.name} ${user.last_name}`;


        userLabel.appendChild(userImg);
        userLabel.appendChild(userName);

        checkboxCollaborators.appendChild(checkboxCollaborator);
        checkboxCollaborator.appendChild(checkbox);
        checkboxCollaborator.appendChild(userLabel);
    });
}

async function collaboratorsModal(users, project) {
    let collaborators = await getCollaborators();
    const collaboratorModal = document.querySelector('#collaboratorModal');
    collaboratorModal.style.display = 'flex';

    const addCollaboratorBtn = document.querySelector('#addCollaboratorBtn');


    // Utilizo una función anónima como manejador de eventos
    const handleAddTaskClick = function (e) {
        e.preventDefault();
        const checkCollaborators = document.querySelectorAll('input[name="collaboratorCheckbox"]:checked');
        const usersId = [];

        // Iterar sobre los elementos option seleccionados y obtener sus valores
        for (let i = 0; i < checkCollaborators.length; i++) {
            usersId.push(checkCollaborators[i].value);
        }

        if (checkCollaborators.length === 0) {
            infoAction.fire({
                icon: "error",
                title: 'Selecccione algún colaborador'
            });
            return;
        }
        addCollaborator(usersId, users, collaborators, project);
        // Elimino el manejador de eventos después de su ejecución
        addCollaboratorBtn.removeEventListener('click', handleAddTaskClick);
    };
    addCollaboratorBtn.addEventListener('click', handleAddTaskClick);
}

// Consulta para añadir una colaboración
async function addCollaborator(usersId, users, collaborators, project) {
    // Convertir el array a una cadena separada por comas
    const usersIdString = usersId.join(',');
    // Construir la petición
    const data = new FormData();
    data.append('user_id', usersIdString);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/collaboration`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();
        showAlert(result.message, result.type);

        if (result.type === 'exito') {
            const collaboratorModal = document.querySelector('#collaboratorModal');
            collaboratorModal.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            const usersToCollaborators = usersId.map (userId => {
                return users.find(user => user.id === userId);
            });
            usersToCollaborators.forEach( userToCollaborator => {
                collaborators.push(userToCollaborator);
            });
            showCollaborators(project, users, collaborators);
            showUsers(users, collaborators);
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido añadir al colaborador'
        });
        console.log(error);
    }

}

// Consulta para eliminar colaboradores
async function deleteCollaborator(users, collaborators, collaborator, project) {
    // Construir la petición
    const data = new FormData();
    data.append('user_id', collaborator.id);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/collaboration/delete`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            collaborators = collaborators.filter(deleteCollaborator => deleteCollaborator.id !== collaborator.id);
            showCollaborators(project, users, collaborators);
            showUsers(users, collaborators);
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido realizar la asignación'
        });
        console.log(error);
    }

}

// Enviar invitación
async function invitation() {

    const invitationBtn = document.getElementById('invitationBtn');
    const loaderInvitation = document.getElementById('loader-invitation');

    loaderInvitation.style.display = 'block';
    invitationBtn.style.display = 'none';

    // Construir la petición
    const invitationEmail = document.getElementById('invitationEmail').value;
    const data = new FormData();
    data.append('invitationEmail', invitationEmail);

    try {
        const url = `${location.origin}/invitation`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();
        invitationBtn.style.display = 'block';
        loaderInvitation.style.display = 'none';
        if (result.type === 'exito') {
            document.getElementById('invitationEmail').value = '';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        } else {
            infoAction.fire({
                icon: "error",
                title: result.message
            });
        }
    } catch (error) {
        invitationBtn.style.display = 'block';
        loaderInvitation.style.display = 'none';
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido enviar el email'
        });
        console.log(error);
    }

}


