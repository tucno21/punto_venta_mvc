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

function imagen(inputFoto, previsualizar) {
  let visorFoto = document.querySelector(inputFoto);
  visorFoto.addEventListener("change", function (e) {
    console.log(e.target.files[0]);
    let file = e.target.files[0];
    //validar que sea una imagen
    if (
      file["type"] != "image/jpg" &&
      file["type"] != "image/png" &&
      file["type"] != "image/jpeg"
    ) {
      visorFoto.value = "";
      Swal.fire({
        title: "Error al subir imagen",
        text: "la imagen debe ser de formato JPQ o PNG",
        icon: "error",
        confirmButtonText: "Cerrar",
      });
      //validar tamaño de la imagen
    } else if (file["size"] > 1000000) {
      visorFoto.value = "";
      Swal.fire({
        title: "Error de tamaño de imagen",
        text: "la imagen no debe pesar mas de 1MB",
        icon: "error",
        confirmButtonText: "Cerrar",
      });
    } else {
      //clase de js hace lectura de archivo
      var datosImagen = new FileReader();
      //leer como dato url la imagen cargada
      datosImagen.readAsDataURL(file);
      //cuando la imagen este cargada
      datosImagen.addEventListener("load", function (event) {
        //asignar la imagen al elemento img
        document
          .querySelector(previsualizar)
          .setAttribute("src", event.target.result);
      });
    }
  });
}
