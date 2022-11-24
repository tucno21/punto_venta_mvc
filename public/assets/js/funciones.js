//funcion toast personalizado
function toastPersonalizado(icon, title) {
  Swal.fire({
    toast: true,
    position: "top-right",
    icon: icon,
    title: title,
    showConfirmButton: false,
    timer: 2000,
    //modificar color
    iconColor: "white",
    customClass: {
      popup: "colored-toast",
    },
    timerProgressBar: true,
  });
}
//mostrar mensaje de error
function mensajeErrorInput(input, mensaje) {
  if (input.nextElementSibling) {
    input.nextElementSibling.remove();
  }
  if (mensaje == undefined) {
    input.classList.remove("is-invalid");
  } else {
    // crear div
    let div = document.createElement("div");
    div.classList.add("invalid-feedback");
    div.innerHTML = mensaje;
    //agregar is-invalid
    input.classList.add("is-invalid");
    input.parentElement.appendChild(div);
  }
}
//limpiar mensaje de error
function limpiarErrrorInput(array) {
  array.forEach((input) => {
    input.value = "";
    input.classList.remove("is-invalid");
    if (input.nextElementSibling) {
      input.nextElementSibling.remove();
    }
  });
}

function imagen(inputFoto, previsualizar) {
  let visorFoto = document.querySelector(inputFoto);
  visorFoto.addEventListener("change", function (e) {
    console.log(e.target.files[0]);
    let file = e.target.files[0];
    //validar que sea una imagen
    if (
      file["type"] != "image/jpg" &&
      file["type"] != "image/png" &&
      file["type"] != "image/jpeg"
    ) {
      visorFoto.value = "";
      Swal.fire({
        title: "Error al subir imagen",
        text: "la imagen debe ser de formato JPQ o PNG",
        icon: "error",
        confirmButtonText: "Cerrar",
      });
      //validar tamaño de la imagen
    } else if (file["size"] > 1000000) {
      visorFoto.value = "";
      Swal.fire({
        title: "Error de tamaño de imagen",
        text: "la imagen no debe pesar mas de 1MB",
        icon: "error",
        confirmButtonText: "Cerrar",
      });
    } else {
      //clase de js hace lectura de archivo
      var datosImagen = new FileReader();
      //leer como dato url la imagen cargada
      datosImagen.readAsDataURL(file);
      //cuando la imagen este cargada
      datosImagen.addEventListener("load", function (event) {
        //asignar la imagen al elemento img
        document
          .querySelector(previsualizar)
          .setAttribute("src", event.target.result);
      });
    }
  });
}

//buscar dniruc
async function buscarDNIRUC(number, document) {
  const url = "https://api_dni_ruc.test/consulta";

  if (document == "dni") {
    const link = `${url}?dni=${number}`;
    const response = await fetch(link);
    const data = await response.json();
    return data;
  }

  if (document == "ruc") {
    const link = `${url}?ruc=${number}`;
    const response = await fetch(link);
    const data = await response.json();
    return data;
  }
}

//crear clase Autocomplete que solicita dos parametros(input, link) y que espero hasta que se seleccione un elemento y el metodo seleccionar me devuelve el objeto seleccionado
class Autocomplete {
  constructor(input, link) {
    this.input = input;
    this.link = link;
    this.seleccionar = null;
    this.elementos = [];
    this.input.addEventListener("input", this.buscar.bind(this));
    this.input.addEventListener("focus", this.buscar.bind(this));
    this.input.addEventListener("blur", this.cerrar.bind(this));
  }
  buscar() {
    if (this.input.value.length >= 3) {
      const url = this.link + `?search=${this.input.value}`;
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          this.elementos = data;
          this.render();
        });
    }
  }
  render() {
    this.limpiar();
    if (this.elementos.length > 0) {
      // crear elemento ul
      const contenedor = document.createElement("UL");
      contenedor.classList.add("ui-menu");
      contenedor.setAttribute("id", "listaBusqueda");
      //agregar hermano siguiente input
      this.input.parentElement.appendChild(contenedor);

      this.elementos.forEach((elemento) => {
        let item = document.createElement("LI");
        item.classList.add("ui-menu-item");
        item.innerHTML = elemento.textItem;
        item.addEventListener("click", () => {
          // this.input.value = elemento.nombre;
          // this.seleccionar = elemento;
          this.seleccionar(elemento);
          this.limpiar();
        });
        contenedor.appendChild(item);
      });
    }
  }
  limpiar() {
    let elementos = document.querySelectorAll(".ui-menu-item");
    elementos.forEach((elemento) => {
      elemento.remove();
    });
  }
  cerrar() {
    setTimeout(() => {
      this.limpiar();
    }, 200);
  }
}
