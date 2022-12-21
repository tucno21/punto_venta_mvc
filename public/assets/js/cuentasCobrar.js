const urlDataTable = document
  .getElementById("urlDataTable")
  .getAttribute("data-url");
const urlAddAbono = document
  .getElementById("urlAddAbono")
  .getAttribute("data-url");
const urlReporte = document
  .getElementById("urlReporte")
  .getAttribute("data-url");
const listaTabla = document.querySelector("#simpleDatatable");

const modalInputs = new bootstrap.Modal("#modalInputs");
const btnFormulario = document.querySelector("#btnFormulario");

//input para el formulario
const inputComprobante = document.querySelector("#inputComprobante");
const inputFecha = document.querySelector("#inputFecha");
const inputTotal = document.querySelector("#inputTotal");
const inputMontoAbonado = document.querySelector("#inputMontoAbonado");
const inputAbonar = document.querySelector("#inputAbonar");
const inputResta = document.querySelector("#inputResta");
const modalLabel = document.querySelector("#modalLabel");
const inputVentaId = document.querySelector("#inputVentaId");

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
    VerEstadoCaja();
    generarDataTable();
    botonFormulario();
    cambioInputAbonar();
    botonesDataTable();
  });
}

//estado de caja
async function VerEstadoCaja() {
  const urlEstadoCajaUsuario = document
    .querySelector("#urlEstadoCajaUsuario")
    .getAttribute("data-url");

  const panelVentas = document.querySelector("#panelVentas");
  const panelCaja = document.querySelector("#panelCaja");

  const response = await fetch(urlEstadoCajaUsuario);
  const data = await response.json();

  if (data.estado == 1) {
    panelVentas.classList.remove("d-none");
    panelCaja.classList.add("d-none");
  } else {
    panelVentas.classList.add("d-none");
    panelCaja.classList.remove("d-none");
  }
}

//Traer los datos de la tabla
async function generarDataTable() {
  const response = await fetch(urlDataTable);
  const data = await response.json();

  let i = 1;
  data.forEach((element) => {
    element.orden = i;
    i++;

    const listaEstado = {
      pagado: `<span class="badge bg-success">Pagado</span>`,
      pendiente: `<span class="badge bg-danger">Pendiente : ${
        element.total - element.abono
      }</span>`,
    };

    element["estadoCredito"] =
      element.estado_credito == 1 ? listaEstado.pendiente : listaEstado.pagado;

    const lista = {
      pdf: `<a href="${urlReporte}?id=${element.id}" class="btn btn-outline-danger btn-sm btnPdf" title="ver Pagos">
                <i class="bi bi-file-earmark-pdf"></i>
            </a>`,
      add: `<a href="${urlAddAbono}?id=${element.id}" class="btn btn-outline-success btn-sm mx-1 btnAdd" title="agregar abono">
                <i class="bi bi-database-add"></i>
                </a>`,
    };

    element["actions"] =
      element.estado_credito == 1 ? lista.pdf + lista.add : lista.pdf;
  });

  let newData = {
    headings: [
      "#",
      "Comprobante",
      "Total Cred.",
      "Cliente",
      "Abono",
      "Estado Credito",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.serie + "-" + item.correlativo,
        item.total,
        item.cliente,
        item.abono,
        item.estadoCredito,
        item.actions,
      ];
    }),
  };

  // Insert the data
  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
  colorThead();
}
//botones de la tabla
function botonesDataTable() {
  listaTabla.addEventListener("click", (e) => {
    e.preventDefault();
    //boton agregar abono
    if (
      e.target.classList.contains("btnAdd") ||
      e.target.parentElement.classList.contains("btnAdd")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      botonAgregarAbono(url);
    }

    //btnPdf
    if (
      e.target.classList.contains("btnPdf") ||
      e.target.parentElement.classList.contains("btnPdf")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      //funcion
      verReportePdf(url);
    }
  });
}
//botonAgregarAbono
async function botonAgregarAbono(url) {
  const response = await fetch(url);
  const data = await response.json();
  console.log(data);

  inputComprobante.value = data.serie + "-" + data.correlativo;
  inputTotal.value = data.total;
  inputMontoAbonado.value = data.abono;
  inputResta.value = (data.total - data.abono).toFixed(2);
  inputVentaId.value = data.id;
  modalLabel.innerHTML = data.cliente;

  //mostrar modal
  modalInputs.show();
}

//cambioInputAbonar
function cambioInputAbonar() {
  inputAbonar.addEventListener("keyup", (e) => {
    if (e.target.value == "") {
      inputResta.value = (inputTotal.value - inputMontoAbonado.value).toFixed(
        2
      );
      return;
    }
    inputResta.value = (
      inputTotal.value -
      inputMontoAbonado.value -
      e.target.value
    ).toFixed(2);

    //error si inputResta es menor a 0
    if (inputResta.value < 0) {
      toastPersonalizado("error", "El monto a abonar es mayor al total");
      inputResta.value = (inputTotal.value - inputMontoAbonado.value).toFixed(
        2
      );
      inputAbonar.value = "";
      return;
    }
  });
}

//botonFormulario
function botonFormulario() {
  btnFormulario.addEventListener("click", (e) => {
    e.preventDefault();
    enviarCrearFormulario();
  });
}

//enviarCrearFormulario()
async function enviarCrearFormulario() {
  if (inputAbonar.value == "" || inputAbonar.value == 0) {
    toastPersonalizado("error", "El campo abonar no puede estar vacio o 0");
    return;
  }
  const total = parseFloat(inputTotal.value);
  const totalsuma =
    parseFloat(inputMontoAbonado.value) +
    parseFloat(inputAbonar.value) +
    parseFloat(inputResta.value);
  if (totalsuma != total) {
    toastPersonalizado("error", "La suma de los abonos es mayor al total");
    return;
  }
  const data = new FormData();
  data.append("venta_id", inputVentaId.value);
  data.append("monto", inputAbonar.value);
  data.append("fecha", inputFechaEnviar.value);

  const response = await fetch(urlAddAbono, {
    method: "POST",
    body: data,
  });
  const dataResponse = await response.json();

  if (dataResponse.status) {
    toastPersonalizado("success", dataResponse.message);
    inputAbonar.value = "";
    modalInputs.hide();
    generarDataTable();
  } else {
    toastPersonalizado("error", "No se pudo agregar el abono");
  }
}

//btnPdf
function verReportePdf(url) {
  window.open(url, "_blank");
}

//color thead
function colorThead() {
  //primer hijo listaTabla
  const thead = listaTabla.firstElementChild;
  //agregar clase
  thead.classList.add("thead-dark");
}
