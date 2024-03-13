function viewComment(comment, anchor, user, project) {

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
    userImg.src = `build/img/${comment.user_image}.jpg`;

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
    if (user.id !== comment.user_id && project.user_id !== user.id) commentActions.style.display = 'none';
    commentContainer.appendChild(infoUserComment);
    commentContainer.appendChild(arrowTextComment);
    commentContainer.appendChild(textComment);
    infoUserComment.appendChild(userImg);
    infoUserComment.appendChild(userComment);
    commentContainer.appendChild(dateComment);
    anchor.appendChild(commentContainer);
}

// Abre el modal para añadir un nuevo comentario
function newCommentsModal(task_id, user, project) {
    const commentModal = document.querySelector('#commentModal');
    commentModal.style.display = 'flex';

    const addCommentBtn = document.querySelector('#addCommentBtn');
    addCommentBtn.textContent = 'Enviar Comentario';

    document.querySelector('#modalCommentLegend').textContent = 'Nuevo Comentario';
    let commentText = commentModal.querySelector('#commentText');
    commentText.value = "";
    
    // Utilizo una función anónima como manejador de eventos
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
        comment = { 'text': commentText, 'task_id': task_id, 'user_id': user.id };
        addComment(comment, user, project);
        // Elimino el manejador de eventos después de su ejecución
        addCommentBtn.removeEventListener('click', handleAddTaskClick);
    };
    addCommentBtn.addEventListener('click', handleAddTaskClick);
}

function editCommentModal(comment, anchor) {
    const commentModal = document.querySelector('#commentModal');
    commentModal.style.display = 'flex';

    const addCommentBtn = document.querySelector('#addCommentBtn');
    addCommentBtn.textContent = 'Editar comentario';

    document.querySelector('#modalCommentLegend').textContent = 'Editar Comentario';

    let commentText = document.querySelector('#commentText').value = comment.text;

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
        comment = { 'text': commentText, 'id': comment.id, 'task_id': comment.task_id };
        updateComment(comment, anchor);

        addCommentBtn.removeEventListener('click', handleAddTaskClick);
    };
    addCommentBtn.addEventListener('click', handleAddTaskClick);
}

// Consulta para añadir un comentario
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
            const commentModal = document.querySelector('#commentModal');
            commentModal.style.display = 'none';

            // Despliego la info adicional de la tarea
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
            viewComment(newComment, taskComments, user, project);

            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido añadir el comentario"
        });
        console.log(error);
    }

}

// Consulta para actualizar un comentario
async function updateComment(comment, anchor) {
    // Despliego la info adicional de la tarea
    const taskContainer = document.querySelector(`.taskContainer[data-task-id="${comment.task_id}"]`);
    const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
    taskContentContainer.classList.add('active');

    // Construir la petición
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
            const commentModal = document.querySelector('#commentModal');
            commentModal.style.display = 'none';

            anchor.textContent = comment.text;

            infoAction.fire({
                icon: "success",
                title: result.message
            });

        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido actualizar el comentario"
        });
        console.log(error);
    }

}

// Consulta para eliminar un comentario
async function deleteComment(comment, commentContainer) {
    // Despliego la info adicional de la tarea
    const taskContainer = document.querySelector(`.taskContainer[data-task-id="${comment.task_id}"]`);
    const taskContentContainer = taskContainer.querySelector('.taskContentContainer');
    taskContentContainer.classList.add('active');

    // Construir la petición
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
            commentContainer.style.display = 'none';
            infoAction.fire({
                icon: "success",
                title: result.message
            });
        }
    } catch (error) {
        infoAction.fire({
            icon: "error",
            title: "No se ha podido eliminar la tarea"
        });
        console.log(error);
    }
}