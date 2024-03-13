<div class="barra">
    <p class="nombre-pagina"><?php echo $titulo ?></p>
    <div class="info-user">
        <p>Bienvenido <span><?php echo $_SESSION['name'] . " " . $_SESSION['last_name']; ?></span></p>
        <picture>
            <source srcset=<?php echo ("build/img/" . $_SESSION['image'] . ".jpg"); ?> type="image/jpeg">
            <source srcset=<?php echo ("build/img/" . $_SESSION['image'] . ".webp"); ?> type="image/webp">
            <img loading="lazy" id="imgProfile" src=<?php echo ("build/img/" . $_SESSION['image'] . ".png"); ?> alt="imagen de perfil de usuario">
        </picture>
    </div>
</div>
