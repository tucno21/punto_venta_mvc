<?php

namespace App\Library\ApiFacturador;

use ZipArchive;
use DOMDocument;

class EnviarSunat
{
    private $ruta_carpeta_xml;
    private $ruta_carpeta_cdr;


    public function __construct()
    {
        $this->ruta_carpeta_xml = dirname(__FILE__) . '/files_factura/xml_files/';
        $this->ruta_carpeta_cdr = dirname(__FILE__) . '/files_factura/cdr_files/';
    }


    //Funcion que me permite enviar a SUNAT los comprobantes: BV; FA; NC; ND por medio del metodo sendBill
    public function enviarComprobante($emisor, $nombreXML)
    {
        // $nombreXML = '20000000001-01-F001-7';
        // $nombreXML = '10427691972-01-F001-7';

        $ruta_archivo_xml = $this->ruta_carpeta_xml . $nombreXML . '.XML';
        //comprobar si existe el arrichivo
        if (!file_exists($ruta_archivo_xml)) {
            return [
                'success' => false,
                'message' => "El archivo XML no existe",
            ];
        }

        //PASO 03: COMPRIMIR EN ZIP - INICIO
        $zip = new ZipArchive();
        $ruta_zip =  $this->ruta_carpeta_xml . $nombreXML . '.ZIP';
        if ($zip->open($ruta_zip, ZipArchive::CREATE) == TRUE) {
            $zip->addFile($ruta_archivo_xml, $nombreXML . '.XML');
            $zip->close();
        }
        // echo "</br>PASO 03: XML comprimido en formato .ZIP";
        //PASO 03: COMPRIMIR EN ZIP - FIN


        //PASO 04: Codificar en base64 el .ZIP - INICIO
        $nombre_zip = $nombreXML . '.ZIP';
        $zip_base64_encode = base64_encode(file_get_contents($ruta_zip));
        // echo "</br>PASO 04: ZIP codificado en base64: "; // . $contenido_del_zip;
        //PASO 04: Codificar en base64 el .ZIP - FIN


        //PASO 05: ENVIO Y RPTA DE SUNTA - WB - INICIO
        //WS BETA DE SUNAT
        $link_sunat = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
        //$link_sunat = https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService //ws DE SUNAT PRODUCCION

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <soapenv:Header>
                    <wsse:Security>
                        <wsse:UsernameToken>
                            <wsse:Username>' . $emisor->ruc . $emisor->usuario_secundario . '</wsse:Username>
                            <wsse:Password>' . $emisor->clave_usuario_secundario . '</wsse:Password>
                        </wsse:UsernameToken>
                    </wsse:Security>
                </soapenv:Header>
                <soapenv:Body>
                    <ser:sendBill>
                        <fileName>' . $nombre_zip . '</fileName>
                        <contentFile>' . $zip_base64_encode . '</contentFile>
                    </ser:sendBill>
                </soapenv:Body>
            </soapenv:Envelope>';

        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio)
        );

        $ch = curl_init(); //inicia la llamada
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); //verificar el ssl
        curl_setopt($ch, CURLOPT_URL, $link_sunat); //url a consultar
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //retornar el resultado
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY); //autenticacion
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //tiempo de espera o respuesta
        curl_setopt($ch, CURLOPT_POST, true); //metodo post
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio); //datos a enviar por metodo POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //datos de cabecera
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/certificado/cacert.pem'); //certificado
        //windows, cuando estemos productivos comenta esta linea

        $response = curl_exec($ch); //Ejecutar y obtiene respuesta
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //obtener el codigo de respuesta (200)

        // echo "</br>PASO 05: Envio del XML envelope a SUNAT: ";
        //PASO 05: ENVIO Y RPTA DE SUNTA - WB - FIN


        //eliminar $ruta_zip
        unlink($ruta_zip);

        //PASO 06 - 09: RECIBIR LA RPTA DE SUNAT - INICIO
        if ($httpcode == 200) {

            $doc = new DOMDocument();
            $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;

                // echo "</br>PASO 06: Obtuve respuesta de SUNAT";

                $cdr = base64_decode($cdr); //decodifico el xml de respuesta

                // echo "</br>PASO 07: Respuesta de SUNAT decodificada";

                file_put_contents($this->ruta_carpeta_cdr . "R-" . $nombre_zip, $cdr); //ZIP DE MEMORIA A DISCO LOCAL
                // $zip = new ZipArchive();
                // if ($zip->open($this->ruta_carpeta_cdr . 'R-' . $nombre_zip) == TRUE) {
                //     $zip->extractTo($this->ruta_carpeta_cdr, 'R-' . $nombreXML . '.XML');
                //     $zip->close();
                //     // echo "</br>PASO 08: ZIP copiado y extraido a disco";
                // }
                // echo "</br>PASO 09: PROCESO TERMINADO";

                return [
                    'success' => true,
                    'message' => 'Se envio correctamente el comprobante',
                ];
            } else {
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;

                return [
                    'success' => false,
                    'message' => 'Ocurrio un error con código: ' . $codigo . ' Msje: ' . $mensaje,
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Ocurrio un error con código: ' . $httpcode . ' Msje: ' . $response,
            ];
        }
        curl_close($ch);
    }
}
