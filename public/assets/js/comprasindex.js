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
    i++;

    const lista = {
      reporte: `<a href="${urlReporte}?id=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="Ver reporte pdf"><i class="bi bi-file-earmark-pdf"></i></a>`,
      eliminar: `<a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="anular la compra"><i class="bi bi-trash3"></i></a>`,
    };

    element["actions"] =
      element.estado === 1 ? lista.reporte + lista.eliminar : lista.reporte;
  });

  let newData = {
    headings: [
      "#",
      "Comprobante",
      "Serie",
      "Proveedor",
      "Fecha",
      "Total",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.tipo_comprobante,
        item.serie,
        item.proveedor,
        item.fecha_compra,
        "S/. " + item.total,
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
