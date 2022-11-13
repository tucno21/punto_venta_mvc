//url - enlaces
const urlDataTable = document
  .querySelector("#urlDataTable")
  .getAttribute("data-url");
const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");
const urlEdit = document.querySelector("#urlEdit").getAttribute("data-url");
const urlPermissions = document
  .querySelector("#urlPermissions")
  .getAttribute("data-url");
const urlDestroy = document
  .querySelector("#urlDestroy")
  .getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const inputName = document.querySelector("#rol_name");
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
    generarDataTable();
    botonCrear();
    botonFormulario();
    botonesDataTable();
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
    element["actions"] = `
        <a href="${urlEdit}?id=${element.id}" class="btn btn-outline-warning btn-sm btnEditar">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm btnEliminar">
            <i class="bi bi-trash3"></i>
        </a>
        `;
    element["permiso"] = `
        <a href="${urlPermissions}?id=${element.id}" class="btn btn-outline-primary btn-sm btnPermiso">
            <i class="bi bi-key"></i>
        </a>`;
  });

  let newData = {
    headings: ["#", "Rol", "Permisos", "Acciones"],
    data: data.map((item) => {
      return [item.orden.toString(), item.rol_name, item.permiso, item.actions];
    }),
  };

  // Insert the data
  dataTable.destroy();
  dataTable.init();
  dataTable.insert(newData);
}

// boton para crear
function botonCrear() {
  btnCrear.addEventListener("click", () => {
    limpiarErrrorInput([inputName]);
    //agregar clase btnCrear botonPermiso
    btnFormulario.classList.add("btnCrear");
    //eliminar clase btnCrear botonPermiso
    btnFormulario.classList.remove("btnEditar");
    //cambiar texto de boton
    btnFormulario.textContent = "Crear Rol";
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
  data.append("rol_name", inputName.value);
  //enviar data
  const response = await fetch(urlCreate, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([inputName]);
    modalInputs.hide();
    toastPersonalizado("success", "Rol creado");
  } else {
    mensajeErrorInput(inputName, dRes.data.rol_name);
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

    //en boton permiso
    if (
      e.target.classList.contains("btnPermiso") ||
      e.target.parentElement.classList.contains("btnPermiso")
    ) {
      //traer link del boton
      const url =
        e.target.parentElement.getAttribute("href") ||
        e.target.getAttribute("href");

      botonPermiso(url);
    }
  });
}

//boton de editar
async function botonEditar(url) {
  limpiarErrrorInput([inputName]);
  //agregar clase btnEditar botonPermiso
  btnFormulario.classList.add("btnEditar");
  //eliminar clase btnEditar botonPermiso
  btnFormulario.classList.remove("btnCrear");
  //cambiar texto de boton
  btnFormulario.textContent = "Editar Rol";
  //   traer data del permiso
  const response = await fetch(url);
  const data = await response.json();
  //llenar inputs
  listId.value = data.data.id;
  inputName.value = data.data.rol_name;

  modalInputs.show();
}

//enviar formulario para editar
async function enviarEditarFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("rol_name", inputName.value);
  //enviar data
  const response = await fetch(urlEdit, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([inputName]);
    modalInputs.hide();
    toastPersonalizado("success", "Rol editado");
  } else {
    mensajeErrorInput(inputName, dRes.data.rol_name);
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
      toastPersonalizado("success", "Permiso eliminado");
    }
  }
}

//boton permisos
async function botonPermiso(url) {
  //abrir url
  window.location.href = url;
}
