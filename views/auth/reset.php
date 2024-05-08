<div class="contenedor login">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <h3>Resetear tu contraseña<br>en NextTask</h3>
        <p class="descripcion-pagina">Introduce la nueva contraseña</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <?php if ($showForm) : ?>
            <form class="formulario" method="POST">
                <div class="campo">
                    <label for="password">Nuevo password</label>
                    <input type="password" id="password" placeholder="Tu nueva contraseña" name="password">
                </div>
                <div class="campo">
                    <label for="repeatPassword">Repetir tu nuevo password</label>
                    <input type="password" id="passwordRepeat" placeholder="Repite tu nueva contraseña" name="repeatPassword">
                </div>
                <input type="submit" class="boton" value="Guardar contraseña">
            </form>
        <?php endif; ?>
        <div class="acciones">
            <a href="/login">¿Ya tienes tu cuenta? Inicia sesión</a>
            <a href="/create">Crea una cuenta</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>