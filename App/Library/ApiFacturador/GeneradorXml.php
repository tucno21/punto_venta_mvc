<?php

namespace App\Library\ApiFacturador;

use Exception;
use DOMDocument;
use App\Model\FirmaDigital;
use Catuva\Firmadigital\Signature;
use App\Library\NumeroALetras\NumeroALetras;

class GeneradorXml
{
   private $numeroALetras;
   private $ruta_certificado;
   private $ruta_carpeta_xml;

   public function __construct()
   {
      $this->numeroALetras = new NumeroALetras();
      $this->ruta_carpeta_xml = dirname(__FILE__) . '/files_factura/xml_files/';
      $this->ruta_certificado = dirname(__FILE__) . '/certificado/certificado.pfx';

      set_error_handler(function ($errno, $errstr, $errfile, $errline) {
         throw new Exception($errstr, $errno);
      });
   }

   public function xml($emisor, $venta, $cliente)
   {
      $nombreXML = $emisor->ruc . '-' . $venta->tipodoc . '-' . $venta->serie . '-' . $venta->correlativo;

      $rutaXML = $this->ruta_carpeta_xml .  $nombreXML;

      $venta->total_texto = $this->numeroALetras->toInvoice($venta->total, 2, 'soles');


      if ($venta->tipodoc == '01' || $venta->tipodoc == '03') { //factura y boleta
         $result = $this->CrearXMLFactura($rutaXML, $emisor, $cliente, $venta);

         if ($result->success) {
            $result = $this->firmarXML($this->ruta_carpeta_xml, $nombreXML, $this->ruta_certificado);
            return $result;
         }
         return $result;
      }

      if ($venta->tipodoc == '07') { //nota de credito
         $result = $this->CrearXMLNotaCredito($rutaXML, $emisor, $cliente, $venta);
         if ($result->success) {
            $result = $this->firmarXML($this->ruta_carpeta_xml, $nombreXML, $this->ruta_certificado);
            return $result;
         }
         return $result;
      }

      if ($venta->tipodoc == '08') { //nota de debito
         $result = $this->CrearXMLNotaDevito($rutaXML, $emisor, $cliente, $venta);
         if ($result->success) {
            $result = $this->firmarXML($this->ruta_carpeta_xml, $nombreXML, $this->ruta_certificado);
            return $result;
         }
         return $result;
      }
   }

   public function firmarXML($ruta_carpeta_xml, $nombreXML, $ruta_certificado)
   {
      try {
         $objFirma = new Signature();
         $flg_firma = 0; //posicion donde se firma en el XML
         $ruta_archivo_xml = $ruta_carpeta_xml . $nombreXML . '.XML';

         $dbFirma = new FirmaDigital;
         // $dataFirmaDigital = $dbFirma->where('id', 1)->get();
         $dataFirmaDigital = $dbFirma->getFirma();
         // $pass_firma = 'carlos'; //contraseña del certificado
         $pass_firma = $dataFirmaDigital->password_firma; //contraseña del certificado

         $result = $objFirma->signature_xml($flg_firma, $ruta_archivo_xml, $ruta_certificado, $pass_firma);
         if ($result['respuesta'] == 'ok') {
            $return = ['success' => true, 'message' => 'Se firmo correctamente el XML'];
            return (object)$return;
         } else {
            $return = ['success' => false, 'message' => 'Error al firmar el XML'];
            return (object)$return;
         }
      } catch (Exception $th) {
         $mensaje = 'error al firmarXML, mensaje: ' . $th->getMessage() . ' del archivo: ' . $th->getFile();
         $return = ['success' => false, 'message' => $mensaje];
         return (object)$return;
      }
   }

   public function CrearXMLFactura($rutaXML, $emisor, $cliente, $venta)
   {
      try {
         $doc = new DOMDocument(); //clase que permite crear archivos, XML
         $doc->formatOutput = false; //NO ES DE SALIDA DE FORMATO TEXTO
         $doc->preserveWhiteSpace = true; //ACEPTE ESPACIOS
         $doc->encoding = 'utf-8'; //ACEPTE Ñ Y TILDES

         //crear el texto o cadena del XML para generar el documento electronico
         $xml = '<?xml version="1.0" encoding="UTF-8"?>
 <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
       <ext:UBLExtension>
          <ext:ExtensionContent />
       </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>' . $venta->serie . '-' . $venta->correlativo . '</cbc:ID>
    <cbc:IssueDate>' . $venta->fecha_emision . '</cbc:IssueDate>
    <cbc:IssueTime>00:00:00</cbc:IssueTime>
    <cbc:DueDate>' . $venta->fecha_emision . '</cbc:DueDate>
    <cbc:InvoiceTypeCode listID="0101">' . $venta->tipodoc . '</cbc:InvoiceTypeCode>
    <cbc:Note languageLocaleID="1000"><![CDATA[' . $venta->total_texto . ']]></cbc:Note>
    <cbc:DocumentCurrencyCode>' . $venta->moneda . '</cbc:DocumentCurrencyCode>
    <cac:Signature>
       <cbc:ID>' . $emisor->ruc . '</cbc:ID>
       <cbc:Note><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Note>
       <cac:SignatoryParty>
          <cac:PartyIdentification>
             <cbc:ID>' . $emisor->ruc . '</cbc:ID>
          </cac:PartyIdentification>
          <cac:PartyName>
             <cbc:Name><![CDATA[' . $emisor->razon_social . ']]></cbc:Name>
          </cac:PartyName>
       </cac:SignatoryParty>
       <cac:DigitalSignatureAttachment>
          <cac:ExternalReference>
             <cbc:URI>#SIGN-EMPRESA</cbc:URI>
          </cac:ExternalReference>
       </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
       <cac:Party>
          <cac:PartyIdentification>
             <cbc:ID schemeID="' . $emisor->tipodoc . '">' . $emisor->ruc . '</cbc:ID>
          </cac:PartyIdentification>
          <cac:PartyName>
             <cbc:Name><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Name>
          </cac:PartyName>
          <cac:PartyLegalEntity>
             <cbc:RegistrationName><![CDATA[' . $emisor->razon_social . ']]></cbc:RegistrationName>
             <cac:RegistrationAddress>
                <cbc:ID>' . $emisor->ubigeo . '</cbc:ID>
                <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                <cbc:CityName>' . $emisor->provincia . '</cbc:CityName>
                <cbc:CountrySubentity>' . $emisor->departamento . '</cbc:CountrySubentity>
                <cbc:District>' . $emisor->distrito . '</cbc:District>
                <cac:AddressLine>
                   <cbc:Line><![CDATA[' . $emisor->direccion . ']]></cbc:Line>
                </cac:AddressLine>
                <cac:Country>
                   <cbc:IdentificationCode>' . $emisor->pais . '</cbc:IdentificationCode>
                </cac:Country>
             </cac:RegistrationAddress>
          </cac:PartyLegalEntity>
       </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
       <cac:Party>
          <cac:PartyIdentification>
             <cbc:ID schemeID="' . $cliente->tipodoc . '">' . $cliente->documento . '</cbc:ID>
          </cac:PartyIdentification>
          <cac:PartyLegalEntity>
             <cbc:RegistrationName><![CDATA[' . $cliente->nombre . ']]></cbc:RegistrationName>
             <cac:RegistrationAddress>
                <cac:AddressLine>
                   <cbc:Line><![CDATA[' . $cliente->direccion . ']]></cbc:Line>
                </cac:AddressLine>
                <cac:Country>
                   <cbc:IdentificationCode>' . $cliente->pais . '</cbc:IdentificationCode>
                </cac:Country>
             </cac:RegistrationAddress>
          </cac:PartyLegalEntity>
       </cac:Party>
    </cac:AccountingCustomerParty>';

         if ($venta->tipodoc == '01') {
            if ($venta->forma_pago == 'Contado') {
               $xml .= '
    <cac:PaymentTerms>
       <cbc:ID>FormaPago</cbc:ID>
       <cbc:PaymentMeansID>' . $venta->forma_pago . '</cbc:PaymentMeansID>
    </cac:PaymentTerms>';
            } else {
               $xml .= '
    <cac:PaymentTerms>
       <cbc:ID>FormaPago</cbc:ID>
       <cbc:PaymentMeansID>' . $venta->forma_pago . '</cbc:PaymentMeansID>
       <cbc:Amount currencyID="PEN">' . $venta->total . '</cbc:Amount>
    </cac:PaymentTerms>';

               $cuotas = json_decode($venta->cuotas);
               foreach ($cuotas as $value) {
                  $xml .= '
    <cac:PaymentTerms>
       <cbc:ID>FormaPago</cbc:ID>
       <cbc:PaymentMeansID>' . $value->cuota . '</cbc:PaymentMeansID>
       <cbc:Amount currencyID="PEN">' . $value->monto . '</cbc:Amount>
       <cbc:PaymentDueDate>' . $value->fecha . '</cbc:PaymentDueDate>
    </cac:PaymentTerms>';
               }
            }
         }

         $xml .= '
    <cac:TaxTotal>
       <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_total . '</cbc:TaxAmount>';

         if ($venta->op_gravadas > 0) {
            $xml .= '
       <cac:TaxSubtotal><!-- GRABADAS -->
          <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gravadas . '</cbc:TaxableAmount>
          <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_grabada . '</cbc:TaxAmount>
          <cac:TaxCategory>
             <cac:TaxScheme>
                <cbc:ID>1000</cbc:ID>
                <cbc:Name>IGV</cbc:Name>
                <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
             </cac:TaxScheme>
          </cac:TaxCategory>
       </cac:TaxSubtotal>';
         }

         if ($venta->op_exoneradas > 0) {
            $xml .= '
       <cac:TaxSubtotal>
          <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_exoneradas . '</cbc:TaxableAmount>
          <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_exonerada . '</cbc:TaxAmount>
          <cac:TaxCategory>
             <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
             <cac:TaxScheme>
                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                <cbc:Name>EXO</cbc:Name>
                <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
             </cac:TaxScheme>
          </cac:TaxCategory>
       </cac:TaxSubtotal>';
         }

         if ($venta->op_inafectas > 0) {
            $xml .= '
       <cac:TaxSubtotal>
          <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_inafectas . '</cbc:TaxableAmount>
          <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_inafecta . '</cbc:TaxAmount>
          <cac:TaxCategory>
             <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
             <cac:TaxScheme>
                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                <cbc:Name>INA</cbc:Name>
                <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
             </cac:TaxScheme>
          </cac:TaxCategory>
       </cac:TaxSubtotal>';
         }

         if ($venta->op_gratuitas > 0) {
            $xml .= '
       <cac:TaxSubtotal>
          <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gratuitas . '</cbc:TaxableAmount>
          <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_gratuita . '</cbc:TaxAmount>
          <cac:TaxCategory>
             <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
             <cac:TaxScheme>
                <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                <cbc:Name>GRA</cbc:Name>
                <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
             </cac:TaxScheme>
          </cac:TaxCategory>
       </cac:TaxSubtotal>';
         }

         $total_antes_de_impuestos = $venta->op_gravadas + $venta->op_exoneradas + $venta->op_inafectas;

         $xml .= '
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
       <cbc:LineExtensionAmount currencyID="' . $venta->moneda . '">' . $total_antes_de_impuestos . '</cbc:LineExtensionAmount>
       <cbc:TaxInclusiveAmount currencyID="' . $venta->moneda . '">' . $venta->total . '</cbc:TaxInclusiveAmount>
       <cbc:PayableAmount currencyID="' . $venta->moneda . '">' . $venta->total . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';

         $productos = json_decode($venta->productos);
         foreach ($productos as $k => $v) {
            $xml .= '
    <cac:InvoiceLine>
       <cbc:ID>' . $v->item . '</cbc:ID><!-- DETALLES -->
       <cbc:InvoicedQuantity unitCode="' . $v->unidad . '">' . $v->cantidad . '</cbc:InvoicedQuantity>
       <cbc:LineExtensionAmount currencyID="' . $venta->moneda . '">' . $v->valor_total . '</cbc:LineExtensionAmount>
       <cac:PricingReference>
          <cac:AlternativeConditionPrice>
             <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->precio_unitario . '</cbc:PriceAmount>
             <cbc:PriceTypeCode>' . $v->tipo_precio . '</cbc:PriceTypeCode>
          </cac:AlternativeConditionPrice>
       </cac:PricingReference>
       <cac:TaxTotal>
          <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_1 . '</cbc:TaxAmount>
          <cac:TaxSubtotal>
             <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $v->valor_total . '</cbc:TaxableAmount>
             <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_2 . '</cbc:TaxAmount>
             <cac:TaxCategory>
                <cbc:Percent>' . $v->porcentaje_igv . '</cbc:Percent>
                <cbc:TaxExemptionReasonCode>' . $v->codigo_afectacion_alt . '</cbc:TaxExemptionReasonCode>
                <cac:TaxScheme>
                   <cbc:ID>' . $v->codigo_afectacion . '</cbc:ID>
                   <cbc:Name>' . $v->nombre_afectacion . '</cbc:Name>
                   <cbc:TaxTypeCode>' . $v->tipo_afectacion . '</cbc:TaxTypeCode>
                </cac:TaxScheme>
             </cac:TaxCategory>
          </cac:TaxSubtotal>
       </cac:TaxTotal>
       <cac:Item>
          <cbc:Description><![CDATA[' . $v->detalle . ']]></cbc:Description>
          <cac:SellersItemIdentification>
             <cbc:ID>' . $v->codigo . '</cbc:ID>
          </cac:SellersItemIdentification>
       </cac:Item>
       <cac:Price>
          <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->valor_unitario_final . '</cbc:PriceAmount>
       </cac:Price>
    </cac:InvoiceLine>';
         }

         $xml .= "
 </Invoice>";

         $doc->loadXML($xml);
         $doc->save($rutaXML . '.XML');

         $return = ['success' => true, 'message' => 'Se generó el XML correctamente'];
         return (object)$return;
      } catch (Exception $th) {
         $mensaje = 'error al generar CrearXMLFactura, mensaje: ' . $th->getMessage() . ' del archivo: ' . $th->getFile();
         $return = ['success' => false, 'message' => $mensaje];
         return (object)$return;
      }
   }

   public function CrearXMLNotaCredito($rutaXML, $emisor, $cliente, $venta)
   {
      try {
         $doc = new DOMDocument(); //clase que permite crear archivos, XML
         $doc->formatOutput = false; //NO ES DE SALIDA DE FORMATO TEXTO
         $doc->preserveWhiteSpace = true; //ACEPTE ESPACIOS
         $doc->encoding = 'utf-8'; //ACEPTE Ñ Y TILDES

         $xml = '<?xml version="1.0" encoding="UTF-8"?>
         <CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
            <ext:UBLExtensions>
               <ext:UBLExtension>
                  <ext:ExtensionContent />
               </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>2.0</cbc:CustomizationID>
            <cbc:ID>' . $venta->serie . '-' . $venta->correlativo . '</cbc:ID>
            <cbc:IssueDate>' . $venta->fecha_emision . '</cbc:IssueDate>
            <cbc:IssueTime>00:00:01</cbc:IssueTime>
            <cbc:Note languageLocaleID="1000"><![CDATA[' . $venta->total_texto . ']]></cbc:Note>
            <cbc:DocumentCurrencyCode>' . $venta->moneda . '</cbc:DocumentCurrencyCode>
            <cac:DiscrepancyResponse>
               <cbc:ReferenceID>' . $venta->serie_ref . '-' . $venta->correlativo_ref . '</cbc:ReferenceID>
               <cbc:ResponseCode>' . $venta->codmotivo . '</cbc:ResponseCode>
               <cbc:Description>' . $venta->descripcion . '</cbc:Description>
            </cac:DiscrepancyResponse>
            <cac:BillingReference>
               <cac:InvoiceDocumentReference>
                  <cbc:ID>' . $venta->serie_ref . '-' . $venta->correlativo_ref . '</cbc:ID>
                  <cbc:DocumentTypeCode>' . $venta->tipodoc_ref . '</cbc:DocumentTypeCode>
               </cac:InvoiceDocumentReference>
            </cac:BillingReference>
            <cac:Signature>
               <cbc:ID>' . $emisor->ruc  . '</cbc:ID>
               <cbc:Note><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Note>
               <cac:SignatoryParty>
                  <cac:PartyIdentification>
                     <cbc:ID>' . $emisor->ruc  . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                     <cbc:Name><![CDATA[' . $emisor->razon_social . ']]></cbc:Name>
                  </cac:PartyName>
               </cac:SignatoryParty>
               <cac:DigitalSignatureAttachment>
                  <cac:ExternalReference>
                     <cbc:URI>#SIGN-EMPRESA</cbc:URI>
                  </cac:ExternalReference>
               </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
               <cac:Party>
                  <cac:PartyIdentification>
                     <cbc:ID schemeID="' . $emisor->tipodoc . '">' . $emisor->ruc  . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                     <cbc:Name><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Name>
                  </cac:PartyName>
                  <cac:PartyLegalEntity>
                     <cbc:RegistrationName><![CDATA[' . $emisor->razon_social . ']]></cbc:RegistrationName>
                     <cac:RegistrationAddress>
                        <cbc:ID>' . $emisor->ubigeo  . '</cbc:ID>
                        <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                        <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                        <cbc:CityName>' . $emisor->provincia . '</cbc:CityName>
                        <cbc:CountrySubentity>' . $emisor->departamento . '</cbc:CountrySubentity>
                        <cbc:District>' . $emisor->distrito . '</cbc:District>
                        <cac:AddressLine>
                           <cbc:Line><![CDATA[' .  $emisor->direccion . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                           <cbc:IdentificationCode>' . $emisor->pais . '</cbc:IdentificationCode>
                        </cac:Country>
                     </cac:RegistrationAddress>
                  </cac:PartyLegalEntity>
               </cac:Party>
            </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
               <cac:Party>
                  <cac:PartyIdentification>
                  <cbc:ID schemeID="' . $cliente->tipodoc . '">' . $cliente->documento . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA[' . $cliente->nombre . ']]></cbc:RegistrationName>
                     <cac:RegistrationAddress>
                        <cac:AddressLine>
                        <cbc:Line><![CDATA[' . $cliente->direccion . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                        <cbc:IdentificationCode>' . $cliente->pais . '</cbc:IdentificationCode>
                        </cac:Country>
                     </cac:RegistrationAddress>
                  </cac:PartyLegalEntity>
               </cac:Party>
            </cac:AccountingCustomerParty>';

         $cuotas = json_decode($venta->cuotas);
         if ($cuotas != null && $venta->codmotivo == '13') {
            $xml .= '
            <cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>' . $venta->forma_pago . '</cbc:PaymentMeansID>
               <cbc:Amount currencyID="PEN">' . $venta->total . '</cbc:Amount>
            </cac:PaymentTerms>';

            foreach ($cuotas as $value) {
               $xml .= '
            <cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>' . $value->cuota . '</cbc:PaymentMeansID>
               <cbc:Amount currencyID="PEN">' . $value->monto . '</cbc:Amount>
               <cbc:PaymentDueDate>' . $value->fecha . '</cbc:PaymentDueDate>
            </cac:PaymentTerms>';
            }
         }

         $xml .= '
         <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_total . '</cbc:TaxAmount>';

         if ($venta->op_gravadas > 0) {
            $xml .= '
               <cac:TaxSubtotal><!-- GRABADAS -->
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gravadas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_grabada . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_exoneradas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_exoneradas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_exonerada . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_inafectas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_inafectas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_inafecta . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                        <cbc:Name>INA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_gratuitas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gratuitas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_gratuita . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                        <cbc:Name>GRA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }


         // $total_antes_de_impuestos = $venta->op_gravadas + $venta->op_exoneradas + $venta->op_inafectas;
         $xml .= '
            </cac:TaxTotal>
            <cac:LegalMonetaryTotal>
               <cbc:PayableAmount currencyID="' . $venta->moneda . '">' .  $venta->total . '</cbc:PayableAmount>
            </cac:LegalMonetaryTotal>';


         $productos = json_decode($venta->productos);

         foreach ($productos as $k => $v) {

            $xml .= '
            <cac:CreditNoteLine>
               <cbc:ID>' . $v->item . '</cbc:ID><!-- DETALLES -->
               <cbc:CreditedQuantity unitCode="' . $v->unidad . '">' . $v->cantidad . '</cbc:CreditedQuantity>
               <cbc:LineExtensionAmount currencyID="' . $venta->moneda . '">' . $v->valor_total . '</cbc:LineExtensionAmount>
               <cac:PricingReference>
                  <cac:AlternativeConditionPrice>
                     <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->precio_unitario . '</cbc:PriceAmount>
                     <cbc:PriceTypeCode>' . $v->tipo_precio . '</cbc:PriceTypeCode>
                  </cac:AlternativeConditionPrice>
               </cac:PricingReference>
               <cac:TaxTotal>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_1  . '</cbc:TaxAmount>
                  <cac:TaxSubtotal>
                     <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $v->valor_total  . '</cbc:TaxableAmount>
                     <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_2 . '</cbc:TaxAmount>
                     <cac:TaxCategory>
                        <cbc:Percent>' . $v->porcentaje_igv . '</cbc:Percent>
                        <cbc:TaxExemptionReasonCode>' . $v->codigo_afectacion_alt . '</cbc:TaxExemptionReasonCode>
                        <cac:TaxScheme>
                           <cbc:ID>' . $v->codigo_afectacion . '</cbc:ID>
                           <cbc:Name>' . $v->nombre_afectacion . '</cbc:Name>
                           <cbc:TaxTypeCode>' . $v->tipo_afectacion . '</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                     </cac:TaxCategory>
                  </cac:TaxSubtotal>
               </cac:TaxTotal>
               <cac:Item>
                  <cbc:Description><![CDATA[' . $v->detalle . ']]></cbc:Description>
                  <cac:SellersItemIdentification>
                    <cbc:ID>' . $v->codigo . '</cbc:ID>
                  </cac:SellersItemIdentification>
               </cac:Item>
               <cac:Price>
                  <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->valor_unitario_final  . '</cbc:PriceAmount>
               </cac:Price>
            </cac:CreditNoteLine>';
         }

         $xml .= '
         </CreditNote>';

         $dd = $doc->loadXML($xml);
         $doc->save($rutaXML . '.XML');
         $return = ['success' => true, 'message' => 'Se generó el XML correctamente'];
         return (object)$return;
      } catch (Exception $th) {
         $mensaje = 'error al generar CrearXMLFactura, mensaje: ' . $th->getMessage() . ' del archivo: ' . $th->getFile();
         $return = ['success' => false, 'message' => $mensaje];
         return (object)$return;
      }
   }
   public function CrearXMLNotaDevito($rutaXML, $emisor, $cliente, $venta)
   {
      try {
         $doc = new DOMDocument(); //clase que permite crear archivos, XML
         $doc->formatOutput = false; //NO ES DE SALIDA DE FORMATO TEXTO
         $doc->preserveWhiteSpace = true; //ACEPTE ESPACIOS
         $doc->encoding = 'utf-8'; //ACEPTE Ñ Y TILDES

         $xml = '<?xml version="1.0" encoding="UTF-8"?>
         <DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
            <ext:UBLExtensions>
               <ext:UBLExtension>
                  <ext:ExtensionContent />
               </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID>2.0</cbc:CustomizationID>
            <cbc:ID>' . $venta->serie . '-' . $venta->correlativo . '</cbc:ID>
            <cbc:IssueDate>' . $venta->fecha_emision . '</cbc:IssueDate>
            <cbc:IssueTime>00:00:01</cbc:IssueTime>
            <cbc:Note languageLocaleID="1000"><![CDATA[' . $venta->total_texto . ']]></cbc:Note>
            <cbc:DocumentCurrencyCode>' . $venta->moneda . '</cbc:DocumentCurrencyCode>
            <cac:DiscrepancyResponse>
               <cbc:ReferenceID>' . $venta->serie_ref . '-' . $venta->correlativo_ref . '</cbc:ReferenceID>
               <cbc:ResponseCode>' . $venta->codmotivo . '</cbc:ResponseCode>
               <cbc:Description>' . $venta->descripcion . '</cbc:Description>
            </cac:DiscrepancyResponse>
            <cac:BillingReference>
               <cac:InvoiceDocumentReference>
                  <cbc:ID>' . $venta->serie_ref . '-' . $venta->correlativo_ref . '</cbc:ID>
                  <cbc:DocumentTypeCode>' . $venta->tipodoc_ref . '</cbc:DocumentTypeCode>
               </cac:InvoiceDocumentReference>
            </cac:BillingReference>
            <cac:Signature>
               <cbc:ID>' . $emisor->ruc  . '</cbc:ID>
               <cbc:Note><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Note>
               <cac:SignatoryParty>
                  <cac:PartyIdentification>
                     <cbc:ID>' . $emisor->ruc  . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                     <cbc:Name><![CDATA[' . $emisor->razon_social . ']]></cbc:Name>
                  </cac:PartyName>
               </cac:SignatoryParty>
               <cac:DigitalSignatureAttachment>
                  <cac:ExternalReference>
                     <cbc:URI>#SIGN-EMPRESA</cbc:URI>
                  </cac:ExternalReference>
               </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
               <cac:Party>
                  <cac:PartyIdentification>
                     <cbc:ID schemeID="' . $emisor->tipodoc . '">' . $emisor->ruc  . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyName>
                     <cbc:Name><![CDATA[' . $emisor->nombre_comercial . ']]></cbc:Name>
                  </cac:PartyName>
                  <cac:PartyLegalEntity>
                     <cbc:RegistrationName><![CDATA[' . $emisor->razon_social . ']]></cbc:RegistrationName>
                     <cac:RegistrationAddress>
                        <cbc:ID>' . $emisor->ubigeo  . '</cbc:ID>
                        <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
                        <cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>
                        <cbc:CityName>' . $emisor->provincia . '</cbc:CityName>
                        <cbc:CountrySubentity>' . $emisor->departamento . '</cbc:CountrySubentity>
                        <cbc:District>' . $emisor->distrito . '</cbc:District>
                        <cac:AddressLine>
                           <cbc:Line><![CDATA[' .  $emisor->direccion . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                           <cbc:IdentificationCode>' . $emisor->pais . '</cbc:IdentificationCode>
                        </cac:Country>
                     </cac:RegistrationAddress>
                  </cac:PartyLegalEntity>
               </cac:Party>
            </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
               <cac:Party>
                  <cac:PartyIdentification>
                  <cbc:ID schemeID="' . $cliente->tipodoc . '">' . $cliente->documento . '</cbc:ID>
                  </cac:PartyIdentification>
                  <cac:PartyLegalEntity>
                  <cbc:RegistrationName><![CDATA[' . $cliente->nombre . ']]></cbc:RegistrationName>
                     <cac:RegistrationAddress>
                        <cac:AddressLine>
                        <cbc:Line><![CDATA[' . $cliente->direccion . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                        <cbc:IdentificationCode>' . $cliente->pais . '</cbc:IdentificationCode>
                        </cac:Country>
                     </cac:RegistrationAddress>
                  </cac:PartyLegalEntity>
               </cac:Party>
            </cac:AccountingCustomerParty>';

         $cuotas = json_decode($venta->cuotas);
         if ($cuotas != null && $venta->codmotivo == '13') {
            $xml .= '
            <cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>' . $venta->forma_pago . '</cbc:PaymentMeansID>
               <cbc:Amount currencyID="PEN">' . $venta->total . '</cbc:Amount>
            </cac:PaymentTerms>';

            foreach ($cuotas as $value) {
               $xml .= '
            <cac:PaymentTerms>
               <cbc:ID>FormaPago</cbc:ID>
               <cbc:PaymentMeansID>' . $value->cuota . '</cbc:PaymentMeansID>
               <cbc:Amount currencyID="PEN">' . $value->monto . '</cbc:Amount>
               <cbc:PaymentDueDate>' . $value->fecha . '</cbc:PaymentDueDate>
            </cac:PaymentTerms>';
            }
         }

         $xml .= '
         <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_total . '</cbc:TaxAmount>';

         if ($venta->op_gravadas > 0) {
            $xml .= '
               <cac:TaxSubtotal><!-- GRABADAS -->
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gravadas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_grabada . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_exoneradas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_exoneradas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_exonerada . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_inafectas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_inafectas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_inafecta . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                        <cbc:Name>INA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }

         if ($venta->op_gratuitas > 0) {
            $xml .= '
               <cac:TaxSubtotal>
                  <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $venta->op_gratuitas . '</cbc:TaxableAmount>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $venta->igv_gratuita . '</cbc:TaxAmount>
                  <cac:TaxCategory>
                     <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                     <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9996</cbc:ID>
                        <cbc:Name>GRA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                     </cac:TaxScheme>
                  </cac:TaxCategory>
               </cac:TaxSubtotal>';
         }


         // $total_antes_de_impuestos = $venta->op_gravadas + $venta->op_exoneradas + $venta->op_inafectas;
         $xml .= '
            </cac:TaxTotal>
            <cac:RequestedMonetaryTotal>
               <cbc:PayableAmount currencyID="' . $venta->moneda . '">' .  $venta->total . '</cbc:PayableAmount>
            </cac:RequestedMonetaryTotal>';


         $productos = json_decode($venta->productos);

         foreach ($productos as $k => $v) {

            $xml .= '
            <cac:DebitNoteLine>
               <cbc:ID>' . $v->item . '</cbc:ID><!-- DETALLES -->
               <cbc:DebitedQuantity unitCode="' . $v->unidad . '">' . $v->cantidad . '</cbc:DebitedQuantity>
               <cbc:LineExtensionAmount currencyID="' . $venta->moneda . '">' . $v->valor_total . '</cbc:LineExtensionAmount>
               <cac:PricingReference>
                  <cac:AlternativeConditionPrice>
                     <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->precio_unitario . '</cbc:PriceAmount>
                     <cbc:PriceTypeCode>' . $v->tipo_precio . '</cbc:PriceTypeCode>
                  </cac:AlternativeConditionPrice>
               </cac:PricingReference>
               <cac:TaxTotal>
                  <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_1  . '</cbc:TaxAmount>
                  <cac:TaxSubtotal>
                     <cbc:TaxableAmount currencyID="' . $venta->moneda . '">' . $v->valor_total  . '</cbc:TaxableAmount>
                     <cbc:TaxAmount currencyID="' . $venta->moneda . '">' . $v->igv_2 . '</cbc:TaxAmount>
                     <cac:TaxCategory>
                        <cbc:Percent>' . $v->porcentaje_igv . '</cbc:Percent>
                        <cbc:TaxExemptionReasonCode>' . $v->codigo_afectacion_alt . '</cbc:TaxExemptionReasonCode>
                        <cac:TaxScheme>
                           <cbc:ID>' . $v->codigo_afectacion . '</cbc:ID>
                           <cbc:Name>' . $v->nombre_afectacion . '</cbc:Name>
                           <cbc:TaxTypeCode>' . $v->tipo_afectacion . '</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                     </cac:TaxCategory>
                  </cac:TaxSubtotal>
               </cac:TaxTotal>
               <cac:Item>
                  <cbc:Description><![CDATA[' . $v->detalle . ']]></cbc:Description>
                  <cac:SellersItemIdentification>
                    <cbc:ID>' . $v->codigo . '</cbc:ID>
                  </cac:SellersItemIdentification>
               </cac:Item>
               <cac:Price>
                  <cbc:PriceAmount currencyID="' . $venta->moneda . '">' . $v->valor_unitario_final  . '</cbc:PriceAmount>
               </cac:Price>
            </cac:DebitNoteLine>';
         }

         $xml .= '
         </DebitNote>';

         $dd = $doc->loadXML($xml);
         $doc->save($rutaXML . '.XML');
         $return = ['success' => true, 'message' => 'Se generó el XML correctamente'];
         return (object)$return;
      } catch (Exception $th) {
         $mensaje = 'error al generar CrearXMLFactura, mensaje: ' . $th->getMessage() . ' del archivo: ' . $th->getFile();
         $return = ['success' => false, 'message' => $mensaje];
         return (object)$return;
      }
   }
}
