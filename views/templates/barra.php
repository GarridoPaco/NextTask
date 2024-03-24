<div class="barra">
    <p class="nombre-pagina"><?php echo $titulo ?></p>
    <div class="info-user">
        <p>Bienvenido <span><?php echo $_SESSION['name'] . " " . $_SESSION['last_name']; ?></span></p>
            <img loading="lazy" id="imgProfile" src=<?php echo ("build/img/profile/" . $_SESSION['image'] . ".jpg"); ?> alt="imagen de perfil de usuario">
    </div>
</div>
