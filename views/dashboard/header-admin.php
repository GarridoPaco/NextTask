<main class="dashboard">
        <?php include_once __DIR__ . '/../templates/menu-mobile.php'; ?>
    <aside class="sidebar">
        <div class="sidebar-header">
        <div class="mobileCloseBtn">
            <img id="mobileCloseBtn" src="build/img/icon_close_white.svg" alt="imagen cerrar">
        </div>
        <?php include __DIR__ . '/../templates/nombre-sitio-clear.php'; ?>
    </div>
        <nav class="sidebar-nav">
            <a class="<?php echo ($titulo === 'Usuarios') ? 'activo' : ''; ?>" href="/admin">Usuarios</a>
            <a class="<?php echo ($titulo === 'Perfil') ? 'activo' : ''; ?>" href="/profile">Perfil</a>
        </nav>
        <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </aside>
    <div class="principal">
        <?php include_once __DIR__ . '/../templates/barra.php' ?>
        <div class="contenido">