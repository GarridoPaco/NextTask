<div class="contenedor login">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <h3>Recupera tu contraseña<br>en NextTask</h3>
        <p class="descripcion-pagina">Para recuperarla, introduce tu email</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <form class="formulario" method="POST" action="/forget">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email">
            </div>
            <input type="submit" class="boton" value="Recuperar contraseña">
        </form>
        <div class="acciones">
            <a href="/">¿Ya tienes tu cuenta? Inicia sesión</a>
            <a href="/create">Crea una cuenta</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>