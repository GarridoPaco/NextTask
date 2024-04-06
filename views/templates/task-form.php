
<div class="modal-overlay" id="taskModal">
    <div class="modal">
        <form class="formulario">
            <img class="closeModal" src="build/img/icon_close.svg" alt="Icoco de cerrar">
            <legend id="modalLegend">Nueva Tarea</legend>
            <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
            <div class="campo">
                <label for="taskTitle">Nombre <span>Obligatorio</span></label>
                <input type="text" id="taskTitle" placeholder="Nombre de la tarea" name="name">
            </div>
            <div class="campo">
                <label for="taskDescription">Descripción</label>
                <textarea id="taskDescription" name="description" placeholder="Descripción de la tarea"></textarea>
            </div>
            <div class="campo">
                <label for="taskDeadline">Fecha de entrega <span>Obligatorio</span></label>
                <input type="date" id="taskDeadline" placeholder="Fecha de entrega" name="deadline" min="<?php echo(date('Y-m-d')) ?>">
            </div>
            <div class="campo">
                <label for="taskPriority">Prioridad</label>
                <select name="priority" id="taskPriority">
                    <option value="0">Baja</option>
                    <option value="1">Normal</option>
                    <option value="2">Alta</option>
                </select>
            </div>
            <div class="campo">
                <label for="taskStatus">Estado</label>
                <select name="status" id="taskStatus">
                    <option value="0">Pendiente</option>
                    <option value="1">En progreso</option>
                    <option value="2">Finalizada</option>
                </select>
            </div>

            <button class="boton" id="addTaskBtn">Enviar</button>
        </form>
    </div>
</div>