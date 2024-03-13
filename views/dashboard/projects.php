<?php include_once __DIR__ . '/../utils/utils.php'; ?>
<?php if (empty($projects)) : ?>
    <h4 id="welcome-message">¡Es hora de dar vida a tus proyectos! Haz clic en el botón de abajo para empezar a crear y gestionar tus proyectos con facilidad</h4>
<?php endif; ?>
<?php include_once __DIR__ . '/../templates/projects-list.php'; ?>
<?php include_once __DIR__ . '/../templates/project-form.php'; ?>
<button class="addProject boton">Crear Proyecto</button>
<?php $script = "<script src='build/js/alert.js'></script>";?>
<?php $script .= "<script src='build/js/utils.js'></script>";?>
<?php $script .= "<script src='build/js/projects.js'></script>";?>