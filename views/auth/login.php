<div class="contenedor login">
    <?php include_once __DIR__ .'/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <h3>Te damos la bienvenida<br>a NextTask</h3>
        <p class="descripcion-pagina">Para comenzar, inicia sesión</p>
        <?php include_once __DIR__ .'/../templates/alertas.php'; ?>
        <form class="formulario" method="POST" action="/login">
            <div class="campo">
                <label for="email">Tu dirección de email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email">
            </div>
            <div class="campo">
                <label for="password">Tu contraseña</label>
                <input type="password" id="password" placeholder="Tu Password" name="password">
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>
        <div class="acciones">
            <a href="/create">Crea una cuenta</a>
            <a href="/forget">Olvidé mi contraseña</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>