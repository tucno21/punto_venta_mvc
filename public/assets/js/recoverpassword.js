const formRecoverPassword = document.getElementById("formRecoverPassword");
cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    formRecoverPassword.addEventListener("submit", recoverPassword);
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

//enviar datos al servidor
async function recoverPassword(e) {
  e.preventDefault();
  const verMensage = document.querySelector("#verMensage");
  //obtener el url de envio
  const url = e.target.action;
  //obtener los datos del formulario
  const data = new FormData();
  data.append("email", document.getElementById("inputEmail").value);

  //enviar datos al servidor
  const res = await fetch(url, {
    method: "POST",
    body: data,
  });
  const respuesta = await res.json();

  if (respuesta.status) {
    limpiarErrrorInput([document.getElementById("inputEmail")]);
    const link = document
      .querySelector("#urlSendMessage")
      .getAttribute("data-url");
    // redireccionar a la pagina de mensaje
    window.location.replace(link);
  } else {
    if (respuesta.message) {
      let html = `<div class="alert alert-danger p-1" role="alert">
      <strong>¡Error!</strong> ${respuesta.message}.
      </div>`;
      verMensage.innerHTML = html;
      return;
    }
    mensajeErrorInput(
      document.getElementById("inputEmail"),
      respuesta.data.email
    );
  }
}
