//funcion toast personalizado
function toastPersonalizado(icon, title) {
  Swal.fire({
    toast: true,
    position: "top-right",
    icon: icon,
    title: title,
    showConfirmButton: false,
    timer: 2000,
    //modificar color
    iconColor: "white",
    customClass: {
      popup: "colored-toast",
    },
    timerProgressBar: true,
  });
}
//mostrar mensaje de error
function mensajeErrorInput(input, mensaje) {
  if (input.nextElementSibling) {
    input.nextElementSibling.remove();
  }
  if (mensaje == undefined) {
    input.classList.remove("is-invalid");
  } else {
    // crear div
    let div = document.createElement("div");
    div.classList.add("invalid-feedback");
    div.innerHTML = mensaje;
    //agregar is-invalid
    input.classList.add("is-invalid");
    input.parentElement.appendChild(div);
  }
}
//limpiar mensaje de error
function limpiarErrrorInput(array) {
  array.forEach((input) => {
    input.value = "";
    input.classList.remove("is-invalid");
    if (input.nextElementSibling) {
      input.nextElementSibling.remove();
    }
  });
}

//boton para crear
// function botonCrear(btn, array, btnEnviar, modal) {
//   btn.addEventListener("click", () => {
//     limpiarErrrorInput(array);
//     //agregar clase btnCrear botonPermiso
//     btnEnviar.classList.add("btnCrear");
//     //eliminar clase btnCrear botonPermiso
//     btnEnviar.classList.remove("btnEditar");
//     //cambiar texto de boton
//     btnEnviar.textContent = "Crear Permiso";
//     modal.show();
//   });
// }
