/**
 * Muestra un calendario con las fechas de entrega de tareas y del proyecto.
 * 
 * @param {Array} tasks - Arreglo de objetos que representan las tareas del proyecto.
 * @param {object} user - Objeto que representa al usuario actual.
 * @param {object} project - Objeto que representa el proyecto actual.
 */
async function showCalendar(tasks, user, project) {
  const calendar = document.getElementById('calendar');
  if (calendar === null) return;

  // Añadir la fecha de entrega del proyecto como evento
  let events = [];
  const projectDeadline = {}
  projectDeadline.title = "Fecha de entrega";
  projectDeadline.start = new Date(project.deadline);
  projectDeadline.end = new Date(project.deadline);
  projectDeadline.content = eventIcon();

  events.push(projectDeadline);

  // Iterar sobre las tareas y crear eventos para cada una
  tasks.forEach(task => {
    let event = {}; // Inicializar el objeto event

    event.id = task.id;
    event.title = task.title;
    event.start = new Date(task.deadline);
    event.end = new Date(task.deadline);
    event.content = eventIcon(task.status, task.priority);

    events.push(event);
  });

  // Configurar y renderizar el calendario con FullCalendar
  const newCalendar = new FullCalendar.Calendar(calendar, {
    initialView: 'dayGridMonth',
    locale: 'esLocale',
    firstDay: 1,
    height: 'auto',
    events: events,
    headerToolbar: {
      start: 'title',
      center: '',
      end: 'prev,next'
    },
    eventContent: function (arg) {
      return {
        html: arg.event.extendedProps.content
      };
    },
    eventMouseEnter: function (info) {
      // Agregar tooltip al evento del calendario
      tippy(info.el, {
        content: info.event.title,

      });
    },
    eventClick: function (info) {
      // Abrir el modal de edición al hacer clic en una tarea
      if (!info.event.id) return;
      const taskToUpdate = tasks.find(task => task.id === info.event.id);
      editTaskModal(taskToUpdate, user, project);
    }
  });
  newCalendar.render()
}

/**
 * Devuelve el icono correspondiente al estado y prioridad de un evento del calendario.
 * 
 * @param {string} icon - Estado del evento ('0' para pendiente, '1' para en progreso, '2' para completado).
 * @param {number} color - Índice del color de prioridad del evento.
 * @returns {string} - Código SVG o imagen correspondiente al icono del evento.
 */
function eventIcon(icon, color) {
  const priorityColors = ['green', 'yellow', 'red'];
  switch (icon){
    case '0':
      return `<?xml version="1.0" encoding="UTF-8"?>
      <svg version="1.1" id="pending_icon" class="fc-event-task-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      viewBox="0 0 24 24">
        <path fill=${priorityColors[color]} d="M13,2.03V2.05L13,4.05C17.39,4.59 20.5,8.58 19.96,12.97C19.5,16.61 16.64,19.5 13,19.93V21.93C18.5,21.38 22.5,16.5 21.95,11C21.5,6.25 17.73,2.5 13,2.03M11,2.06C9.05,2.25 7.19,3 5.67,4.26L7.1,5.74C8.22,4.84 9.57,4.26 11,4.06V2.06M4.26,5.67C3,7.19 2.25,9.04 2.05,11H4.05C4.24,9.58 4.8,8.23 5.69,7.1L4.26,5.67M2.06,13C2.26,14.96 3.03,16.81 4.27,18.33L5.69,16.9C4.81,15.77 4.24,14.42 4.06,13H2.06M7.1,18.37L5.67,19.74C7.18,21 9.04,21.79 11,22V20C9.58,19.82 8.23,19.25 7.1,18.37M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" />
      </svg>`;
    case '1':
      return `<?xml version="1.0" encoding="UTF-8"?>
      <svg version="1.1" id="progress_icon" class="fc-event-task-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
        <path fill=${priorityColors[color]} d="M13,2.03V2.05L13,4.05C17.39,4.59 20.5,8.58 19.96,12.97C19.5,16.61 16.64,19.5 13,19.93V21.93C18.5,21.38 22.5,16.5 21.95,11C21.5,6.25 17.73,2.5 13,2.03M11,2.06C9.05,2.25 7.19,3 5.67,4.26L7.1,5.74C8.22,4.84 9.57,4.26 11,4.06V2.06M4.26,5.67C3,7.19 2.25,9.04 2.05,11H4.05C4.24,9.58 4.8,8.23 5.69,7.1L4.26,5.67M2.06,13C2.26,14.96 3.03,16.81 4.27,18.33L5.69,16.9C4.81,15.77 4.24,14.42 4.06,13H2.06M7.1,18.37L5.67,19.74C7.18,21 9.04,21.79 11,22V20C9.58,19.82 8.23,19.25 7.1,18.37M16.82,15.19L12.71,11.08C13.12,10.04 12.89,8.82 12.03,7.97C11.13,7.06 9.78,6.88 8.69,7.38L10.63,9.32L9.28,10.68L7.29,8.73C6.75,9.82 7,11.17 7.88,12.08C8.74,12.94 9.96,13.16 11,12.76L15.11,16.86C15.29,17.05 15.56,17.05 15.74,16.86L16.78,15.83C17,15.65 17,15.33 16.82,15.19Z" />
      </svg>`;
    case '2':
      return `<?xml version="1.0" encoding="utf-8"?>
      <svg version="1.1" id="complete_icon" class="fc-event-task-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
      <style type="text/css">
        .st0{fill:none;}
      </style>
      <path fill=${priorityColors[color]} d="M11.7,2c-0.1,0-0.1,0-0.2,0c0,0,0,0-0.1,0v0c-0.2,0-0.3,0-0.5,0l0.2,2c0.4,0,0.9,0,1.3,0c4,0.3,7.3,3.5,7.5,7.6
        c0.2,4.4-3.2,8.2-7.6,8.4c0,0-0.1,0-0.2,0c-0.3,0-0.7,0-1,0L11,22c0.4,0,0.8,0,1.3,0c0.1,0,0.3,0,0.4,0v0c5.4-0.4,9.5-5,9.3-10.4
        c-0.2-5.1-4.3-9.1-9.3-9.5v0c0,0,0,0,0,0c-0.2,0-0.3,0-0.5,0C12,2,11.9,2,11.7,2z M8.2,2.7C7.7,3,7.2,3.2,6.7,3.5l1.1,1.7
        C8.1,5,8.5,4.8,8.9,4.6L8.2,2.7z M4.5,5.4c-0.4,0.4-0.7,0.9-1,1.3l1.7,1C5.4,7.4,5.7,7.1,6,6.7L4.5,5.4z M15.4,8.4l-4.6,5.2
        l-2.7-2.1L7,13.2l4.2,3.2l5.8-6.6L15.4,8.4z M2.4,9c-0.2,0.5-0.3,1.1-0.3,1.6l2,0.3c0.1-0.4,0.1-0.9,0.3-1.3L2.4,9z M4.1,13l-2,0.2
        c0,0.1,0,0.2,0,0.3c0.1,0.4,0.2,0.9,0.3,1.3l1.9-0.6c-0.1-0.3-0.2-0.7-0.2-1.1L4.1,13z M5.2,16.2l-1.7,1.1c0.3,0.5,0.6,0.9,1,1.3
        L6,17.3C5.7,16.9,5.4,16.6,5.2,16.2z M7.8,18.8l-1.1,1.7c0.5,0.3,1,0.5,1.5,0.8l0.8-1.8C8.5,19.2,8.1,19,7.8,18.8z"/>
      <rect class="st0" width="24" height="24"/>
      </svg>`;
      default:
        return '<img src="build/img/check_logo.svg" alt="Icono fecha final de proyecto" style="margin: auto;">';
  }
}