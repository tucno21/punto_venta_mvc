const urlCantidades = document
  .getElementById("urlCantidades")
  .getAttribute("data-url");
const urlVentaCompra = document
  .getElementById("urlVentaCompra")
  .getAttribute("data-url");
const urlProductoStock = document
  .getElementById("urlProductoStock")
  .getAttribute("data-url");

//lugares de cambio
const totalUsuarios = document.querySelector(".totalUsuarios");
const totalClientes = document.querySelector(".totalClientes");
const totalProveedores = document.querySelector(".totalProveedores");
const totalProductos = document.querySelector(".totalProductos");
// console.log(totalUsuarios);
//cargar eventos
cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    cargarDatos();
    datosVentaCompra();
    datosProductoStock();
    // cargarGraficoBarra();
  });
}

//obtener datos de cantidades
async function cargarDatos() {
  const cant = await fetch(urlCantidades);
  const cantidades = await cant.json();

  totalUsuarios.innerHTML = cantidades.usuarios;
  totalClientes.innerHTML = cantidades.clientes;
  totalProveedores.innerHTML = cantidades.proveedores;
  totalProductos.innerHTML = cantidades.productos;
}

//obtener datos de ventas y compras
//enviar a la funcion cargarGraficoBarra
async function datosVentaCompra() {
  const ventaCompra = await fetch(urlVentaCompra);
  const datos = await ventaCompra.json();
  cargarGraficoBarra(datos.ventas, datos.compras);
}

//grafica de barras
async function cargarGraficoBarra(ventas, compras) {
  let options = {
    chart: {
      height: 350,
      type: "line",
      stacked: false,
    },
    stroke: {
      width: [0, 2, 5],
      curve: "smooth",
    },
    plotOptions: {
      bar: {
        columnWidth: "80%",
      },
    },
    colors: ["#2A4F72", "#FFA21D"],
    series: [
      {
        name: "Ventas",
        type: "column",
        data: Object.values(ventas), //datos de las ventas
      },
      {
        name: "Compras",
        type: "column",
        data: Object.values(compras), //datos de las compras
      },
    ],
    fill: {
      opacity: [0.85, 1],
    },
    labels: Object.keys(ventas), //los meses
    markers: {
      size: 0,
    },
    xaxis: {
      type: "",
    },
    yaxis: {
      min: 0,
    },
    tooltip: {
      shared: true,
      intersect: false,
      y: {
        formatter: function (y) {
          if (typeof y !== "undefined") {
            return "S/ " + y.toFixed(0);
          }
          return y;
        },
      },
    },
    legend: {
      labels: {
        useSeriesColors: true,
      },
      markers: {
        customHTML: [
          function () {
            return "";
          },
          function () {
            return "";
          },
        ],
      },
    },
  };
  let chart = new ApexCharts(
    document.querySelector("#graficoVentasCompras"),
    options
  );
  chart.render();
}

//obtener datos de productos stock
async function datosProductoStock() {
  const productoStock = await fetch(urlProductoStock);
  const datos = await productoStock.json();
  // console.log(datos);
  productosStockMinimo(datos.productosMin);
  productosStockCero(datos.productosCero);
}
//mostrar productos debajo del stock minimo
async function productosStockMinimo(productosMin) {
  const simpleDatatableStockMin = document.querySelector(
    "#simpleDatatableStockMin"
  );

  let dataTable = new simpleDatatables.DataTable(simpleDatatableStockMin, {
    searchable: false, //activar busqueda
    fixedHeight: true, //activar altura fija
    // paging: false, //activar paginacion
    perPageSelect: false, //activar seleccion de registros por pagina
    labels: {
      placeholder: "Buscar...",
      perPage: "{select} Registros por página",
      noRows: "No hay registros",
      info: "Mostrando {start} a {end} de {rows} registros",
    },
  });

  let i = 1;
  productosMin.forEach((element) => {
    element.orden = i;
    i++;
  });

  let newData = {
    headings: ["#", "codigo", "detalle", "Stock Min", "Stock"],
    data: productosMin.map((item) => {
      return [
        item.orden.toString(),
        item.codigo,
        item.detalle,
        item.stock_minimo.toString(),
        item.stock.toString(),
      ];
    }),
  };

  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
}

//mostrar productos con stock cero
async function productosStockCero(productosCero) {
  const simpleDatatableCeroStock = document.querySelector(
    "#simpleDatatableCeroStock"
  );

  let dataTable = new simpleDatatables.DataTable(simpleDatatableCeroStock, {
    searchable: false, //activar busqueda
    fixedHeight: true, //activar altura fija
    // paging: false, //activar paginacion
    perPageSelect: false, //activar seleccion de registros por pagina
    labels: {
      placeholder: "Buscar...",
      perPage: "{select} Registros por página",
      noRows: "No hay registros",
      info: "Mostrando {start} a {end} de {rows} registros",
    },
  });

  let i = 1;
  productosCero.forEach((element) => {
    element.orden = i;
    i++;
  });

  let newData = {
    headings: ["#", "codigo", "detalle", "stock Min", "Stock"],
    data: productosCero.map((item) => {
      return [
        item.orden.toString(),
        item.codigo,
        item.detalle,
        item.stock_minimo.toString(),
        item.stock.toString(),
      ];
    }),
  };

  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
}
