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

cargarEventListeners();
function cargarEventListeners() {
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

    let actions;

    if (element.estado === 1 && element.estado_sunat === 1) {
      //agregar a actions
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte">
            <i class="bi bi-file-earmark-pdf"></i>
          </a>
      <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar">
          <i class="bi bi-trash3"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadCDR" href="${urlDownloadCdr}?xml=${element.nombre_xml}">Descargar CDR</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 generarNotaDevito" href="${urlReporte}?pdfA5=${element.id}">Nota de Crédito</a></li>
      </ul>
      `;
    } else if (element.estado === 1 && element.estado_sunat === 0) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte">
            <i class="bi bi-file-earmark-pdf"></i>
          </a>
      <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar">
          <i class="bi bi-trash3"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 enviarSunat" href="${urlEnviarSunat}?id=${element.id}">Enviar Sunat</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
      </ul>
      `;
    } else if (element.estado === 0 && element.estado_sunat === 1) {
      actions = `
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
        <li><a class="dropdown-item p-0 py-1 px-2 generarNotaDevito" href="${urlReporte}?pdfA5=${element.id}">Nota de Crédito</a></li>
      </ul>
      `;
    } else if (element.estado === 0 && element.estado_sunat === 0) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte">
            <i class="bi bi-file-earmark-pdf"></i>
          </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 enviarSunat" href="${urlReporte}?id=${element.id}">Enviar Sunat</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 downloadXML" href="${urlDownloadXml}?xml=${element.nombre_xml}">Descargar XML</a></li>
      </ul>
      `;
    }

    element["actions"] = actions;

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
      "F. Emisión",
      "Total",
      "Vendedor",
      "Sunat",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.serie + "-" + item.correlativo,
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
    if (
      e.target.classList.contains("enviarSunat") ||
      e.target.parentElement.classList.contains("enviarSunat")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonEnviarSunat(url);
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

//boton eliminar
async function botonEliminar(url) {
  //preguntar si desea eliminar
  const { value: accept } = await Swal.fire({
    title: "¿Desea Anular la Compra?",
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
      toastPersonalizado("success", "Compra Anulada");
    }
  }
}

//boton reporte
function botonReporte(url) {
  window.open(url, "_blank");
}

//enviar sunat
async function botonEnviarSunat(url) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.success) {
    generarDataTable();
    toastPersonalizado("success", data.message);
  } else {
    toastPersonalizado("error", data.message);
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
