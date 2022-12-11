//url - enlaces
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");

//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");

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
    generarDataTable();
    // botonCrear();
    // botonFormulario();
    // botonesDataTable();
  });
}

//Traer los datos de la tabla
async function generarDataTable() {
  const response = await fetch(urlDataTable);
  const data = await response.json();
  console.log(data);
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
