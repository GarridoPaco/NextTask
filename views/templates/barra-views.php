<div class="barra">
    <p class="nombre-pagina"><?php echo $titulo ?></p>
    <!-- <div class="barra-vistas">
        <form action="/project" method="get">
            <button class="boton" type="submit">
                Vista General
            </button>
            <input type="hidden" name="url" value="<?php echo $project->url ?>">
        </form>
        <form action="/kanban" method="get">
            <button class="boton" type="submit">
                Vista Kanban
            </button>
            <input type="hidden" name="url" value="<?php echo $project->url ?>">
        </form>
    </div> -->
    <div class="info-user">
        <p>Bienvenido <span><?php echo $_SESSION['name'] . " " . $_SESSION['last_name']; ?></span></p>
        <picture>
            <source srcset=<?php echo ("build/img/" . $_SESSION['image'] . ".jpg"); ?> type="image/jpeg">
            <source srcset=<?php echo ("build/img/" . $_SESSION['image'] . ".webp"); ?> type="image/webp">
            <img loading="lazy" src=<?php echo ("build/img/" . $_SESSION['image'] . ".png"); ?> alt="imagen de perfil de usuario">
        </picture>
    </div>
</div>