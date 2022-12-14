const formResetPassword = document.getElementById("formResetPassword");
cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    formResetPassword.addEventListener("submit", resetPassword);
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

//reset password
async function resetPassword(e) {
  e.preventDefault();
  const url = e.target.action;
  const password = document.querySelector("#inputPassword");
  const repeatPassword = document.querySelector("#inputRepeatPassword");
  const verMensage = document.querySelector("#verMensage");

  //si es vacio
  if (password.value === "" || repeatPassword.value === "") {
    let html = `<div class="alert alert-danger p-1" role="alert">
    <strong>¡Error!</strong> Todos los campos son obligatorios.
    </div>`;
    verMensage.innerHTML = html;
    return;
  }

  if (password.value !== repeatPassword.value) {
    let html = `<div class="alert alert-danger p-1" role="alert">
    <strong>¡Error!</strong> Las contraseñas no coinciden.
    </div>`;
    verMensage.innerHTML = html;
    repeatPassword.value = "";
    return;
  }

  //mayor a 6 caracteres
  if (password.value.length < 5) {
    let html = `<div class="alert alert-danger p-1" role="alert">
    <strong>¡Error!</strong> La contraseña debe tener al menos 5 caracteres.
    </div>`;
    verMensage.innerHTML = html;
    repeatPassword.value = "";
    return;
  }

  //capturar la url actual
  const link = window.location.href;
  //obtener el token ?token=
  const token = link.split("?token=")[1];

  const data = new FormData();
  data.append("password", password.value);
  data.append("token", token);

  // enviar datos al servidor
  const res = await fetch(url, {
    method: "POST",
    body: data,
  });
  const respuesta = await res.json();

  if (respuesta.status) {
    password.value = "";
    repeatPassword.value = "";
    limpiarErrrorInput([password]);
    let html = `<div class="alert alert-success p-1" role="alert">
    <strong>¡Exito!</strong> ${respuesta.message}.
    </div>`;
    verMensage.innerHTML = html;
    // redireccionar a la pagina de mensaje
    // window.location.replace(link);
  } else {
    mensajeErrorInput(password, respuesta.data.password);
  }
}
