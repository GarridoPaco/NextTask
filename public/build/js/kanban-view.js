const generalView=!1,kanbanView=!0;function closeModal(){document.querySelectorAll(".modal-overlay").forEach((function(t){t.style.display="none"}))}async function showTasksKanban(){try{const t=document.getElementById("loading-overlay"),a=await getTasks(),e=await getCollaborators();selectCollaborators(e);const n=await getAssign(),s=await getUser(),o=await getProject();document.querySelectorAll("#addTask").forEach((function(t){t.onclick=function(){newTaskModal(a,s,o)}}));const c=document.querySelector("#tasksListToDo");c.innerHTML="";const i=document.querySelector("#tasksListInProgress");i.innerHTML="";const r=document.querySelector("#tasksListFinish");r.innerHTML="";const l=a.map(t=>{switch(t.status){case"0":return taskBin(a,t,n,e,c,s,o);case"1":return taskBin(a,t,n,e,i,s,o);case"2":return taskBin(a,t,n,e,r,s,o)}});Promise.all(l).then(()=>{showContentTask(),taskActionsMenu();document.querySelectorAll(".checkTask").forEach(t=>{t.addEventListener("click",(async function(){const t=this.closest(".taskContainer").dataset.taskId,e=a.find(a=>a.id===t);e.status=(e.status+1)%3,await updateTask(e),await showTasksKanban()}))}),t.style.display="none"})}catch(t){loadingImgView.style.display="none",infoAction.fire({icon:"error",title:"No se ha podido cargar la pagina"}),console.log(t)}}showTasksKanban();