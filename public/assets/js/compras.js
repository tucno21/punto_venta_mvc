//url - enlaces
const urlCreate = document.querySelector("#urlCreate").getAttribute("data-url");
const urlTipoComprobante = document
  .querySelector("#urlTipoComprobante")
  .getAttribute("data-url");
const urlBuscarProveedor = document
  .querySelector("#urlBuscarProveedor")
  .getAttribute("data-url");
//modal y botones
const modalInputs = new bootstrap.Modal("#modalInputs");
const btnRegistrar = document.querySelector("#btnRegistrar");
const btnFormularioRegistrar = document.querySelector(
  "#btnFormularioRegistrar"
);
//inputs para enviar
const listId = document.querySelector("#listId");
const inputTipoComprobante = document.querySelector("#inputTipoComprobante");
const inputSerie = document.querySelector("#inputSerie");
const inputFechaCompra = document.querySelector("#inputFechaCompra");
const inputProveedorId = document.querySelector("#inputProveedorId");
const inputUserId = document.querySelector("#inputUserId");
const inputTotalCompra = document.querySelector("#inputTotalCompra");
const btnEnviarComprar = document.querySelector("#btnEnviarComprar");
//inputs dinamicos
const inputBuscarProveedor = document.querySelector("#inputBuscarProveedor");

//botones y inputs para registrar proveedor
const urlCreateProveedor = document
  .querySelector("#urlCreateProveedor")
  .getAttribute("data-url");
const btnBuscarCliente = document.querySelector("#btnBuscarCliente");
const inputDocumento = document.querySelector("#inputDocumento");
const inputNombre = document.querySelector("#inputNombre");
const inputDireccion = document.querySelector("#inputDireccion");
const inputTelefono = document.querySelector("#inputTelefono");

//barcode scanner
const checkedBarcode = document.getElementById("checkedBarcode");
const checkedNombre = document.getElementById("checkedNombre");
const grupoBarcode = document.getElementById("grupoBarcode");
const grupoNombre = document.getElementById("grupoNombre");
const inputBuscarBarcode = document.querySelector("#inputBuscarBarcode");
const inputBuscarNombre = document.querySelector("#inputBuscarNombre");

//carrito de compras
const tablaCompras = document.querySelector("#tablaCompras");
let productosCarrito = [];

//cargar todos los documentos
cargarEventListeners();
function cargarEventListeners() {
  //cambio de checkbox
  checkedBarcode.addEventListener("click", cambioCheckedBarcode);
  checkedNombre.addEventListener("click", cambioCheckedNombre);
  //buscar producto
  inputBuscarBarcode.addEventListener("keyup", agregarProductoBarcode);
  inputBuscarNombre.addEventListener("keyup", agregarProductoNombre);

  tablaCompras.addEventListener("click", eliminarProducto);

  document.addEventListener("DOMContentLoaded", () => {
    productosCarrito =
      JSON.parse(localStorage.getItem("productocarritocompras")) || [];
    carritoHTML();

    menuTipoComprobante();
    botonRegistrarProveedor();
    // buscarProveedorInput();
    generarCompra();
  });
}

//menu de tipo de comprobante
async function menuTipoComprobante() {
  const response = await fetch(urlTipoComprobante);
  const data = await response.json();

  let html = `<option value="">Seleccione...</option>`;
  //   let html;
  data.forEach((item) => {
    html += `<option value="${item.id}">${item.descripcion}</option>`;
  });
  inputTipoComprobante.innerHTML = html;
}

//boton registrar proveedor
function botonRegistrarProveedor() {
  btnRegistrar.addEventListener("click", () => {
    inputDocumento.value = "";
    inputNombre.value = "";
    inputDireccion.value = "";
    inputTelefono.value = "";

    modalInputs.show();
    buscarProveedor();
    formularioRegistrarProveedor();
  });
}

//boton para buscar Proveedor
async function buscarProveedor() {
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

//registra proveedor
function formularioRegistrarProveedor() {
  btnFormularioRegistrar.addEventListener("click", async () => {
    //crear data para enviar
    const data = new FormData();
    data.append("documento", inputDocumento.value);
    data.append("nombre", inputNombre.value);
    data.append("direccion", inputDireccion.value);
    data.append("telefono", inputTelefono.value);

    //enviar data
    const response = await fetch(urlCreateProveedor, {
      method: "POST",
      body: data,
    });
    const dRes = await response.json();

    if (dRes.status) {
      inputBuscarProveedor.value = inputDocumento.value;
      inputProveedorId.value = dRes.data;

      limpiarErrrorInput([
        inputDocumento,
        inputNombre,
        inputDireccion,
        inputTelefono,
      ]);
      modalInputs.hide();
      toastPersonalizado("success", "¨Proveedor Registrado");
    } else {
      mensajeErrorInput(inputDocumento, dRes.data.documento);
      mensajeErrorInput(inputNombre, dRes.data.nombre);
      mensajeErrorInput(inputDireccion, dRes.data.direccion);
      mensajeErrorInput(inputTelefono, dRes.data.telefono);
    }
  });
}

//invocar clase de autocompletar
const autocompleteProveedor = new Autocomplete(
  inputBuscarProveedor,
  urlBuscarProveedor
);
autocompleteProveedor.seleccionar = (elemento) => {
  inputProveedorId.value = elemento.id;
  inputBuscarProveedor.value = elemento.nombre;
  // console.log(elemento);
};

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
      // console.log(linkCompleto);
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
      detalle: data.data.detalle,
      precio_compra: data.data.precio_compra,
      precio_venta: data.data.precio_venta,
      codigo: data.data.codigo,
    };

    // //sacar el producto del array productosCarrito
    // const producto = productosCarrito.find(
    //   (producto) => producto.id == data.data.id
    // );

    // //controlar si existe producto
    // if (producto) {
    //   if (producto.cantidad < data.data.stock) {
    //     agregarProductoCarrito(result);
    //   } else {
    //     toastPersonalizado("error", "Stock Insuficiente");
    //   }
    // } else {
    //   agregarProductoCarrito(result);
    // }
    agregarProductoCarrito(result);
  } else {
    toastPersonalizado("error", "No se encontro el producto");
    return;
  }
}

//agregar lista de carrito
function agregarProductoCarrito(data) {
  // console.log(data);
  const producto = {
    id: data.id,
    detalle: data.detalle,
    precio_compra: data.precio_compra,
    precio_venta: data.precio_venta,
    codigo: data.codigo,
    cantidad: 1,
  };
  // console.log(producto);
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
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${producto.detalle}</td>
        <td width="100"><input type="number" min="1"  class="form-control form-control-sm productoCantidad" data-id="${
          producto.id
        }"  value="${producto.cantidad}"></td>

        <td width="100"><input type="number" min="1"  class="form-control form-control-sm productoPC" data-id="${
          producto.id
        }"  value="${producto.precio_compra}"></td>

        <td width="100"><input type="number" min="1"  class="form-control form-control-sm productoPV" data-id="${
          producto.id
        }"  value="${producto.precio_venta}"></td>

        <td class="text-center">${
          producto.cantidad * producto.precio_compra
        }</td>
        <td class="text-center">
            <a href="#" class="borrar-producto text-danger" data-id="${
              producto.id
            }"><i class="bi bi-trash3"></i></a>
        </td>
        `;
    //agrega el html del carrito en el tbody
    tablaCompras.appendChild(row);
  });

  //agregar el carrito de compras al storage
  sincronizarStorage();
  sumaTotalPagar();
  //agregar evento a los input cantidad
  aumentarCantidad();
  cambiarPrecioCompra();
  cambiarPrecioVenta();
}

//pasar datos a localStorage
function sincronizarStorage() {
  localStorage.setItem(
    "productocarritocompras",
    JSON.stringify(productosCarrito)
  );
}

//suma total a pagar
function sumaTotalPagar() {
  inputTotalCompra.value = "";
  let total = 0;
  productosCarrito.forEach((producto) => {
    total += producto.cantidad * producto.precio_compra;
  });
  // return total;
  inputTotalCompra.value = total;
}

//cambiar cantidad de productos
function aumentarCantidad() {
  const productoCantidad = document.querySelectorAll(".productoCantidad");
  productoCantidad.forEach((cantidad) => {
    cantidad.addEventListener("change", (e) => {
      const productoId = e.target.getAttribute("data-id");
      const cantidad = e.target.value;
      productosCarrito.forEach((producto) => {
        //cantidad no puede ser menor a 1
        if (cantidad < 1) {
          producto.cantidad = 1;
          toastPersonalizado("error", "Cantidad no puede ser menor a 1");
        } else {
          if (producto.id === parseInt(productoId)) {
            producto.cantidad = parseInt(cantidad);
          }
        }
      });
      carritoHTML();
    });
  });
}

//cambiar precio de compra
function cambiarPrecioCompra() {
  const productoPC = document.querySelectorAll(".productoPC");
  productoPC.forEach((pc) => {
    pc.addEventListener("change", (e) => {
      const productoId = e.target.getAttribute("data-id");
      const precio_compra = e.target.value;
      productosCarrito.forEach((producto) => {
        //precio_c no puede ser menor a 1
        if (precio_compra < 1) {
          producto.precio_compra = 1;
          toastPersonalizado("error", "Precio no puede ser menor a 1");
        } else {
          if (producto.id === parseInt(productoId)) {
            producto.precio_compra = parseInt(precio_compra);
          }
        }
      });
      carritoHTML();
    });
  });
}

//cambiar precio de venta
function cambiarPrecioVenta() {
  const productoPV = document.querySelectorAll(".productoPV");
  productoPV.forEach((pv) => {
    pv.addEventListener("change", (e) => {
      const productoId = e.target.getAttribute("data-id");
      const precio_venta = e.target.value;
      productosCarrito.forEach((producto) => {
        //precio_v no puede ser menor a precio_compra
        if (precio_venta < producto.precio_compra) {
          producto.precio_venta = producto.precio_compra;
          toastPersonalizado(
            "error",
            "Precio venta no puede ser menor a Precio Compra"
          );
        } else {
          if (producto.id === parseInt(productoId)) {
            producto.precio_venta = parseInt(precio_venta);
          }
        }
      });
      carritoHTML();
    });
  });
}

//eliminar lista de carrito
function eliminarProducto(e) {
  e.preventDefault();
  //e.target padre del elemento que se presiona
  if (e.target.parentNode.classList.contains("borrar-producto")) {
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

//traer datos por nombre jquery
function buscarNombreProducto(link) {
  const buscarNombre = new Autocomplete(inputBuscarNombre, link);
  buscarNombre.seleccionar = (elemento) => {
    // console.log(elemento);
    agregarProductoCarrito(elemento);
    inputBuscarNombre.value = "";
    inputBuscarNombre.focus();
  };
}

//generar compra
function generarCompra() {
  btnEnviarComprar.addEventListener("click", async (e) => {
    e.preventDefault();
    if (productosCarrito.length === 0) {
      toastPersonalizado("error", "No hay productos en el carrito");
      return;
    }

    //crear data para enviar
    const data = new FormData();
    data.append("tipo_comprobante_id", inputTipoComprobante.value);
    data.append("serie", inputSerie.value);
    data.append("fecha_compra", inputFechaCompra.value);
    data.append("proveedor_id", inputProveedorId.value);
    data.append("user_id", inputUserId.value);
    data.append("total", inputTotalCompra.value);
    data.append("productos", JSON.stringify(productosCarrito));

    //enviar data
    const response = await fetch(urlCreate, {
      method: "POST",
      body: data,
    });
    const dRes = await response.json();
    if (dRes.status) {
      //eliminar localStorage

      limpiarErrrorInput([
        inputTipoComprobante,
        inputSerie,
        inputFechaCompra,
        inputBuscarProveedor,
      ]);
      inputProveedorId.value = "";

      eliminarLocalStorage();
      toastPersonalizado("success", "Compra Registrado");
    } else {
      mensajeErrorInput(inputTipoComprobante, dRes.data.tipo_comprobante_id);
      mensajeErrorInput(inputSerie, dRes.data.serie);
      mensajeErrorInput(inputFechaCompra, dRes.data.fecha_compra);
      mensajeErrorInput(inputBuscarProveedor, dRes.data.proveedor_id);
    }
  });
}

//eliminar localStorage
function eliminarLocalStorage() {
  //limpiar carrito
  productosCarrito = [];
  localStorage.removeItem("productocarritocompras");
  carritoHTML();
}
