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
const urlBoleta = document.querySelector("#urlBoleta").getAttribute("data-url");
const urlFactura = document
  .querySelector("#urlFactura")
  .getAttribute("data-url");
const urlUpdateElectronico = document
  .querySelector("#urlUpdateElectronico")
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
  let i = 1;
  data.forEach((element) => {
    element.orden = i;

    let actions;

    if (element.estado_sunat === 0 && element.estado === 1) {
      if (element.documentocliente.length == 11) {
        actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
            <i class="bi bi-file-earmark-pdf"></i>
      </a>
      <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="ELimininar venta Interna">
            <i class="bi bi-trash3"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 generarBoleta" href="${urlBoleta}?id=${element.id}" data-id="${element.id}">Generar Boleta</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 generarFactura" href="${urlFactura}?id=${element.id}" data-id="${element.id}">Generar Factura</a></li>
      </ul>
      `;
      } else {
        actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
            <i class="bi bi-file-earmark-pdf"></i>
      </a>
      <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="ELimininar venta Interna">
          <i class="bi bi-trash3"></i>
      </a>
          
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
        <li><a class="dropdown-item p-0 py-1 px-2 generarBoleta" href="${urlBoleta}?id=${element.id}" data-id="${element.id}">Generar Boleta</a></li>
      </ul>
      `;
      }
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
      </ul>
      `;
    } else if (element.estado_sunat === 1 && element.estado === 0) {
      actions = `
      <a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5">
        <i class="bi bi-file-earmark-pdf"></i>
      </a>
        <i class="bi bi-sticky"></i>
      </a>
      <button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots"></i>
      </button>
      <ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
        <li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?ticket=${element.id}">Pdf Ticket</a></li>
      </ul>
      `;
    }

    element["actions"] = actions;

    element["estadoSunat"] =
      element.estado_sunat === 1
        ? `<span  class="text-white badge rounded-pill bg-success">Comprobante Electronico</span>`
        : `<span  class="text-white badge rounded-pill bg-primary">Venta Interna</span>`;

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

    //en boton  generarBoleta
    if (
      e.target.classList.contains("generarBoleta") ||
      e.target.parentElement.classList.contains("generarBoleta")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      const id =
        e.target.parentElement.getAttribute("data-id") ||
        e.target.getAttribute("data-id");

      botonGenerarBoleta(url, id);
    }

    //en boton  generarFactura
    if (
      e.target.classList.contains("generarFactura") ||
      e.target.parentElement.classList.contains("generarFactura")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      const id =
        e.target.parentElement.getAttribute("data-id") ||
        e.target.getAttribute("data-id");

      botonGenerarFactura(url, id);
    }
  });
}

//boton eliminar
async function botonEliminar(url) {
  console.log(url);
  //preguntar si desea eliminar
  const { value: accept } = await Swal.fire({
    title: "¿Desea Eliminar la Venta Interna?",
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
      toastPersonalizado("success", data.message);
    } else {
      toastPersonalizado("error", data.message);
    }
  }
}

//boton reporte
function botonReporte(url) {
  window.open(url, "_blank");
}

//pdf ticket
function botonPdfTicket(url) {
  window.open(url, "_blank");
}

//botonGenerarBoleta
async function botonGenerarBoleta(url, id) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.status) {
    const link = urlUpdateElectronico + "?id=" + id + "&venta_id=" + data.id;
    const response2 = await fetch(link);
    const data2 = await response2.json();

    if (data2.status) {
      generarDataTable();
      toastPersonalizado("success", data.Message);
    }
  } else {
    toastPersonalizado("error", data.Message);
  }
}

//botonGenerarFactura
async function botonGenerarFactura(url, id) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.status) {
    const link = urlUpdateElectronico + "?id=" + id + "&venta_id=" + data.id;
    const response2 = await fetch(link);
    const data2 = await response2.json();

    if (data2.status) {
      generarDataTable();
      toastPersonalizado("success", data.Message);
    }
  } else {
    toastPersonalizado("error", data.Message);
  }
}
