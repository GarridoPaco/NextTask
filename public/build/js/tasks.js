async function showTasks(e,t,a){let o=await getCollaborators(),n=await getAssign();const r=document.querySelector("#tasks-list");if(r.innerHTML="",0===e.length){const e=document.createElement("P");return e.textContent="¡Es hora de dar un paso adelante en tu proyecto! Agrega nuevas tareas para alcanzar tus objetivos. ¡Cada tarea te acerca un paso más hacia el éxito! Haz clic en el botón de abajo para empezar a añadir tareas ahora mismo.",e.classList.add("no-tasks"),void r.appendChild(e)}const i=e.map(e=>taskBin(e,n,o,r,t,a));Promise.all(i).then(()=>{showContentTask(),taskActionsMenu()})}function newTaskModal(e,t){const a=document.querySelector("#taskModal");a.style.display="flex";const o=document.querySelector("#addTaskBtn");o.textContent="Enviar",document.querySelector("#modalLegend").textContent="Nueva tarea",cleanForm();a.querySelector("#taskDeadline").max=t.deadline;const n=async function(a){a.preventDefault();let r=[];const i=document.querySelector("#taskTitle").value.trim();if(""===i)return void showAlert("El nombre es obligatorio","error");const s=document.querySelector("#taskDescription").value.trim(),l=document.querySelector("#taskDeadline").value.trim();if(""===l)return void showAlert("Introduce una fecha de entrega","error");if(r={title:i,description:s,deadline:l,priority:document.querySelector("#taskPriority").value.trim()||"0",status:document.querySelector("#taskStatus").value.trim(),project_id:t.id},await addTask(r)){let a=await getTasks();generalView&&(showTasks(a,e,t),showCalendar(a,e,t)),kanbanView&&showTasksKanban()}o.removeEventListener("click",n)};o.addEventListener("click",n)}function editTaskModal(e,t,a){document.querySelector("#taskModal").style.display="flex";const o=document.querySelector("#addTaskBtn");o.textContent="Actualizar tarea",document.querySelector("#modalLegend").textContent="Editar tarea",cleanForm();let n=document.querySelector("#taskTitle");n.value=e.title;let r=document.querySelector("#taskDescription");r.value=e.description;let i=document.querySelector("#taskDeadline");i.value=e.deadline,i.max=a.deadline;let s=document.querySelector("#taskPriority");s.value=e.priority;let l=document.querySelector("#taskStatus").value=e.status;t.id!==a.user_id&&(n.setAttribute("readonly",!0),r.setAttribute("readonly",!0),i.setAttribute("readonly",!0),s.disabled=!0);const c=async function(d){if(d.preventDefault(),n=document.querySelector("#taskTitle").value.trim(),""!==n){if(r=document.querySelector("#taskDescription").value.trim(),i=document.querySelector("#taskDeadline").value.trim(),s=document.querySelector("#taskPriority").value.trim(),l=document.querySelector("#taskStatus").value.trim(),e={id:e.id,project_id:e.project_id,status:l,title:n,description:r,deadline:i,priority:s},await updateTask(e)){let e=await getTasks();generalView&&(showTasks(e,t,a),showCalendar(e,t,a)),kanbanView&&showTasksKanban()}o.removeEventListener("click",c)}else showAlert("El nombre es obligatorio","error")};o.addEventListener("click",c)}function cleanForm(){document.querySelector("#taskTitle").value="",document.querySelector("#taskDescription").value="",document.querySelector("#taskDeadline").value="",document.querySelector("#taskPriority").value=""}async function addTask(e){const t=new FormData;t.append("title",e.title),t.append("description",e.description),t.append("deadline",e.deadline),t.append("priority",e.priority),t.append("status",e.status),t.append("url",getUrlProject());try{const e=location.origin+"/api/task",a=await fetch(e,{method:"POST",body:t}),o=await a.json();if("exito"===o.type){return document.querySelector("#taskModal").style.display="none",infoAction.fire({icon:"success",title:o.message}),!0}}catch(e){return infoAction.fire({icon:"error",title:"No se ha podido añadir la tarea"}),console.log(e),!1}}async function updateTask(e){const t=new FormData;t.append("id",e.id),t.append("project_id",e.project_id),t.append("status",e.status),t.append("title",e.title),t.append("description",e.description),t.append("deadline",e.deadline),t.append("priority",e.priority),t.append("url",getUrlProject());try{const e=location.origin+"/api/task/update",a=await fetch(e,{method:"POST",body:t}),o=await a.json();if("exito"===o.type){return document.querySelector("#taskModal").style.display="none",infoAction.fire({icon:"success",title:o.message}),!0}}catch(e){return infoAction.fire({icon:"error",title:"No se ha podido actualizar la tarea"}),console.log(e),!1}}async function deleteTask(e){const t=new FormData;t.append("id",e.id),t.append("url",getUrlProject());try{const e=location.origin+"/api/task/delete",a=await fetch(e,{method:"POST",body:t}),o=await a.json();if("exito"===o.type){return document.querySelector("#taskModal").style.display="none",infoAction.fire({icon:"success",title:o.message}),!0}}catch(e){return infoAction.fire({icon:"error",title:"No se ha podido eliminar la tarea"}),console.log(e),!1}}