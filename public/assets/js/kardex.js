//busqueda de Producto scaner
const checkedBarcode = document.querySelector("#checkedBarcode");
const checkedNombre = document.querySelector("#checkedNombre");
const grupoBarcode = document.getElementById("grupoBarcode");
const grupoNombre = document.getElementById("grupoNombre");
const inputBuscarBarcode = document.querySelector("#inputBuscarBarcode");
const inputBuscarNombre = document.querySelector("#inputBuscarNombre");

//input de producto id
const productoID = document.querySelector("#productoID");

//busqueda por fechas
const inputFechaInicio = document.querySelector("#inputFechaInicio");
const inputFechaFin = document.querySelector("#inputFechaFin");
const btnBuscarFecha = document.querySelector("#btnBuscarFecha");
const btnReportePdf = document.querySelector("#btnReportePdf");

//url de producto kardex
const urlProductokardex = document
  .querySelector("#urlProductokardex")
  .getAttribute("data-url");
const urlBuscarFechas = document
  .querySelector("#urlBuscarFechas")
  .getAttribute("data-url");
const urlKardexPDF = document
  .querySelector("#urlKardexPDF")
  .getAttribute("data-url");

//cargar todos los documentos
cargarEventListeners();
function cargarEventListeners() {
  //cambio de checkbox
  checkedBarcode.addEventListener("click", cambioCheckedBarcode);
  checkedNombre.addEventListener("click", cambioCheckedNombre);
  //buscar producto
  inputBuscarBarcode.addEventListener("keyup", agregarProductoBarcode);
  inputBuscarNombre.addEventListener("keyup", agregarProductoNombre);

  //buscar por fechas
  btnBuscarFecha.addEventListener("click", buscarPorFecha);
  btnReportePdf.addEventListener("click", generarReportePDF);

  document.addEventListener("DOMContentLoaded", () => {});
}

// cuando selecciona barcode se oculta nombre
function cambioCheckedBarcode(e) {
  //eliminar class d-none grupoBarcode
  grupoBarcode.classList.remove("d-none");
  //agregar class d-none grupoNombre
  grupoNombre.classList.add("d-none");
  inputBuscarBarcode.value = "";
  inputBuscarBarcode.focus();
}
//cuando selecciona nombre se oculta barcode
function cambioCheckedNombre(e) {
  //eliminar class d-none grupoNombre
  grupoNombre.classList.remove("d-none");
  //agregar class d-none grupoBarcode
  grupoBarcode.classList.add("d-none");
  inputBuscarNombre.value = "";
  inputBuscarNombre.focus();
}

//cuando ingresa datos en input barcode
function agregarProductoBarcode(e) {
  if (checkedBarcode.checked) {
    let link = checkedBarcode.getAttribute("data-link");
    let linkCompleto = link + "?codigo=" + e.target.value;
    //cuando da enter llamar funcion
    if (e.keyCode === 13) {
      buscarBarcode(linkCompleto);
      inputBuscarBarcode.value = "";
      inputBuscarBarcode.focus();
    }
  }
}

//traer datos de barcode
async function buscarBarcode(link) {
  const response = await fetch(link);
  const data = await response.json();
  if (data.status) {
    // console.log(data.data);
    productoID.value = data.data.id;
    listaKardex(data.data.id);
  } else {
    toastPersonalizado("error", "No se encontro el producto");
    return;
  }
}

//cuando ingresa datos en input nombre
function agregarProductoNombre(e) {
  if (checkedNombre.checked) {
    let link = checkedNombre.getAttribute("data-link");
    buscarNombreProducto(link);
  }
}

//traer datos por nombre
function buscarNombreProducto(link) {
  const buscarNombre = new Autocomplete(inputBuscarNombre, link);
  buscarNombre.seleccionar = (elemento) => {
    // console.log(elemento);
    productoID.value = elemento.id;
    listaKardex(elemento.id);
  };
}

//lista kardex
async function listaKardex(id) {
  const link = urlProductokardex + "?productoid=" + id;
  const response = await fetch(link);
  const data = await response.json();
  generarTabla(data);
}

//generar tabla
async function generarTabla(data) {
  const tablaKardex = document.querySelector("#tablaKardex");
  //limpiar tabla
  tablaKardex.innerHTML = "";

  let html = "";
  let i = 1;
  data.forEach((prod) => {
    if (prod.tipo == "entrada") {
      prod.entrada = prod.cantidad;
      prod.salida = "";
    }

    if (prod.tipo == "salida") {
      prod.salida = prod.cantidad;
      prod.entrada = "";
    }
    // console.log(prod);
    html += `
    <tr>
        <td>${i}</td>
        <td>${prod.producto}</td>
        <td class="text-center">${prod.comprobante}</td>
        <td class="text-center">${prod.entrada}</td>
        <td class="text-center">${prod.salida}</td>
        <td class="text-center">${prod.fecha}</td>
        <td class="text-center">${prod.stock_actual}</td>
    </tr>
    `;
    i++;
  });
  tablaKardex.innerHTML = html;
}

//buscar por fechas
async function buscarPorFecha(e) {
  e.preventDefault();
  if (productoID.value == "") {
    toastPersonalizado("error", "Debe seleccionar un producto");
    return;
  }
  if (inputFechaInicio.value == "") {
    toastPersonalizado("error", "Debe seleccionar una fecha de inicio");
    return;
  }
  if (inputFechaFin.value == "") {
    toastPersonalizado("error", "Debe seleccionar una fecha de término");
    return;
  }

  //las fechas no deben ser mayores a la fecha actual
  let fechaActual = new Date();
  let fechaInicio = new Date(inputFechaInicio.value);
  let fechaFin = new Date(inputFechaFin.value);
  if (fechaInicio > fechaActual) {
    toastPersonalizado(
      "error",
      "La fecha de inicio no debe ser mayor a la fecha actual"
    );
    return;
  }
  if (fechaFin > fechaActual) {
    toastPersonalizado(
      "error",
      "La fecha de término no debe ser mayor a la fecha actual"
    );
    return;
  }

  if (inputFechaInicio.value > inputFechaFin.value) {
    toastPersonalizado(
      "error",
      "La fecha de inicio debe ser menor a la fecha de término"
    );
    return;
  }

  let link =
    urlBuscarFechas +
    "?productoid=" +
    productoID.value +
    "&fecha_inicio=" +
    inputFechaInicio.value +
    "&fecha_fin=" +
    inputFechaFin.value;

  const response = await fetch(link);
  const data = await response.json();
  generarTabla(data);
}

//generarReportePDF
function generarReportePDF() {
  if (productoID.value == "") {
    toastPersonalizado("error", "Debe seleccionar un producto");
    return;
  }

  if (inputFechaInicio.value != "" && inputFechaFin.value != "") {
    //las fechas no deben ser mayores a la fecha actual
    let fechaActual = new Date();
    let fechaInicio = new Date(inputFechaInicio.value);
    let fechaFin = new Date(inputFechaFin.value);
    if (fechaInicio > fechaActual) {
      toastPersonalizado(
        "error",
        "La fecha de inicio no debe ser mayor a la fecha actual"
      );
      return;
    }
    if (fechaFin > fechaActual) {
      toastPersonalizado(
        "error",
        "La fecha de término no debe ser mayor a la fecha actual"
      );
      return;
    }

    if (inputFechaInicio.value > inputFechaFin.value) {
      toastPersonalizado(
        "error",
        "La fecha de inicio debe ser menor a la fecha de término"
      );
      return;
    }

    let link =
      urlKardexPDF +
      "?productoid=" +
      productoID.value +
      "&fecha_inicio=" +
      inputFechaInicio.value +
      "&fecha_fin=" +
      inputFechaFin.value;
    window.open(link, "_blank");
    return;
  }

  const link = urlKardexPDF + "?productoid=" + productoID.value;
  window.open(link, "_blank");
}
