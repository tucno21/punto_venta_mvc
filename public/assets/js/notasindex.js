const listaTabla = document.querySelector("#simpleDatatable");
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");

const urlReporte = document
  .querySelector("#urlReporte")
  .getAttribute("data-url");

const urlDownloadXml = document
  .querySelector("#urlDownloadXml")
  .getAttribute("data-url");
const urlDownloadCdr = document
  .querySelector("#urlDownloadCdr")
  .getAttribute("data-url");

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
  console.log(data);
  let i = 1;
  data.forEach((element) => {
    element.orden = i;

    let actions = `
    <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte">
          <i class="bi bi-file-earmark-pdf"></i>
        </a>
    <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-three-dots"></i>
    </button>
    <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
      <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
      <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
      <li><a class="dropdown-item p-0 py-1 px-2 downloadCDR" href="${urlDownloadCdr}?xml=${element.nombre_xml}">Descargar CDR</a></li>
    </ul>
    `;

    element["actions"] = actions;

    element["comprobante"] = `
            <p class="p-0 m-0 h6">${element.serie}-${element.correlativo}</p>
            <p class="p-0 m-0"><small>${element.nombre_tipodoc}</small></p>
            `;

    element["estadoSunat"] =
      element.estado_sunat === 1
        ? `<span  class="text-white badge rounded-pill bg-success">Aceptado</span>`
        : `<span  class="text-white badge rounded-pill bg-danger">Sin enviar</span>`;

    i++;
  });

  let newData = {
    headings: [
      "#",
      "Comprobante",
      "Referente",
      "F. Emisión",
      "Total",
      "Vendedor",
      "Sunat",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.comprobante,
        item.serie_ref + "-" + item.correlativo_ref,
        item.fecha_emision,
        "S/. " + item.total,
        item.vendedor,
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
  });
}

//boton reporte
function botonReporte(url) {
  window.open(url, "_blank");
}

//pdf ticket
function botonPdfTicket(url) {
  window.open(url, "_blank");
}

//descargar xml
function botonDownloadXML(url) {
  window.open(url, "_blank");
}

//descargar cdr
function botonDownloadCDR(url) {
  window.open(url, "_blank");
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
