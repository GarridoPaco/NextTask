<?php include_once __DIR__ . '/../templates/header.php'; ?>
<main class="home_main">
    <section class="home_introduction">
        <h1 class="introduction_title">Simplicidad en la gestión de proyectos</h1>
        <p class="introduction_description">
            NextTask es una aplicación de gestión de proyectos diseñada para simplificar tu vida diaria.
            Con características intuitivas y una interfaz fácil de usar, NextTask te ayuda a mantener tus proyectos organizados y en curso.
        </p>
        <a href="/create" class="boton">Comenzar</a>
    </section>

    <section class="home_functionalities">
        <h2 class="functionalities_title">Más que una lista de tareas</h2>
        <ul class="functionalities_list">
            <li class="functionality">
                <div class="functionality_info">
                    <h3 class="functionality_title">Crea proyectos personalizados y asigna tareas fácilmente</h3>
                    <p class="functionality_description">
                        Con NextTask, puedes crear proyectos a medida y asignar tareas de manera intuitiva y rápida.
                        Diseña tus proyectos según tus necesidades específicas y organiza las tareas de forma eficiente.
                        Ya sea un proyecto personal o de equipo, NextTask te brinda la flexibilidad para adaptarte a cualquier desafío y alcanzar tus objetivos con facilidad.
                    </p>
                </div>
                <img class="functionality_img" src="build/img/project_interface.jpg" alt="Interfaz de los proyectos">
            </li>
            <hr>
            <li class="functionality">
                <div class="functionality_info">
                    <h3 class="functionality_title">Colabora con tu equipo</h3>
                    <p class="functionality_description">
                        ¡Potencia la colaboración al máximo con NextTask! Invita a colaboradores,
                        asigna tareas y trabaja en equipo para alcanzar tus objetivos de manera efectiva.
                        Aprovecha nuestra función de invitación por correo electrónico para agregar rápidamente nuevos miembros al equipo y
                        comenzar a colaborar sin problemas. Además, con la posibilidad de añadir comentarios a las tareas,
                        la comunicación y el seguimiento del progreso se vuelven más claros y eficientes. Únete como colaborador en
                        otros proyectos para ampliar tus horizontes y trabajar en conjunto de manera fluida con NextTask.
                    </p>
                </div>
                <img class="functionality_img" src="build/img/collaborators_interface.jpg" alt="Interfaz de los colaboradores">
            </li>
            <hr>
            <li class="functionality">
                <div class="functionality_info">
                    <h3 class="functionality_title">Visualiza el estado de tus proyectos con un vistazo</h3>
                    <p class="functionality_description">
                        Obtén una visión clara del progreso de tus proyectos con NextTask.
                        Nuestro calendario integrado te permite visualizar fácilmente las fechas límite de tus proyectos y tareas,
                        asegurándote de estar siempre al tanto de tus plazos.
                        Además, con nuestra vista Kanban, puedes organizar y priorizar tus tareas de manera intuitiva, ¡todo con un simple vistazo!
                    </p>
                </div>
                <img class="functionality_img" src="build/img/calendar_interface.jpg" alt="Interfaz del calendario">
            </li>
            <hr>
            <li class="functionality">
                <div class="functionality_info">
                    <h3 class="functionality_title">Accede a NextTask desde cualquier dispositivo, en cualquier momento y lugar</h3>
                    <p class="functionality_description">
                        ¡Accede a NextTask en cualquier momento y lugar con total facilidad!
                        Nuestro diseño adaptativo garantiza una experiencia óptima en cualquier dispositivo,
                        ya sea en tu ordenador de escritorio, portátil, tableta o teléfono móvil.
                        Trabaja en tus proyectos, asigna tareas y visualiza el progreso desde la comodidad de tu dispositivo preferido.
                        Con NextTask, la gestión de proyectos está al alcance de tu mano, siempre que lo necesites y dondequiera que estés.
                    </p>
                </div>
                <img class="functionality_img" src="build/img/responsive_design.jpg" alt="Diseño responsivo">
            </li>
            <hr>
            <li class="functionality">
                <div class="functionality_info">
                    <h3 class="functionality_title">Utiliza NextTask con facilidad sin perder de vista ningún detalle</h3>
                    <p class="functionality_description">
                        Explora la potencia de NextTask a través de una interfaz sencilla y detallada.
                        Cada ficha de tarea se expande con un solo clic, revelando una riqueza de información esencial.
                        Desde la descripción y la fecha de entrega hasta la prioridad y los comentarios de colaboradores,
                        toda la información crucial está al alcance de tu mano. Además,los checkboxes de las tareas permiten un cambio rápido y eficiente de su estado,
                        proporcionando una experiencia fluida y coherente. Con NextTask, la gestión de proyectos nunca ha sido tan fácil y completa.
                    </p>
                </div>
                <img class="functionality_img" src="build/img/task_interface.jpg" alt="Interfaz de la tarea">
            </li>
        </ul>
    </section>
</main>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
<?php $script = "<script src='build/js/mobileMenu.js'></script>"; ?>