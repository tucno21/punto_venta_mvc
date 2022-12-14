const formLogin = document.getElementById("formLogin");
cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    formLogin.addEventListener("submit", iniciarSession);
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

async function iniciarSession(e) {
  e.preventDefault();
  const url = e.target.action;
  const email = document.querySelector("#inputEmail");
  const password = document.querySelector("#inputPassword");
  const verMensage = document.querySelector("#verMensage");

  //enviar datos
  const datos = new FormData();
  datos.append("email", email.value);
  datos.append("password", password.value);

  const res = await fetch(url, {
    method: "POST",
    body: datos,
  });
  const respuesta = await res.json();

  if (respuesta.status) {
    let html = `<div class="alert alert-success p-1" role="alert">
            <strong>¡Exito!</strong> ${respuesta.message}.
            </div>`;
    verMensage.innerHTML = html;
    // setTimeout(() => {
    const link = window.location.href;
    window.location.replace(link);
    // }, 2000);
  } else {
    if (respuesta.message) {
      let html = `<div class="alert alert-danger p-1" role="alert">
              <strong>¡Error!</strong> ${respuesta.message}.
              </div>`;
      verMensage.innerHTML = html;
    }
    mensajeErrorInput(email, respuesta.data.email);
    mensajeErrorInput(password, respuesta.data.password);
  }
}
