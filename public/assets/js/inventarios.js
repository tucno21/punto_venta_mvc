//url - enlaces
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");
const urlBusquedaMes = document
  .querySelector("#urlBusquedaMes")
  .getAttribute("data-url");
const urlInventarioMesPdf = document
  .querySelector("#urlInventarioMesPdf")
  .getAttribute("data-url");
const urlInventarioMesExcel = document
  .querySelector("#urlInventarioMesExcel")
  .getAttribute("data-url");

//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");

//botones y entradas
const inputMes = document.querySelector("#inputMes");
const btnBusquedaMes = document.querySelector("#btnBusquedaMes");
const inventarioMesPDF = document.querySelector("#inventarioMesPDF");
const inventarioMesExcel = document.querySelector("#inventarioMesExcel");

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

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    // generarDataTable();
    listaDeInventarios();
    // botonCrear();
    // botonFormulario();
    // botonesDataTable();
    busquedaInventarioMes();
    botonInventarioMesPDF();
    botonInventarioMesExcel();
  });
}

//lista de inventarios
async function listaDeInventarios() {
  const response = await fetch(urlDataTable);
  const data = await response.json();
  //pasar los datos a la tabla
  generarDataTable(data);
}

//Traer los datos de la tabla
async function generarDataTable(data) {
  let i = 1;
  data.forEach((element) => {
    element.orden = i;
    i++;
  });

  let newData = {
    headings: ["#", "Producto", "Comprobante", "Fecha", "Cantidad", "Tipo"],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.codigo + "-" + item.producto,
        item.comprobante,
        item.fecha,
        item.cantidad.toString(),
        item.accion,
      ];
    }),
  };

  // Insert the data
  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
  colorThead();
}

// //botones de la tabla
// function botonesDataTable() {
//   listaTabla.addEventListener("click", (e) => {
//     e.preventDefault();
//     //en boton editar
//     if (
//       e.target.classList.contains("btnEditar") ||
//       e.target.parentElement.classList.contains("btnEditar")
//     ) {
//       //traer link del boton
//       const url =
//         e.target.parentElement.getAttribute("href") ||
//         e.target.getAttribute("href");
//       botonEditar(url);
//     }
//     //en boton eliminar
//     if (
//       e.target.classList.contains("btnEliminar") ||
//       e.target.parentElement.classList.contains("btnEliminar")
//     ) {
//       //traer link del boton
//       const url =
//         e.target.parentElement.getAttribute("href") ||
//         e.target.getAttribute("href");

//       botonEliminar(url);
//     }

//     //en boton estado
//     if (e.target.classList.contains("btnEstado")) {
//       //traer link del boton
//       const url = e.target.getAttribute("data-url");

//       botonEstado(url);
//     }
//   });
// }

//color thead
function colorThead() {
  //primer hijo listaTabla
  const thead = listaTabla.firstElementChild;
  //agregar clase
  thead.classList.add("thead-dark");
}

//busquedaInventarioMes
function busquedaInventarioMes() {
  btnBusquedaMes.addEventListener("click", async (e) => {
    e.preventDefault();
    const mes = inputMes.value;
    if (mes === "") {
      toastPersonalizado("error", "Seleccione un mes");
      return;
    }
    //no seleccionar un mes superior al actual
    const fechaActual = new Date();
    //fechaActual en formato yyyy-mm
    // const mesActual = fechaActual.getMonth() + 1;
    const anoMesActual = fechaActual.toISOString().slice(0, 7);

    if (mes > anoMesActual) {
      toastPersonalizado("error", "Seleccione el mes actual o inferior");
      return;
    }

    //
    const link = urlBusquedaMes + "?mes=" + mes;
    const response = await fetch(link);
    const data = await response.json();

    generarDataTable(data);
  });
}

//botonInventarioMesPDF
function botonInventarioMesPDF() {
  inventarioMesPDF.addEventListener("click", (e) => {
    e.preventDefault();
    const mes = inputMes.value;
    if (mes === "") {
      toastPersonalizado("error", "Seleccione un mes");
      return;
    }
    //no seleccionar un mes superior al actual
    const fechaActual = new Date();
    //fechaActual en formato yyyy-mm
    // const mesActual = fechaActual.getMonth() + 1;
    const anoMesActual = fechaActual.toISOString().slice(0, 7);

    if (mes > anoMesActual) {
      toastPersonalizado("error", "Seleccione el mes actual o inferior");
      return;
    }

    const link = urlInventarioMesPdf + "?mes=" + mes;
    window.open(link, "_blank");
  });
}

//botonInventarioMesExcel
function botonInventarioMesExcel() {
  inventarioMesExcel.addEventListener("click", (e) => {
    e.preventDefault();
    const mes = inputMes.value;
    if (mes === "") {
      toastPersonalizado("error", "Seleccione un mes");
      return;
    }
    //no seleccionar un mes superior al actual
    const fechaActual = new Date();
    //fechaActual en formato yyyy-mm
    // const mesActual = fechaActual.getMonth() + 1;
    const anoMesActual = fechaActual.toISOString().slice(0, 7);

    if (mes > anoMesActual) {
      toastPersonalizado("error", "Seleccione el mes actual o inferior");
      return;
    }

    const link = urlInventarioMesExcel + "?mes=" + mes;
    window.open(link);
  });
}
