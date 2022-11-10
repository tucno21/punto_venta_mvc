// const table = new simpleDatatables.DataTable("#simpleDatatable");

const urlPermission = document
  .querySelector("#listaPermisos")
  .getAttribute("data-url");
const editarPermiso = document
  .querySelector("#editarPermiso")
  .getAttribute("data-url");
const eliminarPermiso = document
  .querySelector("#eliminarPermiso")
  .getAttribute("data-url");

async function getDataA() {
  const response = await fetch(urlPermission); // espera a que la respuesta llegue
  const data = await response.json(); // la respuesta es un json
  //agregar botonos
  data.forEach((element) => {
    element[
      "actions"
    ] = `<a href="${editarPermiso}?id=${element.id}" class="btn btn-outline-warning btn-sm editarPermiso"><i class="bi bi-pencil"></i></a>
        <a href="${eliminarPermiso}?id=${element.id}" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3"></i></a>`;
  });

  //crear tabla
  const table = new simpleDatatables.DataTable("#simpleDatatable", {
    // searchable: true,
    // fixedHeight: true,
    data: {
      headings: ["#", "Nombre", "Descripción", "Acciones"],
      data: data.map((item) => {
        return [item.id, item.per_name, item.description, item.actions];
      }),
    },
  });

  //   botonPermiso();
}
getDataA();

// //editar permiso
// function botonPermiso() {
//   const btnEditar = document.querySelectorAll(".editarPermiso");
//   btnEditar.forEach((btn) => {
//     btn.addEventListener("click", function (e) {
//       e.preventDefault();
//       console.log("boton");
//     });
//   });
// }
