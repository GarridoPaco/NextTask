/**
 * Consulta a la API la información del proyecto actual.
 * @returns {Promise<Object>} La información del proyecto actual.
 */
async function getProject() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/project?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();
        return result.project;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Consulta a la API el listado de tareas del proyecto actual.
 * @returns {Promise<Array>} El listado de tareas del proyecto actual.
 */
async function getTasks() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/tasks?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();

        return result.tasks;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Consulta a la API las asignaciones a tareas.
 * @returns {Promise<Array>} El listado de asignaciones a tareas.
 */
async function getAssign() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/assignment?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();

        return result.assignments;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Consulta a la API los colaboradores del proyecto actual.
 * @returns {Promise<Array>} El listado de colaboradores del proyecto actual.
 */
async function getCollaborators() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/collaboration?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();
        return result.collaborators;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Consulta a la API los comentarios de una tarea.
 * @param {string} task_id - El ID de la tarea de la que se quieren obtener los comentarios.
 * @returns {Promise<Object>} Los comentarios de la tarea especificada.
 */
async function getComments(task_id) {
    const data = new FormData();
    data.append('task_id', task_id);
    try {
        const url = `${location.origin}/api/comment`;
        const answer = await fetch(url, {
            method: 'POST',
            body: data
        });
        const result = await answer.json();
        return result;
    } catch (error) {

        console.log(error);
    }
}

/**
 * Consulta a la API por los usuarios de la aplicación.
 * @returns {Promise<Array>} El listado de usuarios de la aplicación.
 */
async function getUsers() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/users?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();

        return result.users;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Consulta a la API por el ID del usuario con sesión iniciada.
 * @returns {Promise<Object>} El usuario con sesión iniciada.
 */
async function getUser() {
    try {
        const urlProject = getUrlProject();
        const url = `${location.origin}/api/user?url=${urlProject}`;
        const answer = await fetch(url);
        const result = await answer.json();

        return result.user;
    } catch (error) {
        console.log(error);
    }
}

/**
 * Devuelve el valor de la URL para peticiones tipo GET.
 * @returns {string} El valor de la URL del proyecto actual.
 */
function getUrlProject() {
    const projectParams = new URLSearchParams(window.location.search);
    const project = Object.fromEntries(projectParams.entries());
    return project.url;
}
