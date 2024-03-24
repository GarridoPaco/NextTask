/**
 * Actualiza las opciones del selector de colaboradores en un formulario de asignación de tareas.
 * @param {Array} collaborators - Arreglo de objetos que representan los colaboradores disponibles.
 */
function selectCollaborators(collaborators) {
    const selectAssignments = document.querySelector('#selectAssignment');

    // Reinicia el selector y agrega una opción predeterminada
    selectAssignments.innerHTML = '<option value="" disabled selected style="display:none;">Seleccione un colaborador</option>';

    // Itera sobre los colaboradores y agrega opciones al selector
    collaborators.forEach(collaborator => {

        // Crea una nueva opción para el colaborador
        const assignmentOption = document.createElement('OPTION');
        assignmentOption.classList.add('assignmentOption');
        assignmentOption.value = collaborator.id;
        assignmentOption.textContent = collaborator.name + ' ' + collaborator.last_name;
        // Agrega la opción al selector
        selectAssignments.appendChild(assignmentOption);
    });
}

/**
 * Muestra la lista de colaboradores asignados a un proyecto y proporciona la funcionalidad para eliminar colaboradores.
 * 
 * Selecciona colaboradores disponibles y los muestra en una lista en el contenedor especificado. 
 * Proporciona la opción de eliminar colaboradores asignados al proyecto, con una confirmación del usuario.
 * 
 * @param {object} project - Objeto que representa el proyecto al que se asignan los colaboradores.
 * @param {Array} users - Arreglo de objetos que representan a los usuarios disponibles.
 * @param {Array} collaborators - Arreglo de objetos que representan los colaboradores asignados al proyecto.
 */
async function showCollaborators(project, users, collaborators) {
    // Muestra en el select los colaboradores disponibles
    selectCollaborators(collaborators);

    // Contenedor de colaboradores
    const collaboratorsContainer = document.querySelector('#collaboratorsContainer');
    let collaboratorsList = document.querySelector('#collaboratorsList');
    collaboratorsList.innerHTML = "";

    // Si no hay colaboradores asignados al proyecto, muestra un mensaje indicando que no hay colaboradores
    if (collaborators.length === 0) {
        const textNoCollaborators = document.createElement('P');
        textNoCollaborators.textContent = 'No hay colaboradores asignados a este proyecto. Haz click en el botón de abajo para añadir colaboradores a tu proyecto.';
        textNoCollaborators.classList.add('no-collaborators');
        collaboratorsContainer.insertBefore(textNoCollaborators, collaboratorsList.nextSibling);
        return;
    }

    // Si hay colaboradores asignados, se eliminan mensajes anteriores indicando que no hay colaboradores
    const textNoCollaborators = document.querySelector('.no-collaborators');
    if (textNoCollaborators) {
        textNoCollaborators.remove();
    }

    // Itera sobre la lista de colaboradores asignados para mostrarlos en el contenedor
    collaborators.forEach(collaborator => {
        // Crea un contenedor para cada colaborador
        const containerCollaborator = document.createElement('DIV');
        containerCollaborator.classList.add('containerCollaborator');

        const collaboratorInfo = document.createElement('DIV');
        collaboratorInfo.classList.add('collaboratorInfo');

        const collaboratorImg = document.createElement('IMG');
        collaboratorImg.classList.add('collaboratorImg');
        collaboratorImg.src = `build/img/profile/${collaborator.image}.jpg`;


        const collaboratorName = document.createElement('P');
        collaboratorName.classList.add('collaboratorName');
        collaboratorName.textContent = collaborator.name + ' ' + collaborator.last_name;

        // Botón para eliminar el colaborador
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

        // Agrega los elementos al DOM
        collaboratorsList.appendChild(containerCollaborator);
        containerCollaborator.appendChild(deleteCollaboratorBtn);
        containerCollaborator.appendChild(collaboratorInfo);
        collaboratorInfo.appendChild(collaboratorImg);
        collaboratorInfo.appendChild(collaboratorName);

    });
}

/**
 * Llena el contenedor de checkboxes de colaboradores con los usuarios disponibles.
 * 
 * Esta función crea y muestra checkboxes para cada usuario en el sistema, permitiendo
 * seleccionarlos como colaboradores para un proyecto.
 * 
 * @param {Array} users - Arreglo de objetos que representan a los usuarios disponibles en el sistema.
 * @param {Array} collaborators - Arreglo de objetos que representan los colaboradores ya asignados a un proyecto.
 */
async function showUsers(users, collaborators) {

    // Limpia el contenedor de checkboxes de colaboradores
    var checkboxCollaborators = document.querySelector('#checkboxCollaborators');
    checkboxCollaborators.innerHTML = "";

    // Itera sobre los usuarios disponibles para mostrar checkboxes
    users.forEach(user => {
        // Crea un checkbox para el usuario
        const checkboxCollaborator = document.createElement('DIV');
        checkboxCollaborator.classList.add('checkboxCollaborator');

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.name = "collaboratorCheckbox";
        checkbox.value = user.id;
        checkbox.id = `collaboratorCheckbox${user.id}`;

        // Verifica si el usuario ya es un colaborador y lo deshabilita si es el caso
        let objetoEncontrado = collaborators.find(function (collaborador) {
            return collaborador.id === user.id;
        });
        checkbox.disabled = (objetoEncontrado) ? true : false;

        // Crea la etiqueta del usuario con su imagen y nombre
        const userLabel = document.createElement("label");
        userLabel.classList.add('userLabel');
        userLabel.htmlFor = `collaboratorCheckbox${user.id}`;

        const userImg = document.createElement('IMG');
        userImg.classList.add('userImg');
        userImg.src = `build/img/profile/${user.image}.jpg`;

        const userName = document.createElement('P');
        userName.classList.add('userName');
        userName.textContent = `${user.name} ${user.last_name}`;

        // Agrega los elementos al DOM
        userLabel.appendChild(userImg);
        userLabel.appendChild(userName);

        checkboxCollaborators.appendChild(checkboxCollaborator);
        checkboxCollaborator.appendChild(checkbox);
        checkboxCollaborator.appendChild(userLabel);
    });
}

/**
 * Abre el modal para agregar colaboradores al proyecto.
 * 
 * Esta función muestra un modal que permite agregar colaboradores al proyecto. 
 * Los usuarios disponibles se presentan como checkboxes, y al hacer clic en el botón de agregar,
 * se seleccionan los colaboradores marcados y se añaden al proyecto.
 * 
 * @param {Array} users - Arreglo de objetos que representan a los usuarios disponibles en el sistema.
 * @param {object} project - Objeto que representa el proyecto al que se agregarán los colaboradores.
 */
async function collaboratorsModal(users, project) {
    // Obtener los colaboradores actuales del proyecto
    let collaborators = await getCollaborators();

    // Mostrar el modal de colaboradores
    const collaboratorModal = document.querySelector('#collaboratorModal');
    collaboratorModal.style.display = 'flex';

    // Obtener el botón para agregar colaboradores
    const addCollaboratorBtn = document.querySelector('#addCollaboratorBtn');

    // Manejador de eventos para el clic en el botón de agregar colaboradores
    const handleAddTaskClick = function (e) {
        e.preventDefault();

        // Obtener los checkboxes de los colaboradores seleccionados
        const checkCollaborators = document.querySelectorAll('input[name="collaboratorCheckbox"]:checked');
        const usersId = [];

        // Iterar sobre los elementos option seleccionados y obtener sus valores
        for (let i = 0; i < checkCollaborators.length; i++) {
            usersId.push(checkCollaborators[i].value);
        }

        // Verificar si se seleccionó algún colaborador
        if (checkCollaborators.length === 0) {
            infoAction.fire({
                icon: "error",
                title: 'Selecccione algún colaborador'
            });
            return;
        }

        // Agregar los colaboradores seleccionados al proyecto
        addCollaborator(usersId, users, collaborators, project);

        // Eliminar el manejador de eventos después de su ejecución
        addCollaboratorBtn.removeEventListener('click', handleAddTaskClick);
    };
    // Agregar el manejador de eventos al botón de agregar colaboradores
    addCollaboratorBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Realiza una consulta para añadir colaboradores al proyecto.
 * 
 * Esta función envía una solicitud al servidor para agregar nuevos colaboradores al proyecto,
 * utilizando los IDs de los usuarios seleccionados. Después de recibir la respuesta del servidor,
 * actualiza la lista de colaboradores y usuarios disponibles en el proyecto.
 * 
 * @param {Array} usersId - Arreglo que contiene los IDs de los usuarios seleccionados para colaborar en el proyecto.
 * @param {Array} users - Arreglo de objetos que representan a todos los usuarios disponibles en el sistema.
 * @param {Array} collaborators - Arreglo de objetos que representan a los colaboradores actuales del proyecto.
 * @param {object} project - Objeto que representa el proyecto al que se agregarán los colaboradores.
 */
async function addCollaborator(usersId, users, collaborators, project) {
    // Convertir el array a una cadena separada por comas
    const usersIdString = usersId.join(',');

    // Construir la petición
    const data = new FormData();
    data.append('user_id', usersIdString);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/collaboration`;

        // Realizar la solicitud POST al servidor
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });

        // Obtener la respuesta del servidor en formato JSON
        const result = await answer.json();

        // Verificar si la operación fue exitosa
        if (result.type === 'exito') {
            // Ocultar el modal de colaboradores
            const collaboratorModal = document.querySelector('#collaboratorModal');
            collaboratorModal.style.display = 'none';

            // Mostrar una notificación de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });

            // Convertir los IDs de los usuarios seleccionados en objetos de colaboradores
            const usersToCollaborators = usersId.map(userId => {
                return users.find(user => user.id === userId);
            });

            // Agregar los nuevos colaboradores a la lista de colaboradores del proyecto
            usersToCollaborators.forEach(userToCollaborator => {
                collaborators.push(userToCollaborator);
            });

            // Actualizar la lista de colaboradores y usuarios disponibles en el proyecto
            showCollaborators(project, users, collaborators);
            showUsers(users, collaborators);
        }
    } catch (error) {
        // Mostrar una notificación de error en caso de fallo
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido añadir al colaborador'
        });
        console.log(error);
    }
}

/**
 * Realiza una consulta para eliminar un colaborador del proyecto.
 * 
 * Esta función realiza una petición para eliminar un colaborador específico del proyecto. 
 * Una vez completada la eliminación, actualiza la lista de colaboradores y usuarios mostrados en la interfaz.
 * 
 * @param {Array} users - Arreglo de objetos que representan a los usuarios disponibles en el sistema.
 * @param {Array} collaborators - Arreglo de objetos que representan a los colaboradores actuales del proyecto.
 * @param {object} collaborator - Objeto que representa al colaborador que se eliminará del proyecto.
 * @param {object} project - Objeto que representa al proyecto del cual se eliminará el colaborador.
 */
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

        // Verificar si la eliminación fue exitosa
        if (result.type === 'exito') {
            // Mostrar mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
            // Filtrar el arreglo de colaboradores para eliminar al colaborador eliminado
            collaborators = collaborators.filter(deleteCollaborator => deleteCollaborator.id !== collaborator.id);
            // Actualizar la lista de colaboradores mostrada en la interfaz
            showCollaborators(project, users, collaborators);
            // Actualizar la lista de usuarios mostrada en la interfaz
            showUsers(users, collaborators);
        }
    } catch (error) {
        // Mostrar mensaje de error en caso de fallo en la eliminación
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido realizar la asignación'
        });
        console.log(error);
    }
}

/**
 * Envia una invitación para colaborar en el proyecto por correo electrónico.
 * 
 * Esta función se encarga de enviar una invitación por correo electrónico a la dirección especificada.
 * Muestra un indicador de carga mientras se realiza la operación y muestra un mensaje de éxito o error según el resultado.
 */
async function invitation() {

    const invitationBtn = document.getElementById('invitationBtn');
    const loaderInvitation = document.getElementById('loader-invitation');

    // Mostrar indicador de carga y ocultar el botón de invitación
    loaderInvitation.style.display = 'block';
    invitationBtn.style.display = 'none';

    // Obtener la dirección de correo electrónico ingresada por el usuario
    const invitationEmail = document.getElementById('invitationEmail').value;

    // Construir la petición con los datos necesarios
    const data = new FormData();
    data.append('invitationEmail', invitationEmail);

    try {
        // Realizar la petición al servidor
        const url = `${location.origin}/invitation`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        // Mostrar nuevamente el botón de invitación y ocultar el indicador de carga
        invitationBtn.style.display = 'block';
        loaderInvitation.style.display = 'none';

        // Mostrar mensaje de éxito o error según el resultado de la operación
        if (result.type === 'exito') {
            // Limpiar el campo de correo electrónico después de enviar la invitación
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
        // Mostrar mensaje de error en caso de fallo en el envío del correo electrónico
        invitationBtn.style.display = 'block';
        loaderInvitation.style.display = 'none';
        infoAction.fire({
            icon: "error",
            title: 'No se ha podido enviar el email'
        });
        console.log(error);
    }
}