const idVenta = document.querySelector("#idVenta").value;
const urlIdVenta = document
  .querySelector("#urlIdVenta")
  .getAttribute("data-url");

//mostrar datos del comprobante a hacer nota
const inputComprobante = document.querySelector("#inputComprobante");
const inputCliente = document.querySelector("#inputCliente");
const inputFechaVenta = document.querySelector("#inputFechaVenta");
//referencia
const inputTipoDocumentoReferencia = document.querySelector(
  "#inputTipoDocumentoReferencia"
);
const inputSerieReferencia = document.querySelector("#inputSerieReferencia");
const inputCorrelativoReferencia = document.querySelector(
  "#inputCorrelativoReferencia"
);

//input buscador de comprobantes
const inputBuscarComprobante = document.querySelector(
  "#inputBuscarComprobante"
);
const urlBuscarVenta = document
  .querySelector("#urlBuscarVenta")
  .getAttribute("data-url");

//busqueda de Producto scaner
const checkedBarcode = document.querySelector("#checkedBarcode");
const checkedNombre = document.querySelector("#checkedNombre");
const grupoBarcode = document.getElementById("grupoBarcode");
const grupoNombre = document.getElementById("grupoNombre");
const inputBuscarBarcode = document.querySelector("#inputBuscarBarcode");
const inputBuscarNombre = document.querySelector("#inputBuscarNombre");
//urls
const urlProductosId = document
  .querySelector("#urlProductosId")
  .getAttribute("data-url");

//imputs de notas
const inputTipoComprobante = document.querySelector("#inputTipoComprobante");
const inputSerieId = document.querySelector("#inputSerieId");
const inputTipoNota = document.querySelector("#inputTipoNota");
const inputDescripcion = document.querySelector("#inputDescripcion");
const inputFechaNota = document.querySelector("#inputFechaNota");
const inputUserId = document.querySelector("#inputUserId");
let moneda = "";
let clienteId = "";
let venta_id = "";

//inputs de codicion de pago
const condicion_Pago = document.querySelector("#condicion_Pago");
const cantidadCuotas = document.querySelector("#cantidadCuotas");
const cuotasContainer = document.querySelector("#cuotasContainer");

//url para notas
const urlTipoComprobante = document
  .querySelector("#urlTipoComprobante")
  .getAttribute("data-url");
const urlSerieCorrelativo = document
  .querySelector("#urlSerieCorrelativo")
  .getAttribute("data-url");
const urlTipoNota = document
  .querySelector("#urlTipoNota")
  .getAttribute("data-url");

//boton generar venta
const btnEnviarNotas = document.querySelector("#btnEnviarNotas");
const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");

//index notas
const urlIndexNotas = document
  .querySelector("#urlIndexNotas")
  .getAttribute("data-url");

//carrito de compras
const tablaCompras = document.querySelector("#tablaventas");
let productosCarrito = [];
let cuotasCreditos = [];

cargarEventListeners();
function cargarEventListeners() {
  if (idVenta !== "vacio") {
    datosIdVenta();
  }

  //cambio de checkbox
  checkedBarcode.addEventListener("click", cambioCheckedBarcode);
  checkedNombre.addEventListener("click", cambioCheckedNombre);
  //buscar producto
  inputBuscarBarcode.addEventListener("keyup", agregarProductoBarcode);
  inputBuscarNombre.addEventListener("keyup", agregarProductoNombre);

  tablaCompras.addEventListener("click", eliminarProducto);
  //cambiar precio de producto
  tablaCompras.addEventListener("change", cambioCantPrecioProducto);
  //ingreso de datos de cuota
  cuotasContainer.addEventListener("change", agregarCuotasArray);

  document.addEventListener("DOMContentLoaded", () => {
    productosCarrito =
      JSON.parse(localStorage.getItem("productocarritonotas")) || [];
    carritoHTML();

    menuTipoComprobante();
    cambioMenuTipoComprobante();
    cambioMenuSerie();
    buscarVenta();
    generarNota();
  });
}

//traer la venta segun si id
async function datosIdVenta() {
  //url
  const link = urlIdVenta + "?id=" + idVenta;
  const response = await fetch(link);
  const data = await response.json();

  inputComprobante.value = data.serie + "-" + data.correlativo;
  inputCliente.value = data.cliente;
  //data.fecha_emision  yyyy-mm-dd hh:mm:ss cambiar a yyyy-mm-dd
  inputFechaVenta.value = data.fecha_emision.substring(0, 10);

  //inputBuscarComprobante disabled
  inputBuscarComprobante.disabled = true;

  //inputs de referencia
  inputTipoDocumentoReferencia.value = data.tipodoc;
  inputSerieReferencia.value = data.serie;
  inputCorrelativoReferencia.value = data.correlativo;
  moneda = data.moneda;
  clienteId = data.cliente_id;
  venta_id = data.id;

  //data.productos localStorage
  // localStorage.setItem("productocarritonotas", data.productos);
  productosCarrito = JSON.parse(data.productos);

  //verificar si es factura
  if (data.tipodoc == "01") {
    if (data.forma_pago == "Contado") {
      condicionPago();
    }
    if (data.forma_pago == "Credito") {
      condicionesCredito();
      mostrarCuotas(data.cuotas);
    }
  }

  carritoHTML();
}

// cuando selecciona barcode se oculta nombre
function cambioCheckedBarcode(e) {
  //eliminar class d-none grupoBarcode
  grupoBarcode.classList.remove("d-none");
  //agregar class d-none grupoNombre
  grupoNombre.classList.add("d-none");
  inputBuscarBarcode.value = "";
  inputBuscarBarcode.focus();
}
//cuando selecciona nombre se oculta barcode
function cambioCheckedNombre(e) {
  //eliminar class d-none grupoNombre
  grupoNombre.classList.remove("d-none");
  //agregar class d-none grupoBarcode
  grupoBarcode.classList.add("d-none");
  inputBuscarNombre.value = "";
  inputBuscarNombre.focus();
}

//cuando ingresa datos en input barcode
function agregarProductoBarcode(e) {
  if (checkedBarcode.checked) {
    let link = checkedBarcode.getAttribute("data-link");
    let linkCompleto = link + "?codigo=" + e.target.value;
    //cuando da enter llamar funcion
    if (e.keyCode === 13) {
      buscarBarcode(linkCompleto);
      inputBuscarBarcode.value = "";
      inputBuscarBarcode.focus();
    }
  }
}

//traer datos de barcode
async function buscarBarcode(link) {
  const response = await fetch(link);
  const data = await response.json();
  if (data.status) {
    const result = {
      id: data.data.id,
      codigo: data.data.codigo,
      detalle: data.data.detalle,
      unidad: data.data.unidad,
      precio_compra: data.data.precio_compra,
      precio_venta: data.data.precio_venta,
      codigo_afectacion_alt: data.data.codigo_afectacion_alt,
      codigo_afectacion: data.data.codigo_afectacion,
      nombre_afectacion: data.data.nombre_afectacion,
      tipo_afectacion: data.data.tipo_afectacion,
    };
    //sacar el producto del array productosCarrito
    const producto = productosCarrito.find(
      (producto) => producto.id == data.data.id
    );

    //controlar si existe producto
    if (producto) {
      if (producto.cantidad < data.data.stock) {
        agregarProductoCarrito(result);
      } else {
        toastPersonalizado("error", "Stock Insuficiente");
      }
    } else {
      agregarProductoCarrito(result);
    }
    // agregarProductoCarrito(result);
  } else {
    toastPersonalizado("error", "No se encontro el producto");
    return;
  }
}

//agregar lista de carrito
function agregarProductoCarrito(data) {
  //tipo de afectacion
  const tipo_precio = {
    10: "01",
    11: "02",
    12: "02",
    13: "02",
    14: "02",
    15: "02",
    16: "02",
    20: "01",
    21: "02",
    30: "01",
    31: "02",
    32: "02",
    33: "02",
    34: "02",
    35: "02",
    36: "02",
  };

  const producto = {
    id: data.id,
    codigo: data.codigo,
    detalle: data.detalle,
    unidad: data.unidad,
    precio_compra: data.precio_compra,
    precio_unitario: data.precio_venta,
    tipo_precio: tipo_precio[data.codigo_afectacion_alt],
    porcentaje_igv: 18,
    codigo_afectacion_alt: data.codigo_afectacion_alt,
    codigo_afectacion: data.codigo_afectacion,
    nombre_afectacion: data.nombre_afectacion,
    tipo_afectacion: data.tipo_afectacion,
    cantidad: 1,
  };
  //comparar si el producto ya existe
  if (productosCarrito.some((prod) => prod.id === producto.id)) {
    const productos = productosCarrito.map((prod) => {
      if (prod.id === producto.id) {
        let cantidad = parseInt(prod.cantidad);
        cantidad++;
        prod.cantidad = cantidad;
        return prod;
      } else {
        return prod;
      }
    });
    productosCarrito = [...productos];
  } else {
    //si no existe agregar
    productosCarrito = [...productosCarrito, producto];
  }

  carritoHTML();
}

//generar html
function carritoHTML() {
  //limpiar html
  tablaCompras.innerHTML = "";
  //recorre el carrito y genera el html
  productosCarrito.forEach((producto) => {
    const {
      id,
      codigo,
      detalle,
      precio_unitario,
      codigo_afectacion_alt,
      cantidad,
    } = producto;

    let valor_unitario = 0;
    let subtotal = 0;

    if (codigo_afectacion_alt == 10) {
      valor_unitario = precio_unitario / 1.18;
      subtotal = precio_unitario * cantidad;
    } else if (codigo_afectacion_alt == 20 || codigo_afectacion_alt == 30) {
      valor_unitario = precio_unitario;
      subtotal = precio_unitario * cantidad;
    } else {
      valor_unitario = 0;
      subtotal = 0;
    }

    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${detalle}</td>

        <td width="100"><input type="number" min="1"  class="form-control form-control-sm cambioCant" data-id="${id}"  value="${cantidad}"></td>

        <td>${(valor_unitario * 1).toFixed(2)}</td>

        <td width="100"><input type="number" min="1"  class="form-control form-control-sm cambioPrecio" data-id="${id}"  value="${(
      precio_unitario * 1
    ).toFixed(2)}"></td>

        <td class="text-center">${(subtotal * 1).toFixed(2)}</td>
        <td class="text-center">
            <a href="#" class="borrarProducto text-danger" data-id="${id}"><i class="bi bi-trash3"></i></a>
        </td>
        `;
    //agrega el html del carrito en el tbody
    tablaCompras.appendChild(row);
  });

  //agregar el carrito de compras al storage
  sincronizarStorage();
  sumaTotalPagar();
}

//pasar datos a localStorage
function sincronizarStorage() {
  localStorage.setItem(
    "productocarritonotas",
    JSON.stringify(productosCarrito)
  );
}

//cambiar precio y cantidad
async function cambioCantPrecioProducto(e) {
  //cambiar cantidad de productos
  if (e.target.classList.contains("cambioCant")) {
    const productoId = e.target.getAttribute("data-id");
    const url = urlProductosId + "?id=" + productoId;
    const response = await fetch(url);
    const data = await response.json();

    // stock
    const stock = data.data.stock;
    //cantidadInput
    const cantidadInput = parseInt(e.target.value);

    // actualizar cantidad productosCarrito
    productosCarrito.forEach((producto) => {
      if (producto.id == productoId) {
        if (cantidadInput < 1) {
          producto.cantidad = 1;
          toastPersonalizado("error", "Cantidad no puede ser menor a 1");
          return;
        } else if (cantidadInput > stock) {
          producto.cantidad = stock;
          toastPersonalizado("error", "Stock Insuficiente");
          return;
        } else {
          producto.cantidad = cantidadInput;
        }
      }
    });
  }

  //verificar que tenga la clase cambioPrecio
  if (e.target.classList.contains("cambioPrecio")) {
    const productoId = Number(e.target.getAttribute("data-id"));
    const precio_unitario = Number(e.target.value);
    // buscar productoId en productosCarrito
    productosCarrito.forEach((producto) => {
      if (producto.id === productoId) {
        if (precio_unitario < producto.precio_compra * 1.18) {
          producto.precio_unitario = producto.precio_compra * 1.18;
          toastPersonalizado(
            "error",
            "Precio de venta no puede ser menor al costo"
          );
          return;
        }

        producto.precio_unitario = precio_unitario;
        console.log(producto);
      }
    });
  }

  carritoHTML();
}

//eliminar lista de carrito
function eliminarProducto(e) {
  //e.target padre del elemento que se presiona
  if (e.target.parentNode.classList.contains("borrarProducto")) {
    e.preventDefault();
    const productoId = e.target.parentNode.getAttribute("data-id");
    //convertir de string a numero
    productosCarrito = productosCarrito.filter(
      (producto) => producto.id !== parseInt(productoId)
    );

    carritoHTML();
  }
}

//cuando ingresa datos en input nombre
function agregarProductoNombre(e) {
  if (checkedNombre.checked) {
    let link = checkedNombre.getAttribute("data-link");
    buscarNombreProducto(link);
  }
}

//traer datos por nombre
function buscarNombreProducto(link) {
  const buscarNombre = new Autocomplete(inputBuscarNombre, link);
  buscarNombre.seleccionar = (elemento) => {
    const producto = productosCarrito.find(
      (producto) => producto.id == elemento.id
    );

    inputBuscarNombre.value = "";
    inputBuscarNombre.focus();

    //controlar si existe producto
    if (producto) {
      if (producto.cantidad < elemento.stock) {
        agregarProductoCarrito(elemento);
      } else {
        toastPersonalizado("error", "Stock Insuficiente");
      }
    } else {
      agregarProductoCarrito(elemento);
    }
  };
}

function sumaTotalPagar() {
  const rowGratuita = document.querySelector(".rowGratuita");
  const rowExonerada = document.querySelector(".rowExonerada");
  const rowInafecta = document.querySelector(".rowInafecta");
  const rowGravada = document.querySelector(".rowGravada");

  let op_gratuitas = 0;
  let op_exoneradas = 0;
  let op_inafectas = 0;
  let op_grabadas = 0;
  let igv_gratuita = 0;
  let igv_exonerada = 0;
  let igv_inafecta = 0;
  let igv_grabada = 0;
  let igv_total = 0;
  let total = 0;
  productosCarrito.forEach((producto) => {
    if (producto.codigo_afectacion_alt == "10") {
      op_grabadas += (producto.precio_unitario * producto.cantidad) / 1.18;
      igv_grabada +=
        ((producto.precio_unitario * producto.cantidad) / 1.18) * 0.18;
      igv_total +=
        ((producto.precio_unitario * producto.cantidad) / 1.18) * 0.18;
      total += Number(producto.precio_unitario * producto.cantidad);
    } else if (
      producto.codigo_afectacion_alt == "11" ||
      producto.codigo_afectacion_alt == "12" ||
      producto.codigo_afectacion_alt == "13" ||
      producto.codigo_afectacion_alt == "14" ||
      producto.codigo_afectacion_alt == "15" ||
      producto.codigo_afectacion_alt == "16"
    ) {
      op_gratuitas += producto.precio_unitario * producto.cantidad;
      igv_gratuita += producto.precio_unitario * producto.cantidad * 0.18;
      igv_total += 0;
      total += 0;
    } else if (producto.codigo_afectacion_alt == "20") {
      op_exoneradas += producto.precio_unitario * producto.cantidad;
      igv_exonerada += 0;
      igv_total += 0;
      total += producto.precio_unitario * producto.cantidad;
    } else if (producto.codigo_afectacion_alt == "30") {
      op_inafectas += producto.precio_unitario * producto.cantidad;
      igv_inafecta += 0;
      igv_total += 0;
      total += producto.precio_unitario * producto.cantidad;
    } else if (
      producto.codigo_afectacion_alt == "21" ||
      producto.codigo_afectacion_alt == "31" ||
      producto.codigo_afectacion_alt == "32" ||
      producto.codigo_afectacion_alt == "33" ||
      producto.codigo_afectacion_alt == "34" ||
      producto.codigo_afectacion_alt == "35" ||
      producto.codigo_afectacion_alt == "36"
    ) {
      op_gratuitas += producto.precio_unitario * producto.cantidad;
      igv_gratuita += 0;
      igv_total += 0;
      total += 0;
    }
  });

  if (op_gratuitas > 0) {
    // eliminar classlist d-none rowGratuita
    rowGratuita.classList.remove("d-none");
  } else {
    rowGratuita.classList.add("d-none");
  }

  if (op_exoneradas > 0) {
    rowExonerada.classList.remove("d-none");
  } else {
    rowExonerada.classList.add("d-none");
  }

  if (op_inafectas > 0) {
    rowInafecta.classList.remove("d-none");
  } else {
    rowInafecta.classList.add("d-none");
  }

  if (op_grabadas > 0) {
    rowGravada.classList.remove("d-none");
  } else {
    rowGravada.classList.add("d-none");
  }

  // enviar para mostrar
  inputGratuita.value = op_gratuitas.toFixed(2);
  inputExonerada.value = op_exoneradas.toFixed(2);
  inputInafecta.value = op_inafectas.toFixed(2);
  inputGrabada.value = op_grabadas.toFixed(2);
  inputInpuestoTotal.value = igv_total.toFixed(2);
  inputTotalVenta.value = total.toFixed(2);
  inputIgv_gratuita.value = igv_gratuita.toFixed(2);
  inputIgv_exonerada.value = igv_exonerada.toFixed(2);
  inputIgv_inafecta.value = igv_inafecta.toFixed(2);
  inputIgv_grabada.value = igv_grabada.toFixed(2);
}

//eliminar localStorage
function eliminarLocalStorage() {
  //limpiar carrito
  productosCarrito = [];
  localStorage.removeItem("productocarritonotas");
  carritoHTML();
}

//menu de tipo de comprobante
async function menuTipoComprobante() {
  const response = await fetch(urlTipoComprobante);
  const data = await response.json();

  //si codigo=03 select por defecto
  let html = "";
  data.forEach((item) => {
    if (item.codigo == 03) {
      html += `<option value="${item.codigo}" selected>${item.descripcion}</option>`;
    } else {
      html += `<option value="${item.codigo}">${item.descripcion}</option>`;
    }
  });
  inputTipoComprobante.innerHTML = html;

  //cargar correlativo
  menuSerie();
}

//cambio del menu de tipo de comprobante
function cambioMenuTipoComprobante() {
  inputTipoComprobante.addEventListener("change", () => {
    menuSerie();
  });
}

//menu serie
async function menuSerie() {
  const tipoComprobante = inputTipoComprobante.value;
  const response = await fetch(
    `${urlSerieCorrelativo}?tipo=${tipoComprobante}`
  );
  const data = await response.json();
  //filtrar nuevo array por inputTipoDocumentoReferencia
  const dataFiltrada = data.filter(
    (item) => item.tipo == inputTipoDocumentoReferencia.value
  );

  let html = "";
  dataFiltrada.forEach((item) => {
    html += `<option value="${item.id}" data-serie="${item.serie}" data-correlativo="${item.correlativo}">${item.serie}-${item.correlativo}</option>`;
  });
  inputSerieId.innerHTML = html;

  tipoNota(inputTipoComprobante.value);
}

//CAMBIO DE MENU SERIE
function cambioMenuSerie() {
  inputSerieId.addEventListener("change", () => {
    tipoNota(inputSerieId.value);
  });
}

//function tipo de nota
async function tipoNota(tipoDoc) {
  const link = `${urlTipoNota}?nota=${tipoDoc}`;
  const response = await fetch(link);
  const data = await response.json();
  let html = "";
  //un seleccione ...
  html += `<option value="">Seleccione...</option>`;
  data.forEach((item) => {
    html += `<option value="${item.codigo}">${item.descripcion}</option>`;
  });
  inputTipoNota.innerHTML = html;
}

//traer datos por nombre
function buscarVenta() {
  const buscarNombre = new Autocomplete(inputBuscarComprobante, urlBuscarVenta);
  buscarNombre.seleccionar = (elemento) => {
    inputComprobante.value = elemento.serie + "-" + elemento.correlativo;
    inputCliente.value = elemento.cliente;
    //data.fecha_emision  yyyy-mm-dd hh:mm:ss cambiar a yyyy-mm-dd
    inputFechaVenta.value = elemento.fecha_emision.substring(0, 10);

    //inputs de referencia
    inputTipoDocumentoReferencia.value = elemento.tipodoc;
    inputSerieReferencia.value = elemento.serie;
    inputCorrelativoReferencia.value = elemento.correlativo;
    moneda = elemento.moneda;
    clienteId = elemento.cliente_id;
    venta_id = elemento.id;

    //data.productos localStorage
    // localStorage.setItem("productocarritonotas", data.productos);
    productosCarrito = JSON.parse(elemento.productos);

    //verificar si es factura
    if (elemento.tipodoc == "01") {
      if (elemento.forma_pago == "Contado") {
        condicionPago();
      }
      if (elemento.forma_pago == "Credito") {
        condicionesCredito();
        mostrarCuotas(elemento.cuotas);
      }
    }

    carritoHTML();
    menuSerie();

    //limpiar
    inputBuscarComprobante.value = "";
  };
}

//function condision de pago
function condicionPago() {
  let selected = `
            <select class="form-select form-select-sm" id="selectTipoDePago" name="forma_pago">
              <option value="Contado">Contado</option>
              <option value="Credito">Credito</option>
            </select>`;
  condicion_Pago.innerHTML = selected;
  cantidad_cuotas();
}

function cantidad_cuotas() {
  let selectTipoDePago = document.getElementById("selectTipoDePago");
  // console.log(selectTipoDePago);
  selectTipoDePago.addEventListener("change", function (e) {
    if (e.target.value == "Credito") {
      let selected = `
        <div class="input-group input-group-sm">
          <input min="0" type="number" class="form-control" id="inputCantidadCuotas">
          <button class="btn btn-outline-secondary fs-5 p-0 px-2" type="button" id="agregarCuotas">
            +
          </button>
        </div>`;
      cantidadCuotas.innerHTML = selected;
      agregarCuotas();
    } else {
      cantidadCuotas.innerHTML = "";
      cuotasContainer.innerHTML = "";
    }
  });
}

function agregarCuotas() {
  let agregarCuotas = document.getElementById("agregarCuotas");
  agregarCuotas.addEventListener("click", function (e) {
    cuotasCreditos = [];
    let inputCantidadCuotas = document.getElementById("inputCantidadCuotas");
    if (inputCantidadCuotas.value == 0 || inputCantidadCuotas.value == "") {
      toastPersonalizado("error", "Ingrese una cantidad de cuotas");
      return;
    }

    // console.log(inputCantidadCuotas.value);
    let html = "";
    for (let i = 0; i < inputCantidadCuotas.value; i++) {
      //digitos 1001, 1002, 1003, 1004, 1005
      let numero = 10000 + i + 1;
      //comvertir a string
      let numeroString = numero.toString();
      //obtener los ultimos 3 digitos
      let numeroFinal = numeroString.substring(2, 5);

      let cuotaCredito = {
        id: i + 1,
        cuota: `Cuota` + numeroFinal,
        fecha: "",
        monto: "",
      };

      // agregar obejo a cuotasCreditos
      cuotasCreditos.push(cuotaCredito);

      html += `
        <div class="col-5">
            <input type="number" class="form-control form-control-sm  monto" data-id="${
              i + 1
            }"  placeholder="monto ${i + 1}">
          </div>
          <div class="col-5">
            <input type="date"  class="form-control form-control-sm fecha" data-id="${
              i + 1
            }" >
          </div>
        </div>`;
    }
    cuotasContainer.innerHTML = html;
    inputCantidadCuotas.value = "";
  });
}

function agregarCuotasArray(e) {
  //si la clase es monto
  if (e.target.classList.contains("monto")) {
    //obtener data-id
    let id = e.target.dataset.id;
    //modificar el objeto con el id en cuotasCreditos
    cuotasCreditos.forEach((cuota) => {
      if (cuota.id == id) {
        cuota.monto = e.target.value;
      }
    });
  }
  //si la clase es fecha
  if (e.target.classList.contains("fecha")) {
    let id = e.target.dataset.id;
    //modificar el objeto con el id en cuotasCreditos
    cuotasCreditos.forEach((cuota) => {
      if (cuota.id == id) {
        cuota.fecha = e.target.value;
      }
    });
  }
}

//condicionesCredito
function condicionesCredito(cuotas) {
  //dejar seleccionado credito
  let selected = `
            <select class="form-select form-select-sm" id="selectTipoDePago" name="forma_pago">
              <option value="Contado">Contado</option>
              <option value="Credito" selected>Credito</option>
            </select>`;
  condicion_Pago.innerHTML = selected;

  //entrada de cuotas
  let entrada = `
        <div class="input-group input-group-sm">
          <input min="0" type="number" class="form-control" id="inputCantidadCuotas">
          <button class="btn btn-outline-secondary fs-5 p-0 px-2" type="button" id="agregarCuotas">
            +
          </button>
        </div>`;
  cantidadCuotas.innerHTML = entrada;
  cantidad_cuotas();
  agregarCuotas();
}

//mmostrar cuotas
function mostrarCuotas(cuotas) {
  let arrayCuotas = JSON.parse(cuotas);
  //agregar cuotas al array
  cuotasCreditos = arrayCuotas;

  let html = "";
  arrayCuotas.forEach((cuota) => {
    html += `
        <div class="col-5">
            <input type="number" class="form-control form-control-sm  monto" data-id="${cuota.id}"  placeholder="monto ${cuota.id}" value="${cuota.monto}">
          </div>
          <div class="col-5">
            <input type="date"  class="form-control form-control-sm fecha" data-id="${cuota.id}" value="${cuota.fecha}">
          </div>
        </div>`;
  });

  cuotasContainer.innerHTML = html;
}

function agregarCuotasArray(e) {
  //si la clase es monto
  if (e.target.classList.contains("monto")) {
    //obtener data-id
    let id = e.target.dataset.id;
    //modificar el objeto con el id en cuotasCreditos
    cuotasCreditos.forEach((cuota) => {
      if (cuota.id == id) {
        cuota.monto = e.target.value;
      }
    });
  }
  //si la clase es fecha
  if (e.target.classList.contains("fecha")) {
    let id = e.target.dataset.id;
    //modificar el objeto con el id en cuotasCreditos
    cuotasCreditos.forEach((cuota) => {
      if (cuota.id == id) {
        cuota.fecha = e.target.value;
      }
    });
  }
}

//GENERAR NOTA DE CREDITO Y DEBITO
function generarNota() {
  btnEnviarNotas.addEventListener("click", async (e) => {
    e.preventDefault();
    if (productosCarrito.length === 0) {
      toastPersonalizado("error", "No hay productos en el carrito");
      return;
    }

    let forma_pago = "";
    // comprobar tipo de comprobante y forma de pago
    if (inputTipoDocumentoReferencia.value == "03") {
      forma_pago = "Contado";
    } else if (inputTipoDocumentoReferencia.value == "01") {
      let tipoPago = document.getElementById("selectTipoDePago").value;
      if (tipoPago == "Contado") {
        forma_pago = tipoPago;
      } else if (tipoPago == "Credito") {
        forma_pago = tipoPago;
        //verificar si hay cuotas
        if (cuotasCreditos.length === 0) {
          toastPersonalizado("error", "Selecciono Credito y No hay cuotas");
          return;
        }

        //verificar los elemento cuotasCreditos no esten vacios
        let cuotasCreditosVacios = cuotasCreditos.filter(
          (cuota) => cuota.fecha == "" || cuota.monto == ""
        );
        if (cuotasCreditosVacios.length > 0) {
          toastPersonalizado("error", "Ingrese todos los campos de las cuotas");
          return;
        }

        //verificar que las fechas sean mayores a la fecha actual
        let fechaActual = new Date();
        let fechaActualString = fechaActual.toISOString().split("T")[0];
        let cuotasCreditosFechaMenor = cuotasCreditos.filter(
          (cuota) => cuota.fecha < fechaActualString
        );
        if (cuotasCreditosFechaMenor.length > 0) {
          toastPersonalizado("error", "Ingrese una fecha mayor a la actual");
          return;
        }

        //verificar que los montos sean mayores a 0
        let cuotasCreditosMontoMenor = cuotasCreditos.filter(
          (cuota) => cuota.monto <= 0
        );
        if (cuotasCreditosMontoMenor.length > 0) {
          toastPersonalizado("error", "Ingrese un monto mayor a 0");
          return;
        }

        //verificar que los cuotasCreditos montos sumen inputTotalVenta
        let sumaMontos = 0;
        cuotasCreditos.forEach((cuota) => {
          sumaMontos += parseFloat(cuota.monto);
        });
        if (sumaMontos != inputTotalVenta.value) {
          toastPersonalizado(
            "error",
            "La suma de las cuotas no es igual al total"
          );
          return;
        }
      }
    }

    if (inputTipoNota.value == "") {
      toastPersonalizado("error", "Seleccione un motivo (tipo de nota)");
      return;
    }
    if (inputDescripcion.value == "") {
      toastPersonalizado("error", "Ingrese una descripcion");
      return;
    }
    // capturar data-serie de inputSerieId

    let nombre_tipodoc =
      inputTipoComprobante.options[inputTipoComprobante.selectedIndex].text;

    let motivo = inputTipoNota.options[inputTipoNota.selectedIndex].text;

    let serie = inputSerieId.options[inputSerieId.selectedIndex].dataset.serie;
    let correlativo =
      inputSerieId.options[inputSerieId.selectedIndex].dataset.correlativo;

    //crear data para enviar
    const data = new FormData();
    data.append("usuario_id", inputUserId.value);
    data.append("venta_id", venta_id);
    data.append("tipodoc", inputTipoComprobante.value);
    data.append("nombre_tipodoc", nombre_tipodoc);
    data.append("serie_id", inputSerieId.value);
    data.append("serie", serie);
    data.append("correlativo", correlativo);
    data.append("codmotivo", inputTipoNota.value);
    data.append("motivo", motivo);
    data.append("descripcion", inputDescripcion.value);
    data.append("moneda", moneda);
    data.append("fecha_emision", inputFechaNota.value);
    data.append("op_gratuitas", inputGratuita.value);
    data.append("op_exoneradas", inputExonerada.value);
    data.append("op_inafectas", inputInafecta.value);
    data.append("op_gravadas", inputGrabada.value);
    data.append("igv_gratuita", inputIgv_gratuita.value);
    data.append("igv_exonerada", inputIgv_exonerada.value);
    data.append("igv_inafecta", inputIgv_inafecta.value);
    data.append("igv_grabada", inputIgv_grabada.value);
    data.append("igv_total", inputInpuestoTotal.value);
    data.append("total", inputTotalVenta.value);
    data.append("cliente_id", clienteId);
    data.append("forma_pago", forma_pago);
    data.append("cuotas", JSON.stringify(cuotasCreditos));
    data.append("productos", JSON.stringify(productosCarrito));
    data.append("tipodoc_ref", inputTipoDocumentoReferencia.value);
    data.append("serie_ref", inputSerieReferencia.value);
    data.append("correlativo_ref", inputCorrelativoReferencia.value);

    //enviar data
    const response = await fetch(urlCreate, {
      method: "POST",
      body: data,
    });
    const dRes = await response.json();

    if (dRes.status) {
      eliminarLocalStorage();
      Swal.fire({
        title: "Nota Generada",
        text: "¿Desea imprimir el comprobante?",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: "No",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = urlIndexNotas;
          window
            .open(
              "reporte?ticket=" + dRes.id,
              "Imprimir Comprobante",
              "width=400, height=700"
            )
            .print()
            .close();
        } else {
          window.location.href = urlIndexNotas;
        }
      });
    } else {
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "error",
        title: dRes.Message,
        showConfirmButton: false,
        timer: 5000,
      });
    }
  });
}
