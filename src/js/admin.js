/**
 * Formularios de eliminación de usuarios.
 * Maneja la eliminación de usuarios. Al hacer clic en el botón de eliminación, 
 * muestra un diálogo de confirmación utilizando SweetAlert2. Si se confirma, 
 * el formulario de eliminación se envía.
 */
const deleteUserForms = document.querySelectorAll('#deleteUserForm');

deleteUserForms.forEach(deleteProjectForm => {
    const deleteUserBtn = deleteProjectForm.querySelector('#deleteUserBtn');
    deleteUserBtn.onclick = function (e) {
        e.preventDefault();
        Swal.fire({
            title: "¿Estás seguro que quieres eliminar el usuario?",
            text: "Esta operación no es reversible",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Borrar"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteProjectForm.submit();
            }
        });
    };
    
});