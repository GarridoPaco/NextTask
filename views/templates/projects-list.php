<ul class="projectsList">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <?php foreach ($projects as $project) : ?>

        <li class="projectContainer">
            <h3 class="projectTitle"><?php echo $project->name; ?></h3>

            <p class="projectDescription"><span>Descripción<br></span><?php echo ($project->description !== '') ? ($project->description) : ('No definida'); ?></p>
            <p class="projectDeadline"><span>Fecha de entrega<br></span><?php echo formatDate($project->deadline); ?></p>

            <div class="projectActions">

                <form id="viewProjectForm" action="/project" method="get">
                    <input type="submit" value="Ver Proyecto" class="boton" id="viewProjectBtn">
                    <input type="hidden" name="url" value="<?php echo $project->url ?>">
                </form>

                <form id="deleteProjectForm" action="/project" method="post">
                    <input type="submit" value="Eliminar" class="boton" id="deleteProjectBtn">
                    <input type="hidden" name="project_id" value="<?php echo $project->id ?>">
                </form>
            </div>

        </li>

    <?php endforeach; ?>
</ul>