<header class="header">
    <?php include_once __DIR__ . '/../templates/nombre-sitio-clear.php'; ?>
    <nav class="header-navigation">
        <ul class="header-navigation-list">
            <li class="header-navigation-list_item_container">
                <div class="header-navigation-list_item">
                    <a class="<?php echo ($titulo === 'Inicio') ? 'activo' : ''; ?>" href="/">Inicio</a>
                </div>
            </li>
            <li class="header-navigation-list_item_container">
                <div class="header-navigation-list_item">
                    <a class="<?php echo ($titulo === 'Desarrollo') ? 'activo' : ''; ?>" href="/development">Desarrollo</a>
                </div>
            </li>
            <li class="header-navigation-list_item_container">
                <div class="header-navigation-list_item">
                    <a class="<?php echo ($titulo === 'Recursos') ? 'activo' : ''; ?>" href="/resources">Recursos</a>
                </div>
            </li>
            <li class="header-navigation-list_item_container">
                <div class="header-navigation-list_item">
                    <a class="<?php echo ($titulo === 'Ayuda') ? 'activo' : ''; ?>" href="/help">Ayuda</a>
                </div>
            </li>
        </ul>
    </nav>
    <div class="header-button-group">
        <a href="/login" class="login_btn boton">Iniciar sesión</a>
        <a href="/create" class="create_btn boton">Comenzar</a>
    </div>
    <img id="indexMobileMenuBtn" src="build/img/burger_icon.svg" alt="Icono menú">
</header>