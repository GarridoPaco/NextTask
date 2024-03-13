<div class="vista-general">
    <div class="vista-info">
        <img class="deleteProjectBtn actionImg" src="build/img/delete_icon.svg" alt="Icoco de eliminar">
        <img class="editProjectBtn actionImg" src="build/img/edit_icon.svg" alt="Icoco de editar">
        <h3>Proyecto</h3>
        <h4 id="projectName"></h4>
        <p id="projectDescription">Descripción:<br></p>
        <p>Fecha de entrega:<span id="projectDeadline"></span></p>
        <h4>Tareas</h4>
        <div class="tasks">
            <ul class="tasks-list" id="tasks-list"></ul>
        </div>
        <button class="boton" id="addTask">Añadir tarea</button>
    </div>
    <div class="calendario">
        <h3>Calendario</h3>
        <div id="calendar"></div>
    </div>
    <div class="collaboratorsContainer" id="collaboratorsContainer">
        <h3>Colaboradores</h3>
        <div class="collaboratorsList" id="collaboratorsList"></div>
        <button class="boton" id="openModalCollaborators">Añadir colaborador</button>
    </div>
</div>