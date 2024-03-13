<div class="contenedor login">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <h3>Crea tu cuenta<br>en NextTask</h3>
        <p class="descripcion-pagina">Completa el formulario para registrarte</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <form class="formulario" method="POST" action="/create">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" placeholder="Tu Nombre" name="name" value="<?php echo $user->name; ?>">
            </div>
            <div class="campo">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" placeholder="Tus Apellidos" name="last_name" value="<?php echo $user->last_name; ?>">
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email" value="<?php echo $user->email; ?>">
            </div>
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Tu Password" name="password">
            </div>
            <div class="campo">
                <label for="repeatPassword">Repetir Password</label>
                <input type="password" id="repeatPassword" placeholder="Repite tu Password" name="repeatPassword">
            </div>
            <input type="submit" class="boton" value="Crear cuenta">
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes tu cuenta? Inicia sesión</a>
            <a href="/forget">Olvidé mi contraseña</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>