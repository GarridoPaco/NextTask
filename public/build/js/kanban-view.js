const generalView=!1,kanbanView=!0;function closeModal(){document.querySelectorAll(".modal-overlay").forEach((function(t){t.style.display="none"}))}async function showTasksKanban(){const t=document.getElementById("loading-overlay");try{const e=await getTasks(),n=await getCollaborators(),a=await getAssign(),o=await getUser(),s=await getProject();selectCollaborators(n);document.querySelectorAll("#addTask").forEach((function(t){t.onclick=function(){newTaskModal(o,s)}}));const c=document.querySelector("#tasksListToDo");c.innerHTML="";const r=document.querySelector("#tasksListInProgress");r.innerHTML="";const i=document.querySelector("#tasksListFinish");i.innerHTML="";const l=e.map(t=>{switch(t.status){case"0":return taskBin(t,a,n,c,o,s);case"1":return taskBin(t,a,n,r,o,s);case"2":return taskBin(t,a,n,i,o,s)}});Promise.all(l).then(()=>{showContentTask(),taskActionsMenu();document.querySelectorAll(".checkTask").forEach(t=>{t.addEventListener("click",(async function(){const t=this.closest(".taskContainer").dataset.taskId,n=e.find(e=>e.id===t);n.status=(n.status+1)%3,await updateTask(n),await showTasksKanban()}))}),t.style.display="none"})}catch(e){t.style.display="none",Swal.fire({title:"No se ha podido cargar el proyecto",text:"Inténtelo de nuevo en unos instantes",icon:"error",confirmButtonColor:"#0075beff"}).then(()=>{window.location.href="/dashboard"});document.querySelector(".swal2-container").style.backdropFilter="blur(4px)",console.log(e)}}showTasksKanban();