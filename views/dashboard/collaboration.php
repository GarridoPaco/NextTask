<?php include_once __DIR__ . '/header-dashboard.php' ?>
<?php include_once __DIR__ . '/../templates/filter.php'; ?>
<?php include_once __DIR__ . '/../utils/utils.php'; ?>
<?php
$user_id = $_SESSION['id'];
?>
<ul id="collaborationContainer">
    <?php foreach ($tasks as $task) : ?>
        <li class="collaborationBin">
            <h3 class="collaborationTitle"><?php echo $task->title; ?></h3>
            <p class="collaborationDescription"><?php echo $task->description; ?></p>
            <p class="collaborationStatus"><span>Estado: </span><?php
                        switch ($task->status) {
                            case 0:
                                echo "Pendiente";
                                break;
                            case 1:
                                echo "En progreso";
                                break;
                            case 2:
                                echo "Finalizada";
                                break;
                        }
                        ?>
            </p>
            <p class="collaborationPriority"><span>Prioridad: </span><?php
                            switch ($task->priority) {
                                case 0:
                                    echo "Baja";
                                    break;
                                case 1:
                                    echo "Normal";
                                    break;
                                case 2:
                                    echo "Alta";
                                    break;
                            }
                            ?>
            </p>
            <p class="collaborationDeadline"><span>Fecha de entrega: </span><?php echo formatDate($task->deadline); ?></p>
            <p class="collaborationProjectName"><span>Proyecto: </span><?php
                foreach ($projects as $project) {
                    if ($project->id == $task->project_id) echo ($project->name);
                }
                ?>
            </p>
            <p class="collaborationManagerName"><span>Jefe de proyecto: </span><?php
                foreach ($projects as $project) {
                    if ($project->id == $task->project_id) {
                        foreach ($users as $user) {
                            if ($user->id == $project->user_id) {
                                echo ($user->name . " " . $user->last_name);
                            }
                        }
                    }
                }
                ?>
            </p>
            <div class="collaborationAction">
                <form action="/collaboration-kanban" method="get">
                    <input class="boton" type="submit" value="Ver Proyecto" />
                    <input type="hidden" name="url" value="<?php
                                                            foreach ($projects as $project) {
                                                                if ($project->id == $task->project_id) echo ($project->url);
                                                            } ?>">
                </form>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php include_once __DIR__ . '/footer-dashboard.php' ?>
<?php $script = "<script src='build/js/utils.js'></script>";?>
<?php $script .= "<script src='build/js/collaborations.js'></script>";?>
