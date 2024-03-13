<div class="modal-overlay" id="collaboratorModal">
    <div class="modal">
        <form class="formulario">
            <img class="closeModal" id="closeCollaboratorModal" src="build/img/icon_close.svg" alt="Icoco de cerrar" title="Cerrar">
            <legend id="collaboratorModalLegend">Añadir colaborador</legend>
            <div class="checkboxCollaborators" id="checkboxCollaborators">
            </div>
            <button class="boton" id="addCollaboratorBtn">Añadir colaborador al proyecto</button>
        </form>
        <form class="formulario" id="invitationForm">
            <legend>Invitar a un colaborador</legend>
            <label for="invitationEmail">Introduce el email:</label>
            <input type="email" name="invitationEmail" id="invitationEmail">
            <button class="boton" id="invitationBtn">Invitar</button>
            <div class="lds-ellipsis" id="loader-invitation">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </form>
    </div>
</div>