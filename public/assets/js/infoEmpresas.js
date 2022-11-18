//url - enlaces
// const urlDataTable = document
//   .querySelector("#urlDataTable")
//   .getAttribute("data-url");
// const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");
const urlEdit = document.querySelector("#urlEdit").getAttribute("data-url");
const urlGeneral = document
  .querySelector("#urlGeneral")
  .getAttribute("data-url");
// const urlStatus = document.querySelector("#urlStatus").getAttribute("data-url");
// const urlDestroy = document
//   .querySelector("#urlDestroy")
//   .getAttribute("data-url");
//modal y botones
// const listaTabla = document.querySelector("#simpleDatatable");
// const modalInputs = new bootstrap.Modal("#modalInputs");
// const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const listId = document.querySelector("#listId");
const inputTipoDoc = document.querySelector("#inputTipoDoc");
const inputRuc = document.querySelector("#inputRuc");
const inputRazon = document.querySelector("#inputRazon");
const inputNombre = document.querySelector("#inputNombre");
const inputDireccion = document.querySelector("#inputDireccion");
const inputPais = document.querySelector("#inputPais");
const inputDepart = document.querySelector("#inputDepart");
const inputProvincia = document.querySelector("#inputProvincia");
const inputDistrito = document.querySelector("#inputDistrito");
const inputUbigeo = document.querySelector("#inputUbigeo");
const inputTelf = document.querySelector("#inputTelf");
const inputEmail = document.querySelector("#inputEmail");
const inputuser = document.querySelector("#inputuser");
const inputClave = document.querySelector("#inputClave");
const inputDescripcion = document.querySelector("#inputDescripcion");
const inputLogo = document.querySelector("#inputLogo");

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    inEmpresa();
    botonFormulario();
    verImagen();
  });
}

async function inEmpresa() {
  const response = await fetch(urlEdit);
  const data = await response.json();
  if (data.status) {
    const empresa = data.data;
    listId.value = empresa.id;
    inputTipoDoc.value = empresa.tipodoc;
    inputRuc.value = empresa.ruc;
    inputRazon.value = empresa.razon_social;
    inputNombre.value = empresa.nombre_comercial;
    inputDireccion.value = empresa.direccion;
    inputPais.value = empresa.pais;
    inputDepart.value = empresa.departamento;
    inputProvincia.value = empresa.provincia;
    inputDistrito.value = empresa.distrito;
    inputUbigeo.value = empresa.ubigeo;
    inputTelf.value = empresa.telefono;
    inputEmail.value = empresa.email;
    inputuser.value = empresa.usuario_secundario;
    inputClave.value = empresa.clave_usuario_secundario;
    inputDescripcion.value = empresa.descripcion;
    // inputLogo.value = empresa.logo;
    if (empresa.logo != null) {
      const imagen = document.querySelector(".previsualizar");
      imagen.src = `${urlGeneral}/assets/img/${empresa.logo}`;
    } else {
      const imagen = document.querySelector(".previsualizar");
      imagen.src = `${urlGeneral}/assets/img/producto.png`;
    }
  }
}

//boton de formulario
function botonFormulario() {
  btnFormulario.addEventListener("click", (e) => {
    enviarEditarFormulario();
  });
}

//enviar formulario para editar
async function enviarEditarFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("tipodoc", inputTipoDoc.value);
  data.append("ruc", inputRuc.value);
  data.append("razon_social", inputRazon.value);
  data.append("nombre_comercial", inputNombre.value);
  data.append("direccion", inputDireccion.value);
  data.append("pais", inputPais.value);
  data.append("departamento", inputDepart.value);
  data.append("provincia", inputProvincia.value);
  data.append("distrito", inputDistrito.value);
  data.append("ubigeo", inputUbigeo.value);
  data.append("telefono", inputTelf.value);
  data.append("email", inputEmail.value);
  data.append("usuario_secundario", inputuser.value);
  data.append("clave_usuario_secundario", inputClave.value);
  data.append("descripcion", inputDescripcion.value);
  data.append("logo", inputLogo.files[0]);

  //enviar data
  const response = await fetch(urlEdit, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    limpiarErrrorInput2([
      inputTipoDoc,
      inputRuc,
      inputRazon,
      inputNombre,
      inputDireccion,
      inputPais,
      inputDepart,
      inputProvincia,
      inputDistrito,
      inputUbigeo,
      inputTelf,
      inputEmail,
      inputuser,
      inputClave,
      inputDescripcion,
    ]);
    toastPersonalizado("success", "datos de empresa actualizados");
  } else {
    mensajeErrorInput(inputTipoDoc, dRes.data.tipodoc);
    mensajeErrorInput(inputRuc, dRes.data.ruc);
    mensajeErrorInput(inputRazon, dRes.data.razon_social);
    mensajeErrorInput(inputNombre, dRes.data.nombre_comercial);
    mensajeErrorInput(inputDireccion, dRes.data.direccion);
    mensajeErrorInput(inputPais, dRes.data.pais);
    mensajeErrorInput(inputDepart, dRes.data.departamento);
    mensajeErrorInput(inputProvincia, dRes.data.provincia);
    mensajeErrorInput(inputDistrito, dRes.data.distrito);
    mensajeErrorInput(inputUbigeo, dRes.data.ubigeo);
    mensajeErrorInput(inputTelf, dRes.data.telefono);
    mensajeErrorInput(inputEmail, dRes.data.email);
    mensajeErrorInput(inputuser, dRes.data.usuario_secundario);
    mensajeErrorInput(inputClave, dRes.data.clave_usuario_secundario);
    mensajeErrorInput(inputDescripcion, dRes.data.descripcion);
    // mensajeErrorInput(inputLogo, dRes.data.logo);
  }
}

//limpiar mensaje de error
function limpiarErrrorInput2(array) {
  array.forEach((input) => {
    input.classList.remove("is-invalid");
    if (input.nextElementSibling) {
      input.nextElementSibling.remove();
    }
  });
}

//ver imagen
function verImagen() {
  imagen(".inputFoto", ".previsualizar");
}
