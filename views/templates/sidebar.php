<aside class="sidebar">
    <div class="sidebar-header">
        <div class="mobileCloseBtn">
            <img id="mobileCloseBtn" src="build/img/icon_close_white.svg" alt="imagen cerrar">
        </div>
        <?php include __DIR__ . '/../templates/nombre-sitio-clear.php'; ?>
    </div>
    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo === 'Proyectos') ? 'activo' : ''; ?>" href="/dashboard">Proyectos</a>
        <a class="<?php echo ($titulo === 'Colaboraciones') ? 'activo' : ''; ?>" href="/collaboration">Colaboraciones</a>
        <a class="<?php echo ($titulo === 'Perfil') ? 'activo' : ''; ?>" href="/profile">Perfil</a>
    </nav>
    <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
</aside>