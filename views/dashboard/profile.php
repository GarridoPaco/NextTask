<?php
if ($admin === '1') {
    include_once __DIR__ . '/header-admin.php';
} else {
    include_once __DIR__ . '/header-dashboard.php';
}
?>
<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form class="formulario" id="profileForm" method="POST" action="/profile" enctype="multipart/form-data">
        <legend class="descripcion-pagina">Datos Personales</legend>
        <div class="campo">
            <label for="name">Nombre</label>
            <input type="text" value="<?php echo $_SESSION['name']; ?>" name="name" placeholder="Tu Nombre" />
        </div>
        <div class="campo">
            <label for="last_name">Apellidos</label>
            <input type="text" value="<?php echo $_SESSION['last_name']; ?>" name="last_name" placeholder="Tu Nombre" />
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" value="<?php echo $_SESSION['email']; ?>" name="email" placeholder="Tu Email" />
        </div>
        <div class="campo">
            <label for="image">Foto de perfil</label>
            <input type="file" accept="image/jpeg" name="image" id="image" />
        </div>
        <input type="submit" class="boton" id="updateProfileBtn" value="Actualizar perfil">
    </form>

    <form class="formulario" id="passwordForm" method="POST" action="/changePassword">
        <legend class="descripcion-pagina">Actualizar Password</legend>
        <div class="campo">
            <label for="actualPassword">Contraseña actual</label>
            <input type="password" name="actualPassword" placeholder="Tu password actual" />
        </div>
        <div class="campo">
            <label for="newPassword">Nueva contraseña</label>
            <input type="password" name="newPassword" placeholder="Tu nuevo password" />
        </div>
        <input type="submit" class="boton" value="Actualizar password">
    </form>
    
    <form class="formulario" id="deleteProfileForm" action="/deleteProfile" method="post">
    <legend class="descripcion-pagina">Eliminar cuenta</legend>
        <input type="submit" value="Eliminar Cuenta" class="boton" id="deleteProfileBtn">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['id'] ?>">
    </form>
</div>
<?php include_once __DIR__ . '/footer-dashboard.php' ?>
<?php $script = "<script src='build/js/utils.js'></script>"; ?>
<?php $script .= "<script src='build/js/profile.js'></script>";?>