const listaTabla = document.querySelector("#simpleDatatable");
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");

const urlReporte = document
  .querySelector("#urlReporte")
  .getAttribute("data-url");

const urlDestroy = document
  .querySelector("#urlDestroy")
  .getAttribute("data-url");

const urlEnviarSunat = document
  .querySelector("#urlEnviarSunat")
  .getAttribute("data-url");
const urlDownloadXml = document
  .querySelector("#urlDownloadXml")
  .getAttribute("data-url");
const urlDownloadCdr = document
  .querySelector("#urlDownloadCdr")
  .getAttribute("data-url");
const urlNotasCD = document
  .querySelector("#urlNotasCD")
  .getAttribute("data-url");
const urlIndexNotas = document
  .querySelector("#urlIndexNotas")
  .getAttribute("data-url");
// console.log(urlIndexNotas);

//para reoporte excel y pdf
const urlReportePdf = document
  .querySelector("#urlReportePdf")
  .getAttribute("data-url");
const urlReporteExcel = document
  .querySelector("#urlReporteExcel")
  .getAttribute("data-url");
const inputFechaInicio = document.querySelector("#inputFechaInicio");
const inputFechaFin = document.querySelector("#inputFechaFin");
const btnReportePdf = document.querySelector("#btnReportePdf");
const btnReporteExcel = document.querySelector("#btnReporteExcel");

//enviar correos
const urlSendXml = document
  .querySelector("#urlSendXml")
  .getAttribute("data-url");
const urlSendCdr = document
  .querySelector("#urlSendCdr")
  .getAttribute("data-url");

cargarEventListeners();
function cargarEventListeners() {
  btnReportePdf.addEventListener("click", generarReportePdf);
  btnReporteExcel.addEventListener("click", generarReporteExcel);
  document.addEventListener("DOMContentLoaded", () => {
    generarDataTable();
    botonesDataTable();
  });
}

//mi tabla
let dataTable = new simpleDatatables.DataTable(listaTabla, {
  searchable: true,
  fixedHeight: true,
  labels: {
    placeholder: "Buscar...",
    perPage: "{select} Registros por página",
    noRows: "No hay registros",
    info: "Mostrando {start} a {end} de {rows} registros",
  },
});

//Traer los datos de la tabla
async function generarDataTable() {
  const response = await fetch(urlDataTable);
  const data = await response.json();

  let i = 1;
  data.forEach((element) => {
    element.orden = i;

    let actions;

    if (element.estado_sunat === 0 && element.estado === 1) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
            <i class="bi bi-file-earmark-pdf"></i>
          </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>

        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 sendxml" href="${urlSendXml}?email=${element.id}">Enviar correo (XML)</a></li>
      </ul>
      `;
    } else if (element.estado_sunat === 1 && element.estado === 1) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
            <i class="bi bi-file-earmark-pdf"></i>
          </a>
      <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="Anular venta">
          <i class="bi bi-x-circle"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadCDR" href="${urlDownloadCdr}?xml=${element.nombre_xml}">Descargar CDR</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 generarNotas" href="${urlNotasCD}?id=${element.id}">Nota de C/D</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 sendxml" href="${urlSendXml}?email=${element.id}">Enviar correo (XML)</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 sendcdr" href="${urlSendCdr}?email=${element.id}">Enviar correo (CDR)</a></li>
      </ul>
      `;
    } else if (element.estado_sunat === 1 && element.estado === 0) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
        <i class="bi bi-file-earmark-pdf"></i>
      </a>
      <a href="${urlIndexNotas}" class="btn btn-outline-primary btn-sm btnNotasVentas" title="Ir Listado de Notas">
        <i class="bi bi-sticky"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadCDR" href="${urlDownloadCdr}?xml=${element.nombre_xml}">Descargar CDR</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 sendxml" href="${urlSendXml}?email=${element.id}">Enviar correo (XML)</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 sendcdr" href="${urlSendCdr}?email=${element.id}">Enviar correo (CDR)</a></li>
      </ul>
      `;
    }

    element["actions"] = actions;

    element["estadoSunat"] =
      element.estado_sunat === 1
        ? `<span  class="text-white badge rounded-pill bg-success">Aceptado</span>`
        : `<span  class="text-white badge rounded-pill bg-danger enviarSunat" data-id="${element.id}">Sin enviar</span>`;

    i++;
  });

  let newData = {
    headings: [
      "#",
      "Comprobante",
      "F. Emisión",
      "Total",
      "Cliente",
      "Sunat",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.serie + "-" + item.correlativo,
        item.fecha_emision,
        "S/. " + item.total,
        item.cliente,
        item.estadoSunat,
        item.actions,
      ];
    }),
  };

  // Insert the data
  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
  // colorThead();
}

//botones de la tabla
function botonesDataTable() {
  listaTabla.addEventListener("click", (e) => {
    e.preventDefault();
    //en boton reporte
    if (
      e.target.classList.contains("btnReporte") ||
      e.target.parentElement.classList.contains("btnReporte")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      botonReporte(url);
    }

    //en boton eliminar
    if (
      e.target.classList.contains("btnEliminar") ||
      e.target.parentElement.classList.contains("btnEliminar")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonEliminar(url);
    }

    //en boton enviar sunat
    if (e.target.classList.contains("enviarSunat")) {
      //capturar data-id
      const id = e.target.getAttribute("data-id");
      botonEnviarSunat(id);
    }

    //en boton descargar xml
    if (
      e.target.classList.contains("downloadXML") ||
      e.target.parentElement.classList.contains("downloadXML")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonDownloadXML(url);
    }

    //en boton descargar cdr
    if (
      e.target.classList.contains("downloadCDR") ||
      e.target.parentElement.classList.contains("downloadCDR")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonDownloadCDR(url);
    }

    //en boton pdf ticket
    if (
      e.target.classList.contains("pdfTicket") ||
      e.target.parentElement.classList.contains("pdfTicket")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonPdfTicket(url);
    }

    //en boton generar notas
    if (
      e.target.classList.contains("generarNotas") ||
      e.target.parentElement.classList.contains("generarNotas")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonGenerarNotas(url);
    }

    //btnNotasVentas
    if (
      e.target.classList.contains("btnNotasVentas") ||
      e.target.parentElement.classList.contains("btnNotasVentas")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonNotasVentas(url);
    }

    //sendxml
    if (
      e.target.classList.contains("sendxml") ||
      e.target.parentElement.classList.contains("sendxml")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonSendXML(url);
    }

    //sendcdr
    if (
      e.target.classList.contains("sendcdr") ||
      e.target.parentElement.classList.contains("sendcdr")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonSendCDR(url);
    }
  });
}

//boton eliminar
async function botonEliminar(url) {
  console.log(url);
  //preguntar si desea eliminar
  const { value: accept } = await Swal.fire({
    title: "¿Desea Anular la Venta?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    // showDenyButton: true,
    // confirmButtonText: `SI, eliminar`,
    // denyButtonText: `No`,
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Sí!",
  });

  if (accept) {
    //enviar data
    const response = await fetch(url);
    const data = await response.json();
    if (data.status) {
      generarDataTable();
      toastPersonalizado("success", "Venta Anulada");
    } else {
      toastPersonalizado("error", data.message);
    }
  }
}

//boton reporte
function botonReporte(url) {
  window.open(url, "_blank");
}

//enviar sunat
async function botonEnviarSunat(id) {
  const { value: accept } = await Swal.fire({
    text: "¿Desea enviar la Venta a sunat?",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí",
    cancelButtonText: "No",
  });

  if (accept) {
    const url = urlEnviarSunat + "?id=" + id;
    const response = await fetch(url);
    const data = await response.json();
    if (data.success) {
      generarDataTable();
      toastPersonalizado("success", data.message);
    } else {
      toastPersonalizado("error", data.message);
    }
  }
}

//descargar xml
function botonDownloadXML(url) {
  window.open(url, "_blank");
}

//descargar cdr
function botonDownloadCDR(url) {
  window.open(url, "_blank");
}

//pdf ticket
function botonPdfTicket(url) {
  window.open(url, "_blank");
}

//generar notas
function botonGenerarNotas(url) {
  //abrir url sin _blanck
  window.location.href = url;
}

//botonNotasVentas
function botonNotasVentas(url) {
  window.location.href = url;
}

//genererarReportePdf
function generarReportePdf(e) {
  if (inputFechaInicio.value === "" || inputFechaFin.value === "") {
    toastPersonalizado("error", "Debe ingresar las fechas");
    return;
  }
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

  let url =
    urlReportePdf +
    "?fecha_inicio=" +
    inputFechaInicio.value +
    "&fecha_fin=" +
    inputFechaFin.value;
  window.open(url, "_blank");
}

//generarReporteExcel
function generarReporteExcel(e) {
  if (inputFechaInicio.value === "" || inputFechaFin.value === "") {
    toastPersonalizado("error", "Debe ingresar las fechas");
    return;
  }
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

  let url =
    urlReporteExcel +
    "?fecha_inicio=" +
    inputFechaInicio.value +
    "&fecha_fin=" +
    inputFechaFin.value;
  window.open(url);
}

//botonSendXML
async function botonSendXML(url) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.status) {
    toastPersonalizado("success", data.message);
  } else {
    toastPersonalizado("error", data.message);
  }
}

//botonSendCDR
async function botonSendCDR(url) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.status) {
    toastPersonalizado("success", data.message);
  } else {
    toastPersonalizado("error", data.message);
  }
}
