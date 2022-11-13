//url para crud
const urlPermisos = document
  .querySelector("#urlPermisos")
  .getAttribute("data-url");
const urlEditarPermiso = document
  .querySelector("#urlEditarPermiso")
  .getAttribute("data-url");
const urlEliminarPermiso = document
  .querySelector("#urlEliminarPermiso")
  .getAttribute("data-url");
const urlCrearPermiso = document
  .querySelector("#urlCrearPermiso")
  .getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrearPermiso = document.querySelector("#btnCrearPermiso");
const btnEnviarPermiso = document.querySelector("#botonPermiso");
//inputs
const inputName = document.querySelector("#per_name");
const inputDescription = document.querySelector("#description");
const listId = document.querySelector("#listId");

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
    getListaPermiso();
    botonCrearPermiso();
    botonEnviarPermiso();
    botonesDataTable();
  });
}

//Traer los datos de la tabla
async function getListaPermiso() {
  const url = urlPermisos;
  const response = await fetch(url);
  const data = await response.json();
  let i = 1;
  data.forEach((element) => {
    element.orden = i;
    i++;
    element[
      "actions"
    ] = `<a href="${urlEditarPermiso}?id=${element.id}" class="btn btn-outline-warning btn-sm btnEditar"><i class="bi bi-pencil"></i></a>
            <a href="${urlEliminarPermiso}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar"><i class="bi bi-trash3"></i></a>`;
  });

  let newData = {
    headings: ["ID", "Nombre", "Descripción", "Acciones"],
    data: data.map((item) => {
      // console.log(item.id.toString());
      return [
        item.orden.toString(),
        item.per_name,
        item.description,
        item.actions,
      ];
    }),
  };

  // Insert the data
  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
  // botonesDataTable();
}

// boton para crear un nuevo permiso
function botonCrearPermiso() {
  btnCrearPermiso.addEventListener("click", () => {
    limpiarErrrorInput([inputName, inputDescription]);
    //agregar clase btnCrear botonPermiso
    btnEnviarPermiso.classList.add("btnCrear");
    //eliminar clase btnCrear botonPermiso
    btnEnviarPermiso.classList.remove("btnEditar");
    //cambiar texto de boton
    btnEnviarPermiso.textContent = "Crear Permiso";
    modalInputs.show();
  });
}

//boton para enviar el formulario
function botonEnviarPermiso() {
  btnEnviarPermiso.addEventListener("click", (e) => {
    if (e.target.classList.contains("btnCrear")) {
      crearPermisoFormulario();
    } else if (e.target.classList.contains("btnEditar")) {
      editarPermisoFormulario();
    }
  });
}

//enviar formulario para crear permiso
async function crearPermisoFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("per_name", inputName.value);
  data.append("description", inputDescription.value);
  //enviar data
  const response = await fetch(urlCrearPermiso, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    getListaPermiso();
    //limpiar inputs
    limpiarErrrorInput([inputName, inputDescription]);
    modalInputs.hide();
    toastPersonalizado("success", "Permiso creado");
  } else {
    mensajeErrorInput(inputName, dRes.data.per_name);
    mensajeErrorInput(inputDescription, dRes.data.description);
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

      botonEditarPermiso(url);
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

      botonEliminarPermiso(url);
    }
  });
}

//boton de editar permiso
async function botonEditarPermiso(url) {
  limpiarErrrorInput([inputName, inputDescription]);
  //agregar clase btnEditar botonPermiso
  btnEnviarPermiso.classList.add("btnEditar");
  //eliminar clase btnEditar botonPermiso
  btnEnviarPermiso.classList.remove("btnCrear");
  //cambiar texto de boton
  btnEnviarPermiso.textContent = "Editar Permiso";
  //traer data del permiso
  const response = await fetch(url);
  const data = await response.json();
  //llenar inputs
  listId.value = data.data.id;
  inputName.value = data.data.per_name;
  inputDescription.value = data.data.description;

  modalInputs.show();
}

//enviar formulario para editar permiso
async function editarPermisoFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("per_name", inputName.value);
  data.append("description", inputDescription.value);
  //enviar data
  const response = await fetch(urlEditarPermiso, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    getListaPermiso();
    //limpiar inputs
    limpiarErrrorInput([inputName, inputDescription]);
    modalInputs.hide();
    toastPersonalizado("success", "Permiso editado");
  } else {
    mensajeErrorInput(inputName, dRes.data.per_name);
    mensajeErrorInput(inputDescription, dRes.data.description);
  }
}

//boton de eliminar permiso
async function botonEliminarPermiso(url) {
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
      getListaPermiso();
      toastPersonalizado("success", "Permiso eliminado");
    }
  }
}
