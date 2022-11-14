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
const urlRoles = document.querySelector("#urlRoles").getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const listId = document.querySelector("#listId");
const inputName = document.querySelector("#inputName");
const inputEmail = document.querySelector("#inputEmail");
const inputPassword = document.querySelector("#inputPassword");
const inputRol = document.querySelector("#inputRol");

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
    menuRoles();
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
      element.status == 1
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
    headings: ["#", "Nombre", "Correo", "Estado", "Rol", "Acciones"],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.name,
        item.email,
        item.estado,
        item.rol_name,
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
    limpiarErrrorInput([inputName, inputEmail, inputPassword, inputRol]);
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
  data.append("name", inputName.value);
  data.append("email", inputEmail.value);
  data.append("password", inputPassword.value);
  data.append("rol_id", inputRol.value);

  //enviar data
  const response = await fetch(urlCreate, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([inputName, inputEmail, inputPassword, inputRol]);
    modalInputs.hide();
    toastPersonalizado("success", "Usuario Registrado");
  } else {
    mensajeErrorInput(inputName, dRes.data.name);
    mensajeErrorInput(inputEmail, dRes.data.email);
    mensajeErrorInput(inputPassword, dRes.data.password);
    mensajeErrorInput(inputRol, dRes.data.rol_id);
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
  limpiarErrrorInput([inputName, inputEmail, inputPassword, inputRol]);
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
  inputName.value = data.data.name;
  inputEmail.value = data.data.email;
  inputRol.value = data.data.rol_id;

  modalInputs.show();
}

//enviar formulario para editar
async function enviarEditarFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("name", inputName.value);
  data.append("email", inputEmail.value);
  data.append("password", inputPassword.value);
  data.append("rol_id", inputRol.value);

  //enviar data
  const response = await fetch(urlEdit, {
    method: "POST",
    body: data,
  });
  const dRes = await response.json();
  if (dRes.status) {
    generarDataTable();
    //limpiar inputs
    limpiarErrrorInput([inputName, inputEmail, inputPassword, inputRol]);
    modalInputs.hide();
    toastPersonalizado("success", "Rol editado");
  } else {
    mensajeErrorInput(inputName, dRes.data.name);
    mensajeErrorInput(inputEmail, dRes.data.email);
    mensajeErrorInput(inputPassword, dRes.data.password);
    mensajeErrorInput(inputRol, dRes.data.rol_id);
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

//menu de roles
async function menuRoles() {
  const response = await fetch(urlRoles);
  const data = await response.json();

  let html = `<option value="">Seleccione...</option>`;
  data.forEach((rol) => {
    html += `<option value="${rol.id}">${rol.rol_name}</option>`;
  });
  inputRol.innerHTML = html;
}

//boton de estado
async function botonEstado(url) {
  console.log(url);
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
