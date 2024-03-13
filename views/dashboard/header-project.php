<main class="dashboard">
    <?php include_once __DIR__ . '/../templates/sidebar.php' ?>
    <?php include_once __DIR__ . '/../templates/menu-mobile.php'; ?>
    <div class="principal">
        <nav class="nav-views" id="navViews">
            <form action="/project" method="get">
                <input class="viewBtn <?php echo (strpos($titulo, 'General')) ? 'activo' : ''; ?>" type="submit" value="General" />
                <input type="hidden" name="url" value="<?php echo $project->url ?>">
            </form>
            <form action="/kanban" method="get">
                <input class="viewBtn <?php echo (strpos($titulo, 'Kanban')) ? 'activo' : ''; ?>" type="submit" value="Kanban" />
                <input type="hidden" name="url" value="<?php echo $project->url ?>">
            </form>
        </nav>
        <?php include_once __DIR__ . '/../templates/barra.php' ?>
        <div class="contenido-project">
        <?php include_once __DIR__ . '/../templates/loading-overlay.php' ?>