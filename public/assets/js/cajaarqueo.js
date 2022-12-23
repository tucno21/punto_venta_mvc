//url - enlaces
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");
const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");
const urlDestroy = document
  .querySelector("#urlDestroy")
  .getAttribute("data-url");
const urlCajas = document.querySelector("#urlCajas").getAttribute("data-url");
const urlEstadoCajaUsuario = document
  .querySelector("#urlEstadoCajaUsuario")
  .getAttribute("data-url");
const urlReporte = document
  .querySelector("#urlReporte")
  .getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const listId = document.querySelector("#listId");
const inputCajaId = document.querySelector("#inputCajaId");
const inputMonto = document.querySelector("#inputMonto");

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
    verEstadoCajaUsuario();
    generarDataTable();
    botonCrear();
    botonFormulario();
    botonesDataTable();
  });
}

//funcion verEstadoCajaUsuario
async function verEstadoCajaUsuario() {
  const response = await fetch(urlEstadoCajaUsuario);
  const data = await response.json();
  if (data.estado == 1) {
    //agregar disabled
    btnCrear.setAttribute("disabled", true);
    //eliminar clase btn-success
    btnCrear.classList.remove("btn-success");
    //agregar clase btn-danger
    btnCrear.classList.add("btn-danger");
    //agregar hijo
    btnCrear.innerHTML = `<i class="bi bi-unlock"></i>  Caja Abierta`;
  } else {
    //eliminar disabled
    btnCrear.removeAttribute("disabled");
    //eliminar clase btn-danger
    btnCrear.classList.remove("btn-danger");
    //agregar clase btn-success
    btnCrear.classList.add("btn-success");
    //agregar hijo
    btnCrear.innerHTML = `<i class="bi bi-lock"></i>  Aperturar Caja Nueva`;
  }
}

//Traer los datos de la tabla
async function generarDataTable() {
  const response = await fetch(urlDataTable);
  const data = await response.json();
  // console.log(data);
  let i = 1;
  data.forEach((element) => {
    element.orden = i;
    i++;

    let actions;
    if (element.estado == 1) {
      actions = `
        <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar" title="Cerrar Caja">
            <i class="bi bi-unlock"></i>
        </a>
        `;
    } else {
      actions = `
        <a href="${urlReporte}?id=${element.id}" class="btn btn-outline-success btn-sm btnReporte" title="reporte de ventas de caja">
            <i class="bi bi-file-pdf"></i>
        </a>
        `;
    }

    element["actions"] = actions;

    //formatear fecha <p>d/m/y</p>
    //formatear hora <p>h:m:s</p>

    element["fecha_apertura"] = `
      <p class="p-0 m-0 h6">${new Date(
        element.fecha_apertura
      ).toLocaleDateString()}</p>
      <p class="p-0 m-0 h6"><small>${new Date(
        element.fecha_apertura
      ).toLocaleTimeString()}</small></p>
      `;

    //si es null no se formatea
    element["fecha_cierre"] = element.fecha_cierre
      ? `
      <p class="p-0 m-0 h6">${new Date(
        element.fecha_cierre
      ).toLocaleDateString()}</p>
      <p class="p-0 m-0 h6"><small>${new Date(
        element.fecha_cierre
      ).toLocaleTimeString()}</small></p>
      `
      : null;
  });

  let newData = {
    headings: [
      "#",
      "M. Inicial",
      "F. Apertura",
      "M. Final",
      "F. Cierre",
      "Venta Total",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.monto_inicial,
        item.fecha_apertura,
        item.monto_final,
        item.fecha_cierre,
        item.total_venta,
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

// boton para crear
function botonCrear() {
  btnCrear.addEventListener("click", () => {
    limpiarErrrorInput([inputCajaId, inputMonto]);
    //agregar clase btnCrear
    btnFormulario.classList.add("btnCrear");
    //eliminar clase btnCrear
    btnFormulario.classList.remove("btnEditar");
    //cambiar texto de boton
    btnFormulario.textContent = "Aperturar Caja";
    //Lista de cajas
    listaCajas();
    //mostrar modal
    modalInputs.show();
  });
}

//lista de cajas
async function listaCajas() {
  const response = await fetch(urlCajas);
  const data = await response.json();
  let html = "";
  data.forEach((element) => {
    html += `<option value="${element.id}">${element.nombre}</option>`;
  });
  inputCajaId.innerHTML = html;
}

//boton de formulario
function botonFormulario() {
  btnFormulario.addEventListener("click", (e) => {
    if (e.target.classList.contains("btnCrear")) {
      enviarCrearFormulario();
    } else if (e.target.classList.contains("btnEditar")) {
      enviarEditarFormulario();
    }
  });
}

//enviar formulario para crear
async function enviarCrearFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("caja_id", inputCajaId.value);
  data.append("monto_inicial", inputMonto.value);

  //enviar data
  const response = await fetch(urlCreate, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    verEstadoCajaUsuario();
    generarDataTable();
    //limpiar inputs
    // limpiarErrrorInput([inputCajaId]);
    modalInputs.hide();
    toastPersonalizado("success", "Caja aperturada correctamente");
  } else {
    mensajeErrorInput(inputCajaId, dRes.data.caja_id);
    mensajeErrorInput(inputMonto, dRes.data.monto_inicial);
  }
}

//botones de la tabla
function botonesDataTable() {
  listaTabla.addEventListener("click", (e) => {
    e.preventDefault();
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

    //en boton btnReporte
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
  });
}

//boton de eliminar permiso
async function botonEliminar(url) {
  //preguntar si desea eliminar
  const { value: accept } = await Swal.fire({
    title: "¿Deseas cerrar la caja?",
    // text: "Deseas cerrar la caja",
    //interrogante
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Sí, cerrar!",
  });

  if (accept) {
    //enviar data
    const response = await fetch(url);
    const data = await response.json();
    if (data.status) {
      verEstadoCajaUsuario();
      generarDataTable();
      toastPersonalizado("success", "Caja cerrada correctamente");
    }
  }
}

//color thead
function colorThead() {
  //primer hijo listaTabla
  const thead = listaTabla.firstElementChild;
  //agregar clase
  thead.classList.add("thead-dark");
}

//botonReporte
function botonReporte(url) {
  window.open(url, "_blank");
}
