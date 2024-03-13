<ul class="projectsList">
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <?php foreach ($projects as $project) : ?>

        <li>
            <form id="createProjectForm" action="/project" method="get">
                <button class="boton-proyecto" type="submit">
                    <p><?php echo $project->name; ?></p>
                    <div class="info-project">
                        <p>Entrega: <?php echo formatDate($project->deadline); ?></p>
                    </div>
                </button>
                <input type="hidden" name="url" value="<?php echo $project->url ?>">
            </form>
            <form id="deleteProjectForm" action="/project" method="post">
                <input type="submit" value="Eliminar" class="boton" id="deleteProjectBtn">
                <input type="hidden" name="project_id" value="<?php echo $project->id ?>">
            </form>
        </li>

    <?php endforeach; ?>
</ul>