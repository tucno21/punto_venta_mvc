const btnXML = document.getElementById("btnXML");
const btnCDR = document.getElementById("btnCDR");
const urlXml = document.getElementById("urlXml").getAttribute("data-url");
const urlCdr = document.getElementById("urlCdr").getAttribute("data-url");

cargarEventListeners();
function cargarEventListeners() {
  document.addEventListener("DOMContentLoaded", () => {
    descargarxml();
    descargarcdr();
  });
}

async function descargarxml() {
  btnXML.addEventListener("click", () => {
    window.open(urlXml);
    // window.open(urlXml, "_blank");

    // const response = fetch(urlXml);
    // const data = response.json();
    // console.log(data);
  });
}

async function descargarcdr() {
  btnCDR.addEventListener("click", () => {
    window.open(urlCdr);
    // window.open(urlCdr, "_blank");
  });
}
