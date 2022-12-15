const formSearchDocument = document.getElementById("formSearchDocument");

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    menuTipoDocumento();
    formSearchDocument.addEventListener("submit", searchDocument);
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

//limpiar mensaje de error sin tocar los valores inputs
function soloLimpiarErrrorInput(array) {
  array.forEach((input) => {
    input.classList.remove("is-invalid");
    if (input.nextElementSibling) {
      input.nextElementSibling.remove();
    }
  });
}

//listar los tipos de documentos
async function menuTipoDocumento() {
  const response = await fetch(
    document.querySelector("#urlTipoComprobante").getAttribute("data-url")
  );
  const data = await response.json();

  let html = "";
  data.forEach((item) => {
    html += `<option value="${item.codigo}">${item.descripcion}</option>`;
  });
  document.querySelector("#inputSelectTipoDoc").innerHTML = html;
}

async function searchDocument(e) {
  e.preventDefault();
  const verMensage = document.querySelector("#verMensage");
  const url = e.target.action;
  const inputSelectTipoDoc = document.querySelector("#inputSelectTipoDoc");
  const inputFechaEmision = document.querySelector("#inputFechaEmision");
  const inputSerie = document.querySelector("#inputSerie");
  const inputCorrelativo = document.querySelector("#inputCorrelativo");
  const inputNumberDocumento = document.querySelector("#inputNumberDocumento");
  const inputTotal = document.querySelector("#inputTotal");

  soloLimpiarErrrorInput([
    inputFechaEmision,
    inputSerie,
    inputCorrelativo,
    inputNumberDocumento,
    inputTotal,
  ]);

  const data = new FormData();
  data.append("tipodoc", inputSelectTipoDoc.value);
  data.append("fecha_emision", inputFechaEmision.value);
  data.append("serie", inputSerie.value);
  data.append("correlativo", inputCorrelativo.value);
  data.append("documento_cliente", inputNumberDocumento.value);
  data.append("total", inputTotal.value);

  // enviar datos al servidor
  const res = await fetch(url, {
    method: "POST",
    body: data,
  });
  const respuesta = await res.json();
  if (respuesta.status) {
    // generar tabla
    generarTabla(respuesta.data);
  } else {
    if (respuesta.message) {
      let html = `<div class="alert alert-danger p-1" role="alert">
            <strong>¡Error!</strong> ${respuesta.message}.
            </div>`;
      verMensage.innerHTML = html;

      //   borrar despues de 20 segundos
      setTimeout(() => {
        verMensage.innerHTML = "";
      }, 20000);
    }

    mensajeErrorInput(inputSelectTipoDoc, respuesta.data.tipodoc);
    mensajeErrorInput(inputFechaEmision, respuesta.data.fecha_emision);
    mensajeErrorInput(inputSerie, respuesta.data.serie);
    mensajeErrorInput(inputCorrelativo, respuesta.data.correlativo);
    mensajeErrorInput(inputNumberDocumento, respuesta.data.documento_cliente);
    mensajeErrorInput(inputTotal, respuesta.data.total);
  }
}

//generar tabla
async function generarTabla(data) {
  const verMensage = document.querySelector("#verMensage");
  const urlDownloadXml = document
    .querySelector("#urlDownloadXml")
    .getAttribute("data-url");
  const urlDownloadCdr = document
    .querySelector("#urlDownloadCdr")
    .getAttribute("data-url");
  const urlReportePdf = document
    .querySelector("#urlReportePdf")
    .getAttribute("data-url");
  verMensage.innerHTML = "";

  let html = `<table class="table table-striped table-bordered table-hover table-sm">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Cliente</th>
            <th scope="col">Comprobante</th>
            <th scope="col">Total</th>
            <th scope="col">Descarga</th>
        </tr>
    </thead>
    <tbody>`;

  html += `<tr>
            <td>${data.documentocliente}</td>
            <td>${data.serie}-${data.correlativo}</td>
            <td>${data.total}</td>
            <td>
                <a href="${urlDownloadXml}?xml=${data.nombre_xml}" class="">XML</a>
                <a href="${urlReportePdf}?pdfA5=${data.id}" target="_blank class="">PDF</a>
                <a href="${urlDownloadCdr}?xml=${data.nombre_xml}" class="">CDR</a>
            </td>
        </tr>`;

  html += `</tbody>
    </table>`;
  verMensage.innerHTML = html;
}
