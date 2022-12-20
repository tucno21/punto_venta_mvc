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
const urlVentaInterna = document
  .querySelector("#urlVentaInterna")
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

    let estadoFecha = "";
    //============================== FECHAS
    let fecha_emision = new Date(element.fecha_emision);
    //sumar a la element.fecha_emision 18-12-2022 el  element.tiempo 5
    let fecha_vencimiento = new Date(element.fecha_emision);
    fecha_vencimiento.setDate(fecha_vencimiento.getDate() + element.tiempo);

    //comparar si la fecha de vencimiento es menor a la fecha actual
    if (fecha_vencimiento < new Date()) {
      estadoFecha = false;
      //mostrar en forma de texto los dias que paso en letras
      element[
        "fecha_vencimiento"
      ] = `<span  class="text-white badge rounded-pill bg-danger">Venció</span>`;
      // "Venció : " + Math.floor((new Date() - fecha_vencimiento) / (1000 * 60 * 60 * 24)).toString() + " días";
    } else if (fecha_vencimiento > new Date()) {
      estadoFecha = true;
      let resultado = "";
      if (element.estado === 0 && element.estado_sunat === 0) {
        resultado = "Anulada";
      }
      if (
        (element.estado === 1 && element.estado_sunat === 1) ||
        element.estado_sunat === 2
      ) {
        resultado = "Venta";
      }
      if (element.estado === 1 && element.estado_sunat === 0) {
        resultado =
          "Falta : " +
          Math.floor(
            (fecha_vencimiento - new Date()) / (1000 * 60 * 60 * 24)
          ).toString() +
          " días";
      }

      element[
        "fecha_vencimiento"
      ] = `<span  class="text-white badge rounded-pill bg-success">${resultado}</span>`;
    }

    element["fecha_emision"] =
      fecha_emision.getDate() +
      "/" +
      (fecha_emision.getMonth() + 1) +
      "/" +
      fecha_emision.getFullYear();
    //==============================

    const listaMenu = {
      reporteA5: `<a href="${urlReporte}?pdfA5=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="pdf A5"><i class="bi bi-file-earmark-pdf"></i></a>`,
      destroy: `<a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="ELimininar venta Interna"><i class="bi bi-trash3"></i></a>`,
      menuPdfA4: `<li><a class="dropdown-item p-0 py-1 px-2 pdfTicket" href="${urlReporte}?pdfA4=${element.id}">Pdf A4</a></li>`,
      menuVentaInterna: `<li><a class="dropdown-item p-0 py-1 px-2 generarVentaInterna" href="${urlVentaInterna}?id=${element.id}" data-id="${element.id}">Generar Venta Interna</a></li>`,
      menuBoleta: `<li><a class="dropdown-item p-0 py-1 px-2 generarBoleta" href="${urlBoleta}?id=${element.id}" data-id="${element.id}">Generar Boleta</a></li>`,
      menuFactura: `<li><a class="dropdown-item p-0 py-1 px-2 generarFactura" href="${urlFactura}?id=${element.id}" data-id="${element.id}">Generar Factura</a></li>`,
      MENUINICIO: `<button class="btn btn-outline-warning btn-sm rounded-circle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">`,
      MENUFIN: `</ul>`,
    };

    let actions;
    if (
      !estadoFecha ||
      element.estado === 0 ||
      element.estado_sunat === 2 ||
      element.estado_sunat === 1
    ) {
      actions =
        listaMenu.reporteA5 +
        listaMenu.MENUINICIO +
        listaMenu.menuPdfA4 +
        listaMenu.MENUFIN;
    } else if (estadoFecha) {
      if (element.estado_sunat === 0 && element.estado === 1) {
        if (element.documentocliente.length == 11) {
          actions =
            listaMenu.reporteA5 +
            listaMenu.destroy +
            listaMenu.MENUINICIO +
            listaMenu.menuPdfA4 +
            listaMenu.menuVentaInterna +
            listaMenu.menuBoleta +
            listaMenu.menuFactura +
            listaMenu.MENUFIN;
        } else {
          actions =
            listaMenu.reporteA5 +
            listaMenu.destroy +
            listaMenu.MENUINICIO +
            listaMenu.menuPdfA4 +
            listaMenu.menuVentaInterna +
            listaMenu.menuBoleta +
            listaMenu.MENUFIN;
        }
      }
    }

    element["actions"] = actions;

    const menuSunat = {
      cotizacion: `<span  class="text-white badge rounded-pill bg-primary">Cotización</span>`,
      ventaInterna: `<span  class="text-white badge rounded-pill bg-primary">V. Interna</span>`,
      ventaSunat: `<span  class="text-white badge rounded-pill bg-success">Género XML</span>`,
      anulado: `<span  class="text-white badge rounded-pill bg-danger">V. Anulado</span>`,
    };

    let estadoSunat = "";

    if (element.estado === 0) estadoSunat = menuSunat.anulado;
    if (element.estado === 1 && element.estado_sunat === 0)
      estadoSunat = menuSunat.cotizacion;
    if (element.estado === 1 && element.estado_sunat === 1)
      estadoSunat = menuSunat.ventaSunat;
    if (element.estado === 1 && element.estado_sunat === 2)
      estadoSunat = menuSunat.ventaInterna;

    element["estadoSunat"] = estadoSunat;

    i++;
  });

  let newData = {
    headings: [
      "#",
      "Comprobante",
      "F. Emisión",
      "F. Vencimiento",
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
        item.fecha_vencimiento,
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

    //generarVentaInterna
    if (
      e.target.classList.contains("generarVentaInterna") ||
      e.target.parentElement.classList.contains("generarVentaInterna")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      const id =
        e.target.parentElement.getAttribute("data-id") ||
        e.target.getAttribute("data-id");

      botonGenerarVentaInterna(url, id);
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
      toastPersonalizado("success", data.message);
    }
  } else {
    toastPersonalizado("error", data.message);
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
      toastPersonalizado("success", data.message);
    }
  } else {
    toastPersonalizado("error", data.message);
  }
}

//botonGenerarVentaInterna
async function botonGenerarVentaInterna(url, id) {
  const response = await fetch(url);
  const data = await response.json();
  if (data.status) {
    const link =
      urlUpdateElectronico + "?id=" + id + "&ventainterna_id=" + data.id;
    const response2 = await fetch(link);
    const data2 = await response2.json();

    if (data2.status) {
      generarDataTable();
      toastPersonalizado("success", data.message);
    }
  } else {
    toastPersonalizado("error", data.message);
  }
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
