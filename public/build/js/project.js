async function showProject(e){document.querySelector("#projectName").textContent=e.name,document.querySelector("#projectDescription").textContent=e.description,document.querySelector("#projectDeadline").textContent=formatDate(e.deadline)}async function editModalProject(e,t,o){document.querySelector("#projectModal").style.display="flex";const n=document.querySelector("#addProjectBtn");n.textContent="Actualizar proyecto",document.querySelector("#modalLegendProject").textContent="Actualizar Proyecto";let r=document.querySelector("#projectNameInput").value=o.name,c=document.querySelector("#projectDescriptionInput").value=o.description,i=document.querySelector("#projectDeadlineInput").value=o.deadline;const a=function(d){if(d.preventDefault(),r=document.querySelector("#projectNameInput").value.trim(),""===r)return void showAlert("El nombre es obligatorio","error");c=document.querySelector("#projectDescriptionInput").value.trim(),i=document.querySelector("#projectDeadlineInput").value.trim();const l={id:o.id,user_id:o.user_id,name:r,description:c,deadline:i};updateProject(e,t,l),n.removeEventListener("click",a)};n.addEventListener("click",a)}async function updateProject(e,t,o){const n=new FormData;n.append("id",o.id),n.append("user_id",o.user_id),n.append("name",o.name),n.append("description",o.description),n.append("deadline",o.deadline),n.append("url",getUrlProject());try{const r=location.origin+"/api/project/update",c=await fetch(r,{method:"POST",body:n}),i=await c.json();if("exito"===i.type){document.querySelector("#projectModal").style.display="none",infoAction.fire({icon:"success",title:i.message}),showProject(o),showCalendar(e,t,o)}}catch(e){infoAction.fire({icon:"error",title:"No se ha podido actualizar el proyecto"}),console.log(e)}}async function deleteProject(){const e=new FormData;e.append("url",getUrlProject());try{const t=location.origin+"/api/project/delete",o=await fetch(t,{method:"POST",body:e}),n=await o.json();if("exito"===n.type){document.querySelector("#taskModal").style.display="none",infoAction.fire({icon:"success",title:n.message}),window.location.href="/dashboard"}}catch(e){infoAction.fire({icon:"error",title:"No se ha podido eliminar el proyecto"}),console.log(e)}}