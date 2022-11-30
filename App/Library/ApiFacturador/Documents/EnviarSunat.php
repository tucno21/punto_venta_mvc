<?php

namespace App\Library\ApiFacturador\Documents;

use ZipArchive;
use DOMDocument;
use App\Model\FirmaDigitalModel;
use Catuva\Firmadigital\Signature;


class EnviarSunat
{
    //Funcion que me permite enviar a SUNAT los comprobantes: BV; FA; NC; ND por medio del metodo sendBill
    public function EnviarComprobanteElectronico($emisor, $nombreXML, $rutacertificado, $ruta_carpeta_xml, $ruta_archivo_cdr)
    {
        // var_dump($rutacertificado);
        // exit;
        //PASO 02: FIRMAR DIGITALMENTE - INICIO
        $objFirma = new Signature();
        $flg_firma = 0; //posicion donde se firma en el XML
        $ruta_archivo_xml = $ruta_carpeta_xml . $nombreXML . '.XML';
        $ruta_firma = $rutacertificado;

        $dbFirma = new FirmaDigitalModel;
        $passwordFirma = $dbFirma->where('id', 1)->first();
        // $pass_firma = 'carlos'; //contraseña del certificado
        $pass_firma = $passwordFirma->password_firma; //contraseña del certificado

        $objFirma->signature_xml($flg_firma, $ruta_archivo_xml, $ruta_firma, $pass_firma);
        // echo "</br>PASO 02: XML Firmado digitalmente";
        //PASO 02: FIRMAR DIGITALMENTE - FIN


        //PASO 03: COMPRIMIR EN ZIP - INICIO
        $zip = new ZipArchive();
        $ruta_zip = $ruta_carpeta_xml . $nombreXML . '.ZIP';
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
                        <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                        <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
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



        //PASO 06 - 09: RECIBIR LA RPTA DE SUNAT - INICIO
        if ($httpcode == 200) {

            $doc = new DOMDocument();
            $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;

                // echo "</br>PASO 06: Obtuve respuesta de SUNAT";

                $cdr = base64_decode($cdr); //decodifico el xml de respuesta

                // echo "</br>PASO 07: Respuesta de SUNAT decodificada";

                file_put_contents($ruta_archivo_cdr . "R-" . $nombre_zip, $cdr); //ZIP DE MEMORIA A DISCO LOCAL
                $zip = new ZipArchive();
                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombre_zip) == TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombreXML . '.XML');
                    $zip->close();
                    // echo "</br>PASO 08: ZIP copiado y extraido a disco";
                }
                // echo "</br>PASO 09: PROCESO TERMINADO";
            } else {
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                echo "</br> Ocurrio un error con código: " . $codigo . " Msje: " . $mensaje;
            }
        } else {
            echo curl_error($ch);
            echo "</br> Problema de conexión";
        }
        curl_close($ch);
        //PASO 06: RECIBIR LA RPTA DE SUNAT - FIN
        return true;
    }


    public function EnviarResumenComprobantes($emisor, $nombreXML, $rutacertificado, $ruta_carpeta_xml)
    {
        //PASO 02 FIRMAR XML DIGITALMENTE - INICIO
        $objFirma = new Signature();
        $flg_firma = 0; //posicion donde se firma en el XML
        $ruta_archivo_xml = $ruta_carpeta_xml . $nombreXML . '.XML';
        $ruta_firma = $rutacertificado;

        $dbFirma = new FirmaDigitalModel;
        $passwordFirma = $dbFirma->where('id', 1)->first();
        // $pass_firma = 'carlos'; //contraseña del certificado
        $pass_firma = $passwordFirma->password_firma; //contraseña del certificado

        $objFirma->signature_xml($flg_firma, $ruta_archivo_xml, $ruta_firma, $pass_firma);
        // echo "</br>PASO 02: XML Firmado digitalmente";
        //PASO 02 FIRMAR XML DIGITALMENTE - FIN

        //PASO 03: COMPRIMIR INICIO
        $zip = new ZipArchive();
        $ruta_zip = $ruta_carpeta_xml . $nombreXML . '.ZIP';
        if ($zip->open($ruta_zip, ZipArchive::CREATE) == TRUE) {
            $zip->addFile($ruta_archivo_xml, $nombreXML . '.XML');
            $zip->close();
        }
        // echo "</br>PASO 03: XML comprimido en formato .ZIP";
        //PASO 03: COMPRIMIR FIN

        //PASO 04: Codificar en base64 el .ZIP - INICIO
        $nombre_zip = $nombreXML . '.ZIP';
        $zip_base64_encode = base64_encode(file_get_contents($ruta_zip));
        // echo "</br>PASO 04: ZIP codificado en base64: "; // . $contenido_del_zip;
        //PASO 04: Codificar en base64 el .ZIP - FIN

        //PASO 05: ENVIO Y CONSUMO DE WEB SERVICE SUNAT -INICIO
        $link_sunat = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService"; //WS DE SUNAT BETA
        //$link_sunat = https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService //ws DE SUNAT PRODUCCION

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                    <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
            <ser:sendSummary>
                <fileName>' . $nombre_zip . '</fileName>
                <contentFile>' . $zip_base64_encode . '</contentFile>
            </ser:sendSummary>
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); //
        curl_setopt($ch, CURLOPT_URL, $link_sunat); //url a consultar
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/certificado/cacert.pem'); //windows, cuando estemos productivos comenta esta linea

        $response = curl_exec($ch); //Ejecuto y obtengo resultado
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // echo '</br> PASO 05: CONSUMO DE WS DE SUNAT';

        //PASO 06 - RPTA SUNAT - INICIO
        if ($httpcode == 200) // ok
        {
            $doc = new DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
                // echo '</br> PASO 06: OBTENEMOS EL NUMERO DE TICKET: ' . $ticket;
            } else {
                $codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                echo "</br> Ocurrio un error con código: " . $codigo . " Msje: " . $mensaje;
            }
        } else {
            echo curl_error($ch);
            echo "</br> Problema de conexión";
        }
        curl_close($ch);

        return $ticket;
        //PASO 06 - RPTA SUNAT - FIN
    }

    public function ConsultarTicket($emisor, $cabecera, $ticket, $ruta_archivo_cdr)
    {
        $link_sunat = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService"; //WS DE SUNAT BETA
        //$link_sunat = https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService //ws DE SUNAT PRODUCCION
        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_secundario'] . '</wsse:Username>
                    <wsse:Password>' . $emisor['clave_usuario_secundario'] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
            <ser:getStatus>
                <ticket>' . $ticket . '</ticket>
            </ser:getStatus>
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); //
        curl_setopt($ch, CURLOPT_URL, $link_sunat); //url a consultar
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/certificado/cacert.pem'); //windows, cuando estemos productivos comenta esta linea

        $response = curl_exec($ch); //Ejecuto y obtengo resultado
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $nombreXML = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];

        $nombre_zip = $nombreXML . '.ZIP';


        if ($httpcode == 200) //ok
        {
            $doc = new DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                $cdr = base64_decode($cdr);

                file_put_contents($ruta_archivo_cdr . 'R-' . $nombre_zip, $cdr);

                $zip = new ZipArchive();
                if ($zip->open($ruta_archivo_cdr . 'R-' . $nombre_zip) === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $nombreXML . '.XML');
                    // echo '</br> PASO 07: Extraer ZIP';
                    $zip->close();
                }
                // echo '</br> PASO 08: PROCESADO CORRECTAMENTE';
            } else {
                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
                echo '<br> OCURRIO UN ERROR CON CODIGO: ' . $codigo . 'Msje: ' . $mensaje;
            }
        } else //Problema de conexion
        {
            echo curl_error($ch);
            echo '</br> Problema de conexion';
        }

        curl_close($ch);
    }
}
