/**
 * Muestra un comentario en la interfaz de usuario.
 * 
 * Esta función crea un contenedor para mostrar un comentario en la interfaz de usuario.
 * Incluye información sobre el usuario que hizo el comentario, el texto del comentario,
 * la fecha en que se realizó y botones de acción para editar o eliminar el comentario
 * (si el usuario tiene los permisos necesarios).
 * 
 * @param {object} comment - Objeto que contiene la información del comentario.
 * @param {HTMLElement} anchor - Elemento HTML al que se adjuntará el contenedor del comentario.
 * @param {object} user - Objeto que representa al usuario actual.
 * @param {object} project - Objeto que representa el proyecto al que pertenece el comentario.
 */
function viewComment(comment, anchor, user, project) {
    // Crear elementos HTML para mostrar el comentario y la información del usuario
    const commentContainer = document.createElement('DIV');
    commentContainer.classList.add('commentContainer');
    commentContainer.dataset.user_id = comment.user_id;

    const commentActions = document.createElement('DIV');
    commentActions.classList.add('commentActions');

    const arrowTextComment = document.createElement('DIV');
    arrowTextComment.classList.add('arrowTextComment');
    const textComment = document.createElement('P');
    textComment.classList.add('textComment');
    textComment.textContent = comment.text;

    const infoUserComment = document.createElement('DIV');
    infoUserComment.classList.add('infoUserComment');

    const userImg = document.createElement('IMG');
    userImg.classList.add('userImg');
    userImg.src = `build/img/profile/${comment.user_image}.jpg`;

    const userComment = document.createElement('P');
    userComment.classList.add('userComment');
    if (user.id === comment.user_id) {
        userComment.textContent = 'Tú';
    } else {
        userComment.textContent = comment.user_name;
    }

    const dateComment = document.createElement('P');
    dateComment.classList.add('dateComment');
    dateComment.textContent = formatDate(comment.Timestamp);

    // Botones de acción de los comentarios

    // Botón de eliminar comentario
    const deleteCommentBtn = document.createElement('IMG');
    deleteCommentBtn.title = "Eliminar comentario";
    deleteCommentBtn.src = 'build/img/delete_icon.svg';
    deleteCommentBtn.classList.add('deleteCommentBtn');
    deleteCommentBtn.classList.add('actionImg');
    commentActions.appendChild(deleteCommentBtn);

    deleteCommentBtn.onclick = function (e) {
        Swal.fire({
            title: "¿Estás seguro que quieres eliminar el comentario?",
            text: "Esta operación no es reversible",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Borrar"
        }).then((result) => {
            if (result.isConfirmed) {
                const commentToDelete = e.target.closest('.commentContainer');
                deleteComment(comment, commentToDelete);
            }
        });
    };

    // Botón de editar comentario
    const editCommentBtn = document.createElement('IMG');
    editCommentBtn.title = "Editar comentario";
    editCommentBtn.src = 'build/img/edit_icon.svg';
    editCommentBtn.classList.add('editCommentBtn');
    editCommentBtn.classList.add('actionImg');
    commentActions.appendChild(editCommentBtn);
    editCommentBtn.onclick = function (e) {
        let anclaEditComment = e.target.closest('.commentContainer').querySelector('.textComment');
        const commentToUpdate = { 'text': comment.text, 'id': comment.id, 'task_id': comment.task_id };
        editCommentModal(commentToUpdate, anclaEditComment);
    };

    commentContainer.appendChild(commentActions);

    // Ocultar botones de acción si el usuario no tiene permisos
    if (user.id !== comment.user_id && project.user_id !== user.id) commentActions.style.display = 'none';

    // Adjuntar elementos al contenedor del comentario
    commentContainer.appendChild(infoUserComment);
    commentContainer.appendChild(arrowTextComment);
    commentContainer.appendChild(textComment);
    infoUserComment.appendChild(userImg);
    infoUserComment.appendChild(userComment);
    commentContainer.appendChild(dateComment);
    // Adjuntar el contenedor del comentario al elemento especificado en el parámetro anchor
    anchor.appendChild(commentContainer);
}

/**
 * Abre el modal para añadir un nuevo comentario.
 * 
 * Esta función se encarga de mostrar el modal para que el usuario pueda agregar un nuevo comentario a una tarea específica.
 * Se preconfiguran los elementos del modal y se añade un evento al botón de envío para procesar el comentario ingresado.
 * Si el usuario intenta enviar un comentario vacío, se muestra un mensaje de error.
 * 
 * @param {string} task_id - El ID de la tarea a la que se va a agregar el comentario.
 * @param {object} user - El objeto de usuario que representa al usuario actual.
 * @param {object} project - El objeto de proyecto al que pertenece la tarea.
 */
function newCommentsModal(task_id, user, project) {
    const commentModal = document.querySelector('#commentModal');
    commentModal.style.display = 'flex';

    const addCommentBtn = document.querySelector('#addCommentBtn');
    addCommentBtn.textContent = 'Enviar Comentario';

    document.querySelector('#modalCommentLegend').textContent = 'Nuevo Comentario';
    let commentText = commentModal.querySelector('#commentText');
    commentText.value = "";

    // Utilizo una función anónima como manejador de eventos para el botón de envío
    const handleAddTaskClick = function (e) {
        e.preventDefault();
        let comment = [];
        commentText = commentText.value.trim();
        if (commentText === '') {
            // Si el comentario está vacio muestra un error
            infoAction.fire({
                icon: "error",
                title: "El comentario no puede estar vacio"
            });
            return;
        }
        // Crea el objeto de comentario con los datos ingresados por el usuario
        comment = { 'text': commentText, 'task_id': task_id, 'user_id': user.id };
        // Invoca la función para agregar el comentario
        addComment(comment, user, project);
        // Elimino el manejador de eventos después de su ejecución
        addCommentBtn.removeEventListener('click', handleAddTaskClick);
    };
    // Agrega un evento al botón de envío para procesar el comentario ingresado
    addCommentBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Abre el modal para editar un comentario existente.
 * 
 * Esta función abre el modal que permite al usuario editar un comentario existente en una tarea.
 * Los elementos del modal se preconfiguran con los datos del comentario a editar.
 * Se añade un evento al botón de envío para procesar la actualización del comentario.
 * Si el usuario intenta enviar un comentario vacío, se muestra un mensaje de error.
 * 
 * @param {object} comment - El objeto de comentario que se va a editar.
 * @param {HTMLElement} anchor - El elemento ancla donde se mostrará el comentario actualizado.
 */
function editCommentModal(comment, anchor) {
    const commentModal = document.querySelector('#commentModal');
    commentModal.style.display = 'flex';

    const addCommentBtn = document.querySelector('#addCommentBtn');
    addCommentBtn.textContent = 'Editar comentario';

    document.querySelector('#modalCommentLegend').textContent = 'Editar Comentario';

    // Preconfigura el campo de texto con el contenido del comentario existente
    let commentText = document.querySelector('#commentText').value = comment.text;

    // Utiliza una función anónima como manejador de eventos para el botón de envío
    const handleAddTaskClick = function (e) {
        e.preventDefault();
        commentText = document.querySelector('#commentText').value.trim();
        if (commentText === '') {
            // Si el comentario está vacio se muestra un error
            infoAction.fire({
                icon: "error",
                title: "El comentario no puede estar vacio"
            });
            return;
        }
        // Crea el objeto de comentario actualizado con los datos ingresados por el usuario
        comment = { 'text': commentText, 'id': comment.id, 'task_id': comment.task_id };
        // Invoca la función para actualizar el comentario
        updateComment(comment, anchor);

        // Elimina el manejador de eventos después de su ejecución
        addCommentBtn.removeEventListener('click', handleAddTaskClick);
    };
    // Agrega un evento al botón de envío para procesar la actualización del comentario
    addCommentBtn.addEventListener('click', handleAddTaskClick);
}

/**
 * Realiza una consulta para añadir un nuevo comentario a una tarea.
 * 
 * Esta función envía una solicitud al servidor para agregar un nuevo comentario a una tarea específica.
 * Los datos del comentario, incluido el ID de la tarea, el texto del comentario y la URL del proyecto,
 * se adjuntan al cuerpo de la solicitud.
 * Si la operación es exitosa, se cierra el modal de comentarios y se muestra el nuevo comentario en la interfaz de usuario.
 * Además, se despliega información adicional sobre la tarea y se muestra un mensaje de éxito.
 * 
 * @param {object} comment - El objeto de comentario que se va a añadir.
 * @param {object} user - El objeto de usuario que está realizando la acción.
 * @param {object} project - El objeto de proyecto al que pertenece la tarea.
 */
async function addComment(comment, user, project) {
    // Construir la petición
    const data = new FormData();
    data.append('task_id', comment.task_id);
    data.append('text', comment.text);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/comment/create`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            // Oculta el modal de comentarios
            const commentModal = document.querySelector('#commentModal');
            commentModal.style.display = 'none';

            // Despliega información adicional de la tarea
            const taskContainer = document.querySelector(`.taskContainer[data-task-id="${comment.task_id}"]`);
            const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
            taskContentContainer.classList.add('active');

            // Ancla para el nuevo comentario
            const taskComments = taskContentContainer.querySelector('.taskComments');

            const newComment = {
                'id': result.comment_id,
                'task_id': comment.task_id,
                'user_id': user.id, 'text': comment.text,
                'user_image': user.image,
                'user_name': `${user.name} ${user.last_name}`,
                'Timestamp': new Date()
            };
            // Agrega el nuevo comentario a la interfaz de usuario
            viewComment(newComment, taskComments, user, project);

            // Muestra un mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        // En caso de error, muestra un mensaje de error y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido añadir el comentario"
        });
        console.log(error);
    }
}

/**
 * Realiza una consulta para actualizar un comentario existente.
 * 
 * Esta función envía una solicitud al servidor para actualizar un comentario específico.
 * Los datos del comentario, incluido su ID, el ID de la tarea asociada y el nuevo texto del comentario,
 * se adjuntan al cuerpo de la solicitud.
 * Si la operación es exitosa, se cierra el modal de comentarios y se actualiza el texto del comentario en la interfaz de usuario.
 * Además, se muestra un mensaje de éxito.
 * 
 * @param {object} comment - El objeto de comentario que se va a actualizar.
 * @param {HTMLElement} anchor - El elemento ancla donde se mostrará el nuevo texto del comentario.
 */
async function updateComment(comment, anchor) {
    // Despliega información adicional de la tarea
    const taskContainer = document.querySelector(`.taskContainer[data-task-id="${comment.task_id}"]`);
    const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
    taskContentContainer.classList.add('active');

    // Construye la petición
    const data = new FormData();
    data.append('id', comment.id);
    data.append('task_id', comment.task_id);
    data.append('text', comment.text);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/comment/update`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            // Oculta el modal de comentarios
            const commentModal = document.querySelector('#commentModal');
            commentModal.style.display = 'none';

            // Actualiza el texto del comentario en la interfaz de usuario
            anchor.textContent = comment.text;

            // Muestra un mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });

        }
    } catch (error) {
        // En caso de error, muestra un mensaje de error y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar el comentario"
        });
        console.log(error);
    }
}

/**
 * Realiza una consulta para eliminar un comentario.
 * 
 * Esta función envía una solicitud al servidor para eliminar un comentario específico.
 * El ID del comentario se adjunta al cuerpo de la solicitud.
 * Si la operación es exitosa, el comentario se elimina de la interfaz de usuario y se muestra un mensaje de éxito.
 * En caso de error, se muestra un mensaje de error y se registra el error en la consola.
 * 
 * @param {object} comment - El objeto de comentario que se va a eliminar.
 * @param {HTMLElement} commentContainer - El contenedor del comentario en la interfaz de usuario.
 */
async function deleteComment(comment, commentContainer) {
    // Despliega la info adicional de la tarea
    const taskContainer = document.querySelector(`.taskContainer[data-task-id="${comment.task_id}"]`);
    const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
    taskContentContainer.classList.add('active');

    // Construye la petición
    const data = new FormData();
    data.append('id', comment.id);
    data.append('url', getUrlProject());

    try {
        const url = `${location.origin}/api/comment/delete`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();

        if (result.type === 'exito') {
            // Oculta el contenedor del comentario en la interfaz de usuario
            commentContainer.style.display = 'none';
            // Muestra un mensaje de éxito
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        // En caso de error, muestra un mensaje de error y registra el error en la consola
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar la tarea"
        });
        console.log(error);
    }
}