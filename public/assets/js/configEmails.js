const urlEdit = document.getElementById("urlEdit").getAttribute("data-url");
const btnFormulario = document.getElementById("btnFormulario");

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    btnFormulario.addEventListener("click", editarEmail);
  });
}

async function editarEmail(e) {
  e.preventDefault();
  const servidor = document.querySelector("#inputServidor");
  const correo_servidor = document.querySelector("#inputCorreo");
  const contrasena_servidor = document.querySelector("#inputPassword");
  const puerto = document.querySelector("#inputPuerto");
  const tipo_protocolo = document.querySelector("#inputProtocolo");

  const datos = new FormData();
  datos.append("servidor", servidor.value);
  datos.append("correo_servidor", correo_servidor.value);
  datos.append("contrasena_servidor", contrasena_servidor.value);
  datos.append("puerto", puerto.value);
  datos.append("tipo_protocolo", tipo_protocolo.value);

  try {
    const res = await fetch(urlEdit, {
      method: "POST",
      body: datos,
    });
    const dRes = await res.json();
    if (dRes.status) {
      limpiarErrrorInput([
        servidor,
        correo_servidor,
        contrasena_servidor,
        puerto,
        // tipo_protocolo,
      ]);
      toastPersonalizado("success", "Configuración de correo actualizada");
    } else {
      mensajeErrorInput(servidor, dRes.data.servidor);
      mensajeErrorInput(correo_servidor, dRes.data.correo_servidor);
      mensajeErrorInput(contrasena_servidor, dRes.data.contrasena_servidor);
      mensajeErrorInput(puerto, dRes.data.puerto);
      mensajeErrorInput(tipo_protocolo, dRes.data.tipo_protocolo);
    }
  } catch (error) {
    console.log(error);
  }
}
