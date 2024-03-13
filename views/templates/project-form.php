
<div class="modal-overlay" id="projectModal">
    <div class="modal">
        <form class="formulario" method="POST" action="/dashboard">
            <img class="closeModal" src="build/img/icon_close.svg" alt="Icoco de cerrar" title="Cerrar">
            <legend class="descripcion-pagina" id="modalLegendProject">Nuevo Proyecto</legend>
            <div class="campo">
                <label for="projectNameInput">Nombre</label>
                <input type="text" id="projectNameInput" placeholder="Nombre del proyecto" name="name">
            </div>
            <div class="campo">
                <label for="projectDescriptionInput">Descripción</label>
                <textarea id="projectDescriptionInput" name="description" placeholder="Descripción del proyecto"></textarea>
            </div>
            <div class="campo">
                <label for="projectDeadlineInput">Fecha de entrega</label>
                <input type="date" id="projectDeadlineInput" placeholder="Fecha de entrega" name="deadline">
            </div>

            <button class="boton" id="addProjectBtn" type="submit">Enviar</button>
        </form>
    </div>
</div>