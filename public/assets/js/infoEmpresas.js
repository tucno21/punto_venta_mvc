const urlEdit = document.querySelector("#urlEdit").getAttribute("data-url");
const urlGeneral = document
  .querySelector("#urlGeneral")
  .getAttribute("data-url");

const btnFirmaDigital = document.querySelector("#btnFirmaDigital");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnFormularioFirma = document.querySelector("#btnFormularioFirma");

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

//FIRMA
const urlFirmaDigital = document
  .querySelector("#urlFirmaDigital")
  .getAttribute("data-url");
const urlVerFirmaDigital = document
  .querySelector("#urlVerFirmaDigital")
  .getAttribute("data-url");

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    inEmpresa();
    botonFormulario();
    verImagen();
    btnFirmaDigitalShow();
    fechaVencimiento();
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

//btnbtnFirmaDigitalShow
function btnFirmaDigitalShow() {
  btnFirmaDigital.addEventListener("click", (e) => {
    e.preventDefault();
    // modalInputs
    modalInputs.show();
    enviarFormularioFirmaDigital();
  });
}

function enviarFormularioFirmaDigital() {
  btnFormularioFirma.addEventListener("click", async (e) => {
    e.preventDefault();
    const inputComprobante = document.querySelector("#inputComprobante");
    const inputPassword = document.querySelector("#inputPassword");
    const inputFechaV = document.querySelector("#inputFechaV");

    const data = new FormData();
    data.append("password_firma", inputPassword.value);
    data.append("fecha_venc", inputFechaV.value);
    data.append("firma_digital", inputComprobante.files[0]);

    //enviar data
    const response = await fetch(urlFirmaDigital, {
      method: "POST",
      body: data,
    });
    const dRes = await response.json();

    if (dRes.status) {
      limpiarErrrorInput([inputComprobante, inputPassword, inputFechaV]);
      toastPersonalizado("success", "datos de firma digital actualizados");
      fechaVencimiento();
      //cerrar modal
      modalInputs.hide();
    } else {
      mensajeErrorInput(inputComprobante, dRes.data.firma_digital);
      mensajeErrorInput(inputPassword, dRes.data.password_firma);
      mensajeErrorInput(inputFechaV, dRes.data.fecha_venc);
    }
  });
}

//fecha vencimiento
async function fechaVencimiento() {
  const response = await fetch(urlVerFirmaDigital);
  const dRes = await response.json();
  //mostrar cuantos años, meses y dias faltan en letras
  const fechaVencimiento = dRes.fecha_venc;
  const fechaVencimientoDate = new Date(fechaVencimiento);
  const fechaActual = new Date();
  //restar fechaVencimientoDate - fechaActual
  const resta = fechaVencimientoDate - fechaActual;

  //mostrar cuantos años
  const anios = Math.floor(resta / (1000 * 60 * 60 * 24 * 365));
  //cuantos meses falta despues de quitar los años
  const meses = Math.floor(
    (resta % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30)
  );
  //cuantos dias falta despues de quitar los meses
  const dias = Math.floor(
    (resta % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24)
  );

  //mostrar en el html
  const resultado = `Vence en: ${anios} años, ${meses} meses y ${dias} dias`;

  const buttonMostrarFecha = document.querySelector("#buttonMostrarFecha");
  buttonMostrarFecha.innerHTML = resultado;
}
