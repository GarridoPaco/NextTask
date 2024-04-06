/**
 * Formulario de eliminación de cuenta.
 * Maneja la eliminación de la cuenta de usuario. Al hacer clic en el botón de eliminación, 
 * muestra un diálogo de confirmación utilizando SweetAlert2. Si se confirma, 
 * se envía el formulario para eliminar la cuenta.
 */
const deleteProfileForm = document.querySelector('#deleteProfileForm');

const deleteProfiletBtn = deleteProfileForm.querySelector('#deleteProfileBtn');
deleteProfiletBtn.onclick = function (e) {
    e.preventDefault();
    Swal.fire({
        title: "¿Estás seguro que quieres eliminar la cuenta?",
        text: "Todos tus proyectos serán eliminados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Borrar"
    }).then((result) => {
        if (result.isConfirmed) {
            deleteProfileForm.submit();
        }
    });
};
