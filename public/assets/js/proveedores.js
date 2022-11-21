//url - enlaces
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");
const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");
const urlEdit = document.querySelector("#urlEdit").getAttribute("data-url");
const urlStatus = document.querySelector("#urlStatus").getAttribute("data-url");
const urlDestroy = document
  .querySelector("#urlDestroy")
  .getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const listId = document.querySelector("#listId");
const inputDocumento = document.querySelector("#inputDocumento");
const inputNombre = document.querySelector("#inputNombre");
const inputDireccion = document.querySelector("#inputDireccion");
const inputTelefono = document.querySelector("#inputTelefono");

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
    botonCrear();
    botonFormulario();
    botonesDataTable();
    buscarCliente();
  });
}

//Traer los datos de la tabla
async function generarDataTable() {
  const response = await fetch(urlDataTable);
  const data = await response.json();
  let i = 1;
  data.forEach((element) => {
    element.orden = i;
    i++;
    element["estado"] =
      element.estado == 1
        ? `<button data-url="${urlStatus}?id=${element.id}" class="btnEstado btn btn-outline-success rounded-pill btn-sm p-0 px-1">Habilitado</button>`
        : `<p data-url="${urlStatus}?id=${element.id}" class="btnEstado btn btn-outline-danger rounded-pill btn-sm p-0 px-1">Inhabilitado</p>`;
    element["actions"] = `
        <a href="${urlEdit}?id=${element.id}" class="btn btn-outline-warning btn-sm btnEditar">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar">
            <i class="bi bi-trash3"></i>
        </a>
        `;
  });

  let newData = {
    headings: ["#", "Nombres", "Telefono", "Estado", "Acciones"],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.nombre,
        item.telefono,
        item.estado,
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
    limpiarErrrorInput([
      inputDocumento,
      inputNombre,
      inputDireccion,
      inputTelefono,
    ]);
    inputDocumento.placeholder = "";
    //agregar clase btnCrear
    btnFormulario.classList.add("btnCrear");
    //eliminar clase btnCrear
    btnFormulario.classList.remove("btnEditar");
    //cambiar texto de boton
    btnFormulario.textContent = "Crear";
    modalInputs.show();
  });
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
  data.append("documento", inputDocumento.value);
  data.append("nombre", inputNombre.value);
  data.append("direccion", inputDireccion.value);
  data.append("telefono", inputTelefono.value);

  //enviar data
  const response = await fetch(urlCreate, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([
      inputDocumento,
      inputNombre,
      inputDireccion,
      inputTelefono,
    ]);
    modalInputs.hide();
    toastPersonalizado("success", "Cliente Registrado");
  } else {
    mensajeErrorInput(inputDocumento, dRes.data.documento);
    mensajeErrorInput(inputNombre, dRes.data.nombre);
    mensajeErrorInput(inputDireccion, dRes.data.direccion);
    mensajeErrorInput(inputTelefono, dRes.data.telefono);
  }
}

//botones de la tabla
function botonesDataTable() {
  listaTabla.addEventListener("click", (e) => {
    e.preventDefault();
    //en boton editar
    if (
      e.target.classList.contains("btnEditar") ||
      e.target.parentElement.classList.contains("btnEditar")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");
      botonEditar(url);
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

    //en boton estado
    if (e.target.classList.contains("btnEstado")) {
      //traer link del boton
      const url = e.target.getAttribute("data-url");

      botonEstado(url);
    }
  });
}

//boton de editar
async function botonEditar(url) {
  limpiarErrrorInput([
    inputDocumento,
    inputNombre,
    inputDireccion,
    inputTelefono,
  ]);
  //agregar clase btnEditar
  btnFormulario.classList.add("btnEditar");
  //eliminar clase btnEditar
  btnFormulario.classList.remove("btnCrear");
  //cambiar texto de boton
  btnFormulario.textContent = "Actualizar";
  //   traer data del permiso
  const response = await fetch(url);
  const data = await response.json();
  //llenar inputs
  listId.value = data.data.id;
  inputDocumento.value = data.data.documento;
  inputNombre.value = data.data.nombre;
  inputDireccion.value = data.data.direccion;
  inputTelefono.value = data.data.telefono;

  modalInputs.show();
}

//enviar formulario para editar
async function enviarEditarFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("documento", inputDocumento.value);
  data.append("nombre", inputNombre.value);
  data.append("direccion", inputDireccion.value);
  data.append("telefono", inputTelefono.value);

  //enviar data
  const response = await fetch(urlEdit, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([
      inputDocumento,
      inputNombre,
      inputDireccion,
      inputTelefono,
    ]);
    modalInputs.hide();
    toastPersonalizado("success", "Cliente editado");
  } else {
    mensajeErrorInput(inputDocumento, dRes.data.documento);
    mensajeErrorInput(inputNombre, dRes.data.nombre);
    mensajeErrorInput(inputDireccion, dRes.data.direccion);
    mensajeErrorInput(inputTelefono, dRes.data.telefono);
  }
}

//boton de eliminar permiso
async function botonEliminar(url) {
  //preguntar si desea eliminar
  const { value: accept } = await Swal.fire({
    title: "¿esta seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    // showDenyButton: true,
    // confirmButtonText: `SI, eliminar`,
    // denyButtonText: `No`,
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Sí, bórralo!",
  });

  if (accept) {
    //enviar data
    const response = await fetch(url);
    const data = await response.json();
    if (data.status) {
      generarDataTable();
      toastPersonalizado("success", "Cliente eliminado");
    }
  }
}

//boton de estado
async function botonEstado(url) {
  // console.log(url);
  //preguntar si desea cambiar estado
  const { value: accept } = await Swal.fire({
    title: "¿desea cambiar Estado?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Sí!",
    cancelButtonText: `No`,
  });

  if (accept) {
    //enviar data
    const response = await fetch(url);
    const data = await response.json();
    if (data.status) {
      generarDataTable();
      toastPersonalizado("success", "Estado cambiado");
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

//buscar cliente
function buscarCliente() {
  const btnBuscarCliente = document.querySelector("#btnBuscarCliente");
  btnBuscarCliente.addEventListener("click", () => {
    if (
      inputDocumento.value.length == 8 &&
      Number.isInteger(Number(inputDocumento.value))
    ) {
      buscarDNI(inputDocumento.value);
    } else if (
      inputDocumento.value.length == 11 &&
      Number.isInteger(Number(inputDocumento.value))
    ) {
      buscarRUC(inputDocumento.value);
    } else {
      toastPersonalizado("error", "DNI 8 digitos o RUC 11 digitos");
    }
  });
}

//buscar dni
async function buscarDNI(number) {
  let data = await buscarDNIRUC(number, "dni");
  if (data.success) {
    inputNombre.value = data.data.nombre;
    inputDireccion.value = data.data.direccion;
  } else {
    toastPersonalizado("error", data.message);
  }
}

//buscar ruc
async function buscarRUC(number) {
  let data = await buscarDNIRUC(number, "ruc");
  if (data.success) {
    inputNombre.value = data.data.razonSocial;
    inputDireccion.value = data.data.direccion;
  } else {
    toastPersonalizado("error", data.message);
  }
}
