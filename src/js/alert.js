// Modal que muestra la información de la acción realizada
const infoAction = Swal.mixin({
    toast: true,
    color: "#0a3a67ff",
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
    customClass: {
      title: 'font-size: 3rem;'
    }
  });