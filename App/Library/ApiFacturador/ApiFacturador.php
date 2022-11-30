<?php

namespace App\Library\ApiFacturador;

use App\Library\NumeroALetras\NumeroALetras;
use App\Library\ApiFacturador\Documents\EnviarSunat;
use App\Library\ApiFacturador\Documents\GeneradorXML;

class ApiFacturador
{
    private $generadorXML;
    private $numeroALetras;
    private $enviarSunat;
    private $ruta_carpeta_xml;
    private $ruta_carpeta_cdr;
    private $ruta_certificado;
    private $nombreXML;

    public function __construct()
    {
        $this->generadorXML = new GeneradorXML();
        $this->numeroALetras = new NumeroALetras();
        $this->enviarSunat = new EnviarSunat();
        $this->ruta_carpeta_xml = dirname(__FILE__) . '/files_factura/xml_files/';
        $this->ruta_carpeta_cdr = dirname(__FILE__) . '/files_factura/cdr_files/';
        $this->ruta_certificado = dirname(__FILE__) . '/Documents/certificado/certificado.pfx';
    }

    public function generarDocumentoElectronico($emisor, $cliente, $comprobante, $detalles, $cuotas = null)
    {
        $comprobante['total_texto'] = $this->numeroALetras->toInvoice($comprobante['total'], 2, 'soles');
        //RUC EMISOR - TIPO COMPROBANTE - SERIE - CORRELATIVO
        $this->nombreXML = $emisor['ruc'] . '-' . $comprobante['tipodoc'] . '-' . $comprobante['serie'] . '-' . $comprobante['correlativo'];

        $rutaXML = $this->ruta_carpeta_xml . $this->nombreXML;

        if ($comprobante["tipodoc"] == '01') { //factura
            $this->generadorXML->CrearXMLFactura($rutaXML, $emisor, $cliente, $comprobante, $detalles, $cuotas);
        } else if ($comprobante["tipodoc"] == '03') { //boleta
            $this->generadorXML->CrearXMLFactura($rutaXML, $emisor, $cliente, $comprobante, $detalles);
        } else if ($comprobante["tipodoc"] == '07') { //nota de credito
            $this->generadorXML->CrearXMLNotaCredito($rutaXML, $emisor, $cliente, $comprobante, $detalles, $cuotas);
        } else if ($comprobante["tipodoc"] == '08') { //nota de debito
            $this->generadorXML->CrearXMLNotaDebito($rutaXML, $emisor, $cliente, $comprobante, $detalles);
        }

        $result = $this->enviarSunat->EnviarComprobanteElectronico($emisor, $this->nombreXML, $this->ruta_certificado, $this->ruta_carpeta_xml, $this->ruta_carpeta_cdr);

        $this->deleteZIP();

        if ($result) {
            return $this->nombreXML;
        } else {
            return false;
        }
    }

    public function resumenDocumentos($emisor, $cabecera, $detalle)
    {
        $this->nombreXML = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];

        $rutaXML = $this->ruta_carpeta_xml . $this->nombreXML;

        $this->generadorXML->CrearXMLResumenDocumentos($emisor, $cabecera, $detalle, $rutaXML);

        $ticket = $this->enviarSunat->EnviarResumenComprobantes($emisor, $this->nombreXML, $this->ruta_certificado, $this->ruta_carpeta_xml);

        $result = $this->enviarSunat->ConsultarTicket($emisor, $cabecera, $ticket, $this->ruta_carpeta_cdr);

        $this->deleteZIP();

        if ($result) {
            return $this->nombreXML;
        } else {
            return false;
        }
    }

    public function bajaDocumentos($emisor, $cabecera, $detalle)
    {
        $this->nombreXML = $emisor['ruc'] . '-' . $cabecera['tipodoc'] . '-' . $cabecera['serie'] . '-' . $cabecera['correlativo'];

        $rutaXML = $this->ruta_carpeta_xml . $this->nombreXML;

        $this->generadorXML->CrearXmlBajaDocumentos($emisor, $cabecera, $detalle, $rutaXML);

        $ticket = $this->enviarSunat->EnviarResumenComprobantes($emisor, $this->nombreXML, $this->ruta_certificado, $this->ruta_carpeta_xml);

        $result = $this->enviarSunat->ConsultarTicket($emisor, $cabecera, $ticket, $this->ruta_carpeta_cdr);

        $this->deleteZIP();

        if ($result) {
            return $this->nombreXML;
        } else {
            return false;
        }
    }

    protected function deleteZIP()
    {
        $deleteZIPxml = dirname(__FILE__) . '/files_factura/xml_files/' . $this->nombreXML . '.ZIP';
        $deleteZIPcdr = dirname(__FILE__) . '/files_factura/cdr_files/R-' . $this->nombreXML . '.ZIP';

        if (file_exists($deleteZIPxml)) {
            unlink($deleteZIPxml);
            unlink($deleteZIPcdr);
        }
    }
}
