const urlChangePassword = document
  .querySelector("#urlChangePassword")
  .getAttribute("data-url");

const inputPassword = document.querySelector("#inputPassword");
const inputRepeatPassword = document.querySelector("#inputRepeatPassword");

const btnFormulario = document.querySelector("#btnFormulario");

//cargar todos los documentos
cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    comprobarValoresInputs();
  });
}

function comprobarValoresInputs() {
  btnFormulario.addEventListener("click", (e) => {
    e.preventDefault();
    const password = inputPassword.value;
    const repeatPassword = inputRepeatPassword.value;
    //si es vacio
    if (password === "" || repeatPassword === "") {
      toastPersonalizado("error", "Todos los campos son obligatorios");
      return;
    }

    if (password !== repeatPassword) {
      toastPersonalizado("error", "Las contraseñas no coinciden");
      inputRepeatPassword.value = "";
      return;
    }

    //mayor a 6 caracteres
    if (password.length < 5) {
      toastPersonalizado(
        "error",
        "La contraseña debe ser mayor a 6 caracteres"
      );
      inputRepeatPassword.value = "";
      return;
    }

    //actualizar password
    actualizarPassword(password);
  });
}

//actualizar password
async function actualizarPassword(password) {
  const inputId = document.querySelector("#inputId").value;

  const data = new FormData();
  data.append("id", inputId);
  data.append("password", password);

  const response = await fetch(urlChangePassword, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    toastPersonalizado("success", dRes.message);
    inputRepeatPassword.value = "";
    inputPassword.value = "";
  } else {
    toastPersonalizado("error", "Ocurrio un error");
  }
}
