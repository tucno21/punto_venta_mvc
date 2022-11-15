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
const urlUnidades = document
  .querySelector("#urlUnidades")
  .getAttribute("data-url");
const urlCategorias = document
  .querySelector("#urlCategorias")
  .getAttribute("data-url");
const urlAfectation = document
  .querySelector("#urlAfectation")
  .getAttribute("data-url");
const urlGeneral = document
  .querySelector("#urlGeneral")
  .getAttribute("data-url");
//modal y botones
const listaTabla = document.querySelector("#simpleDatatable");
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnCrear = document.querySelector("#btnCrear");
const btnFormulario = document.querySelector("#btnFormulario");
//inputs
const listId = document.querySelector("#listId");
const inputCodigo = document.querySelector("#inputCodigo");
const inputDescripcion = document.querySelector("#inputDescripcion");
const inputStock = document.querySelector("#inputStock");
const inputStockMin = document.querySelector("#inputStockMin");
const inputPC = document.querySelector("#inputPC");
const inputPV = document.querySelector("#inputPV");
const inputUnidad = document.querySelector("#inputUnidad");
const inputCategoria = document.querySelector("#inputCategoria");
const inputTipAfec = document.querySelector("#inputTipAfec");
const inputImagen = document.querySelector("#inputImagen");

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
    menuUnidades();
    menuCategorias();
    menuTipAfectacion();
    verImagen();
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
        <a href="${urlEdit}?id=${element.id}" class="btn btn-outline-warning btn-sm rounded-pill btnEditar" title="Editar">
            <i class="bi bi-pencil"></i>
        </a>
        <a href="${urlDestroy}?id=${element.id}" class="btn btn-outline-danger btn-sm rounded-pill btnEliminar" title="Eliminar">
            <i class="bi bi-trash3"></i>
        </a>
        `;
    element["imagen"] =
      element.imagen == null
        ? `<img src="${urlGeneral}/assets/img/producto.png" class="img-thumbnail" width="40px">`
        : `<img src="${urlGeneral}/assets/img/${element.imagen}" class="img-thumbnail" width="40px">`;
    element["stock"] =
      element.stock < element.stock_minimo
        ? `<span class="badge bg-danger rounded-pill p-1">${element.stock}</span>`
        : `<span class="badge bg-success rounded-pill p-1">${element.stock}</span>`;
  });

  let newData = {
    headings: [
      "#",
      "Codigo",
      "Detalle",
      "Imagen",
      "P. Compra",
      "P. Venta",
      "Stock",
      "Categoria",
      "Estado",
      "Acciones",
    ],
    data: data.map((item) => {
      return [
        item.orden.toString(),
        item.codigo,
        item.detalle,
        item.imagen,
        item.precio_compra,
        item.precio_venta,
        item.stock.toString(),
        item.categoria.toString(),
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
      inputCodigo,
      inputDescripcion,
      inputStock,
      inputStockMin,
      inputPC,
      inputPV,
      inputUnidad,
      inputCategoria,
      inputTipAfec,
      inputImagen,
    ]);
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
  data.append("codigo", inputCodigo.value);
  data.append("detalle", inputDescripcion.value);
  data.append("stock", inputStock.value);
  data.append("stock_minimo", inputStockMin.value);
  data.append("precio_compra", inputPC.value);
  data.append("precio_venta", inputPV.value);
  data.append("unidad_id", inputUnidad.value);
  data.append("categoria_id", inputCategoria.value);
  data.append("tipo_afectacion_id", inputTipAfec.value);
  data.append("imagen", inputImagen.files[0]);

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
      inputCodigo,
      inputDescripcion,
      inputStock,
      inputStockMin,
      inputPC,
      inputPV,
      inputUnidad,
      inputCategoria,
      inputTipAfec,
      inputImagen,
    ]);
    modalInputs.hide();
    toastPersonalizado("success", "Producto Registrado");
  } else {
    mensajeErrorInput(inputCodigo, dRes.data.codigo);
    mensajeErrorInput(inputDescripcion, dRes.data.detalle);
    mensajeErrorInput(inputStock, dRes.data.stock);
    mensajeErrorInput(inputStockMin, dRes.data.stock_minimo);
    mensajeErrorInput(inputPC, dRes.data.precio_compra);
    mensajeErrorInput(inputPV, dRes.data.precio_venta);
    mensajeErrorInput(inputUnidad, dRes.data.unidad_id);
    mensajeErrorInput(inputCategoria, dRes.data.categoria_id);
    mensajeErrorInput(inputTipAfec, dRes.data.tipo_afectacion_id);
    // mensajeErrorInput(inputImagen, dRes.data.imagen);
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
    inputCodigo,
    inputDescripcion,
    inputStock,
    inputStockMin,
    inputPC,
    inputPV,
    inputUnidad,
    inputCategoria,
    inputTipAfec,
    inputImagen,
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
  inputCodigo.value = data.data.codigo;
  inputDescripcion.value = data.data.detalle;
  inputStock.value = data.data.stock;
  inputStockMin.value = data.data.stock_minimo;
  inputPC.value = data.data.precio_compra;
  inputPV.value = data.data.precio_venta;
  inputUnidad.value = data.data.unidad_id;
  inputCategoria.value = data.data.categoria_id;
  inputTipAfec.value = data.data.tipo_afectacion_id;
  // inputImagen.value = data.data.imagen;
  if (data.data.imagen != null) {
    const imagen = document.querySelector(".previsualizar");
    imagen.src = `${urlGeneral}/assets/img/${data.data.imagen}`;
  } else {
    const imagen = document.querySelector(".previsualizar");
    imagen.src = `${urlGeneral}/assets/img/producto.png`;
  }

  modalInputs.show();
}

//enviar formulario para editar
async function enviarEditarFormulario() {
  //crear data para enviar
  const data = new FormData();
  data.append("id", listId.value);
  data.append("codigo", inputCodigo.value);
  data.append("detalle", inputDescripcion.value);
  data.append("stock", inputStock.value);
  data.append("stock_minimo", inputStockMin.value);
  data.append("precio_compra", inputPC.value);
  data.append("precio_venta", inputPV.value);
  data.append("unidad_id", inputUnidad.value);
  data.append("categoria_id", inputCategoria.value);
  data.append("tipo_afectacion_id", inputTipAfec.value);
  data.append("imagen", inputImagen.files[0]);

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
      inputCodigo,
      inputDescripcion,
      inputStock,
      inputStockMin,
      inputPC,
      inputPV,
      inputUnidad,
      inputCategoria,
      inputTipAfec,
      inputImagen,
    ]);
    modalInputs.hide();
    toastPersonalizado("success", "Producto editado");
  } else {
    mensajeErrorInput(inputCodigo, dRes.data.codigo);
    mensajeErrorInput(inputDescripcion, dRes.data.detalle);
    mensajeErrorInput(inputStock, dRes.data.stock);
    mensajeErrorInput(inputStockMin, dRes.data.stock_minimo);
    mensajeErrorInput(inputPC, dRes.data.precio_compra);
    mensajeErrorInput(inputPV, dRes.data.precio_venta);
    mensajeErrorInput(inputUnidad, dRes.data.unidad_id);
    mensajeErrorInput(inputCategoria, dRes.data.categoria_id);
    mensajeErrorInput(inputTipAfec, dRes.data.tipo_afectacion_id);
    // mensajeErrorInput(inputImagen, dRes.data.imagen);
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
      toastPersonalizado("success", "Producto eliminado");
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

//menu de unidades
async function menuUnidades() {
  const response = await fetch(urlUnidades);
  const data = await response.json();

  let html = `<option value="">Seleccione...</option>`;
  data.forEach((item) => {
    html += `<option value="${item.id}">${item.descripcion}</option>`;
  });
  inputUnidad.innerHTML = html;
}

//menu de categorias
async function menuCategorias() {
  const response = await fetch(urlCategorias);
  const data = await response.json();

  let html = `<option value="">Seleccione...</option>`;
  data.forEach((item) => {
    html += `<option value="${item.id}">${item.nombre}</option>`;
  });
  inputCategoria.innerHTML = html;
}

//menu de tipos de afectacion
async function menuTipAfectacion() {
  const response = await fetch(urlAfectation);
  const data = await response.json();
  // console.log(data);

  let html = `<option value="">Seleccione...</option>`;
  data.forEach((item) => {
    html += `<option value="${item.id}">${item.descripcion}</option>`;
  });
  inputTipAfec.innerHTML = html;
}

//ver imagen
function verImagen() {
  imagen(".inputFoto", ".previsualizar");
}
