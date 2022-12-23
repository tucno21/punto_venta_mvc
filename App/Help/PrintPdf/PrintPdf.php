<?php

namespace App\Help\PrintPdf;

require DIR_APP . '/Library/phpqrcode/phpqrcode.php';

use DateTime;
use IntlDateFormatter;
use App\Library\FPDF\FPDF;
use App\Library\NumeroALetras\NumeroALetras;

class PrintPdf
{
    public function ModeloA5($emisor, $venta, $cliente)
    {
        $pdf = new FPDF('P', 'mm', 'A5');
        $pdf->AddPage();
        $pdf->setMargins(6, 10, 6);

        $pdf->Image(base_url('/assets/img/' . $emisor->logo), 5, 7, 19);

        //DATOS DE LA EMPRESA
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(20, 4, '', 0, 0, 'C');
        $pdf->MultiCell(52, 2, utf8_decode($emisor->razon_social), 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->Ln(1);
        $pdf->cell(24, 4, '', 0, 0, 'C');
        $pdf->MultiCell(52, 2, utf8_decode($emisor->descripcion), 0, 'L');
        $pdf->Ln(1);
        $pdf->cell(24, 4, '', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 5);
        $pdf->cell(30, 2, utf8_decode($emisor->direccion), 0, 'L', 1);
        $pdf->Ln(1);
        $pdf->cell(24, 4, '', 0, 0, 'C');
        $pdf->cell(10, 1, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 'L', 1);
        $pdf->Ln(1);
        $pdf->cell(24, 4, '', 0, 0, 'C');
        $pdf->cell(10, 2, 'Correo: ' . utf8_decode($emisor->email), 0, 'L', 1);
        $pdf->Ln(1);
        $pdf->cell(24, 4, '', 0, 0, 'C');
        $pdf->cell(10, 2, 'Celular: ' . utf8_decode($emisor->telefono), 0, 'L', 1);


        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(83, 8);
        $pdf->cell(60, 9, 'RUC: ' . $emisor->ruc, 'LRT', 1, 'C', 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(83, 16);
        if ($venta->tipodoc == "07" || $venta->tipodoc == "08" || $venta->tipodoc == "20" || $venta->tipodoc == "21") {
            $pdf->cell(60, 4, utf8_decode($venta->nombre_tipodoc), 'LR', 1, 'C', 0);
        } else {
            $pdf->cell(60, 4, utf8_decode($venta->nombre_tipodoc)  . ' ELECTRONICA', 'LR', 1, 'C', 0);
        }
        $pdf->SetXY(83, 20);
        $pdf->cell(60, 6, $venta->serie . '-' . $correlativo, 'BLR', 0, 'C', 0);

        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(0);
        $pdf->cell(25, 6, 'Cliente', 'LT', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->nombre), 'TR', 1, 'L', 0);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(25, 7, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->documento), 'R', 1, 'L', 0);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(25, 6, utf8_decode('Dirección'), 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->direccion), 'R', 1, 'L', 0);

        if ($venta->tipodoc == "07" || $venta->tipodoc == "08") {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(25, 6, utf8_decode('Doc. Afectado'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->cell(112, 6, ': ' . utf8_decode($venta->serie_ref . "-" . $venta->correlativo_ref), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(25, 6, utf8_decode('Tipo Nota'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->cell(112, 6, ': ' . utf8_decode($venta->motivo), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(25, 6, utf8_decode('Descripción'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->cell(112, 6, ': ' . utf8_decode($venta->descripcion), 'R', 1, 'L', 0);
        }
        // dd($venta->fecha_emision);
        if ($venta->tipodoc == "21") {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(25, 6, utf8_decode('Tiempo de Oferta'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            $pdf->cell(112, 6, ': ' . utf8_decode($venta->tiempo . " Días"), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(25, 6, utf8_decode('Fecha Vencimiento'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 8);
            //agregar $venta->fecha_emision los dias de tiempo de oferta
            $fecha_vencimiento = date('d-m-Y', strtotime($venta->fecha_emision . ' + ' . $venta->tiempo . ' days'));
            $pdf->cell(112, 6, ': ' . utf8_decode($fecha_vencimiento), 'R', 1, 'L', 0);
        }

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(25, 6, utf8_decode('Fecha Emisión'), 'LB', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);

        $fecha_letra =
            IntlDateFormatter::formatObject(
                new DateTime($venta->fecha_emision),
                // IntlDateFormatter::FULL,
                "eeee d MMMM 'de' y",
                // 'es_ES'
            );

        $pdf->cell(112, 6,  ': ' . utf8_decode($fecha_letra), 'RB', 1, 'L', 0);
        $pdf->Ln(1);

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(10, 5, 'ITEM', 1, 0, 'C', 0);
        $pdf->cell(10, 5, 'CANT', 1, 0, 'C', 0);
        $pdf->cell(81, 5, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 0);
        $pdf->cell(15, 5, 'V.U.', 1, 0, 'C', 0);
        $pdf->cell(21, 5, 'SUBTOTAL', 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 7);

        $productos = json_decode($venta->productos);
        $item = 1;
        foreach ($productos as $key => $value) {
            $pdf->cell(10, 5, $item, 1, 0, 'C', 0);
            $pdf->cell(10, 5, $value->cantidad, 1, 0, 'C', 0);
            $pdf->cell(81, 5, utf8_decode($value->detalle), 1, 0, 'L', 0);
            $pdf->cell(15, 5, $value->precio_unitario, 1, 0, 'R', 0);
            $pdf->cell(21, 5, $value->precio_unitario * $value->cantidad, 1, 1, 'R', 0);
            $item++;
        }
        $pdf->cell(116, 4, 'OP. EXONERADAS  S/', '', 0, 'R', 0);
        $pdf->cell(21, 4, $venta->op_exoneradas, 0, 1, 'R', 0);
        $pdf->cell(116, 4, 'OP. INAFECTAS  S/', '', 0, 'R', 0);
        $pdf->cell(21, 4, $venta->op_inafectas, 0, 1, 'R', 0);
        $pdf->cell(116, 4, 'OP. GRAVADAS  S/', '', 0, 'R', 0);
        $pdf->cell(21, 4, $venta->op_gravadas, 0, 1, 'R', 0);
        $pdf->cell(116, 4, 'IGV (18%)  S/', '', 0, 'R', 0);
        $pdf->cell(21, 4, $venta->igv_total, 0, 1, 'R', 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(116, 4, 'IMPORTE TOTAL  S/', '', 0, 'R', 0);
        $pdf->cell(21, 4, $venta->total, 1, 1, 'R', 0);

        $pdf->ln(6);

        $classnumeroLetra = new NumeroALetras();
        $numeroLetra = $classnumeroLetra->toInvoice($venta->total, 2, 'soles');

        //CODIGO QR
        //RUC|TIPO DOC|SERIE|CORRELATIVO|IGV|TOTAL|FECHA EMISION|TIPO DOC CLIENTE|NUMERO DOC CLI|
        $ruc = $emisor->ruc;
        $t_doc = $venta->tipodoc;
        $serie = $venta->serie;
        $correlativo = $venta->correlativo;
        $igv = $venta->igv_total;
        $total = $venta->total;
        $fecha = $venta->fecha_emision;
        $doc_cliente = $cliente->tipodoc;
        $n_cliente = $cliente->documento;

        $nombrexml = $ruc . '-' . $t_doc . '-' . $serie . '-' . $correlativo;
        $ruta_qr = DIR_IMG . $nombrexml . '.png';

        $texto_qr = $ruc . '|' . $t_doc . '|' . $serie . '|' . $correlativo . '|' . $igv . '|' . $total . '|' . $fecha . '|' . $doc_cliente . '|' . $n_cliente . '|';


        \QRcode::png($texto_qr, $ruta_qr, 'Q', 15, 0);

        $pdf->Image($ruta_qr, 115, $pdf->GetY(), 25, 25);
        // $pdf->Ln(-3);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(105, 6, utf8_decode('SON: ' . $numeroLetra), 'LRT', 1, 'L', 0);
        $pdf->SetFont('Arial', '', 7);

        if ($venta->forma_pago == 'Contado') {
            $pdf->cell(105, 4, utf8_decode('CONDICIÓN DE PAGO: Contado'), 'BLR', 1, 'L', 0);
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell(105, 4, utf8_decode('CONDICIÓN DE PAGO: Credito'), 'LR', 1, 'L', 0);
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell(105, 4, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 'LR', 1, 'L', 0);
            }
            $pdf->cell(105, 4, '', 'BLR', 1, 'L', 0);
        }

        $name_comprobante = strtolower($venta->nombre_tipodoc);

        $pdf->Ln(7);
        $pdf->cell(137, 0, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 0, 'L', 0);
        $pdf->Ln(4);
        $pdf->cell(137, 0, utf8_decode('Consultar en https://ww3.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm'), 0, 0, 'L', 0);

        if ($venta->estado == 0) {
            $pdf->Image(base_url('/assets/img/anulado.png'), 35, 70, 80);
        }

        $pdf->Output('I', $nombrexml . '.pdf');
        unlink($ruta_qr);
    }

    public function ModeloA4($emisor, $venta, $cliente)
    {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(6, 6, 6);

        $pdf->Image(base_url('/assets/img/' . $emisor->logo), 5, 5, 30);

        //DATOS DE LA EMPRESA
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->cell(25, 20, '', 0, 0, 'C');
        $pdf->cell(30, 1, '', 0, 'L', 1);
        $pdf->cell(30, 2, utf8_decode($emisor->razon_social), 0, 'L', 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(30, 7, utf8_decode($emisor->descripcion), 0, 'L', 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(30, 1, utf8_decode($emisor->direccion), 0, 'L', 1);
        $pdf->cell(10, 6, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 'L', 1);
        $pdf->cell(10, 1, 'Correo: ' . utf8_decode($emisor->email), 0, 'L', 1);
        $pdf->cell(10, 6, 'Celular: ' . utf8_decode($emisor->telefono), 0, 'L', 1);

        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(142, 8);
        $pdf->cell(60, 9, 'RUC: ' . $emisor->ruc, 'LRT', 1, 'C', 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(142, 16);
        if ($venta->tipodoc == "07" || $venta->tipodoc == "08" || $venta->tipodoc == "20"  || $venta->tipodoc == "21") {
            $pdf->cell(60, 4, utf8_decode($venta->nombre_tipodoc), 'LR', 1, 'C', 0);
        } else {
            $pdf->cell(60, 4, utf8_decode($venta->nombre_tipodoc)  . ' ELECTRONICA', 'LR', 1, 'C', 0);
        }
        $pdf->SetXY(142, 20);
        $pdf->cell(60, 6, $venta->serie . '-' . $correlativo, 'BLR', 0, 'C', 0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(15);
        $pdf->cell(35, 6, 'Cliente', 'LT', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . utf8_decode($cliente->nombre), 'TR', 1, 'L', 0);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(35, 7, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . utf8_decode($cliente->documento), 'R', 1, 'L', 0);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(35, 6, utf8_decode('Dirección'), 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . utf8_decode($cliente->direccion), 'R', 1, 'L', 0);

        if ($venta->tipodoc == "07" || $venta->tipodoc == "08") {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->cell(35, 6, utf8_decode('Doc. Afectado'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->cell(160, 6, ': ' . utf8_decode($venta->serie_ref . "-" . $venta->correlativo_ref), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->cell(35, 6, utf8_decode('Tipo Nota'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->cell(160, 6, ': ' . utf8_decode($venta->motivo), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->cell(35, 6, utf8_decode('Descripción'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->cell(160, 6, ': ' . utf8_decode($venta->descripcion), 'R', 1, 'L', 0);
        }
        if ($venta->tipodoc == "21") {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->cell(35, 6, utf8_decode('Tiempo de Oferta'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->cell(160, 6, ': ' . utf8_decode($venta->tiempo . " Días"), 'R', 1, 'L', 0);
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->cell(35, 6, utf8_decode('Fecha Vencimiento'), 'L', 0, 'L', 0);
            $pdf->SetFont('Arial', '', 9);
            $fecha_vencimiento = date('d-m-Y', strtotime($venta->fecha_emision . ' + ' . $venta->tiempo . ' days'));
            $pdf->cell(160, 6, ': ' . utf8_decode($fecha_vencimiento), 'R', 1, 'L', 0);
        }


        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(35, 6, utf8_decode('Fecha Emisión'), 'LB', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);

        $fecha_letra =
            IntlDateFormatter::formatObject(
                new DateTime($venta->fecha_emision),
                // IntlDateFormatter::FULL,
                "eeee d MMMM 'de' y",
                // 'es_ES'
            );

        $pdf->cell(160, 6,  ': ' . utf8_decode($fecha_letra), 'RB', 1, 'L', 0);

        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(10, 6, 'ITEM', 1, 0, 'C', 0);
        $pdf->cell(14, 6, 'CANT', 1, 0, 'C', 0);
        $pdf->cell(123, 6, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 0);
        $pdf->cell(22, 6, 'V.U.', 1, 0, 'C', 0);
        $pdf->cell(26, 6, 'SUBTOTAL', 1, 1, 'C', 0);

        $productos = json_decode($venta->productos);
        $item = 1;
        $pdf->SetFont('Arial', '', 9);
        foreach ($productos as $key => $value) {
            $pdf->cell(10, 6, $item, 1, 0, 'C', 0);
            $pdf->cell(14, 6, $value->cantidad, 1, 0, 'C', 0);
            $pdf->cell(123, 6, utf8_decode($value->detalle), 1, 0, 'L', 0);
            $pdf->cell(22, 6, $value->precio_unitario, 1, 0, 'R', 0);
            $pdf->cell(26, 6, $value->precio_unitario * $value->cantidad, 1, 1, 'R', 0);
            $item++;
        }

        $pdf->cell(169, 6, 'OP. EXONERADAS  S/', '', 0, 'R', 0);
        $pdf->cell(26, 6, $venta->op_exoneradas, 0, 1, 'R', 0);
        $pdf->cell(169, 6, 'OP. INAFECTAS  S/', '', 0, 'R', 0);
        $pdf->cell(26, 6, $venta->op_inafectas, 0, 1, 'R', 0);
        $pdf->cell(169, 6, 'OP. GRAVADAS  S/', '', 0, 'R', 0);
        $pdf->cell(26, 6, $venta->op_gravadas, 0, 1, 'R', 0);
        $pdf->cell(169, 6, 'IGV (18%)  S/', '', 0, 'R', 0);
        $pdf->cell(26, 6, $venta->igv_total, 0, 1, 'R', 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(169, 6, 'IMPORTE TOTAL  S/', '', 0, 'R', 0);
        $pdf->cell(26, 6, $venta->total, 1, 1, 'R', 0);

        $pdf->ln(6);

        $classnumeroLetra = new NumeroALetras();
        $numeroLetra = $classnumeroLetra->toInvoice($venta->total, 2, 'soles');

        //CODIGO QR
        //RUC|TIPO DOC|SERIE|CORRELATIVO|IGV|TOTAL|FECHA EMISION|TIPO DOC CLIENTE|NUMERO DOC CLI|
        $ruc = $emisor->ruc;
        $t_doc = $venta->tipodoc;
        $serie = $venta->serie;
        $correlativo = $venta->correlativo;
        $igv = $venta->igv_total;
        $total = $venta->total;
        $fecha = $venta->fecha_emision;
        $doc_cliente = $cliente->tipodoc;
        $n_cliente = $cliente->documento;

        $nombrexml = $ruc . '-' . $t_doc . '-' . $serie . '-' . $correlativo;
        $ruta_qr = DIR_IMG . $nombrexml . '.png';

        $texto_qr = $ruc . '|' . $t_doc . '|' . $serie . '|' . $correlativo . '|' . $igv . '|' . $total . '|' . $fecha . '|' . $doc_cliente . '|' . $n_cliente . '|';

        \QRcode::png($texto_qr, $ruta_qr, 'Q', 15, 0);

        $pdf->Image($ruta_qr, 170, $pdf->GetY(), 30, 30);
        // $pdf->Ln(-3);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell(160, 6, utf8_decode('SON: ' . $numeroLetra), 'LRT', 1, 'L', 0);
        $pdf->SetFont('Arial', '', 7);

        if ($venta->forma_pago == 'Contado') {
            $pdf->cell(160, 4, utf8_decode('CONDICIÓN DE PAGO: Contado'), 'BLR', 1, 'L', 0);
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell(160, 4, utf8_decode('CONDICIÓN DE PAGO: Credito'), 'LR', 1, 'L', 0);
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell(160, 4, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 'LR', 1, 'L', 0);
            }
            $pdf->cell(160, 4, '', 'BLR', 1, 'L', 0);
        }

        $name_comprobante = strtolower($venta->nombre_tipodoc);
        $pdf->Ln(7);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(137, 0, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 0, 'L', 0);
        $pdf->Ln(4);
        $pdf->cell(137, 0, utf8_decode('Consultar en https://ww3.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm'), 0, 0, 'L', 0);

        if ($venta->estado == 0) {
            $pdf->Image(base_url('/assets/img/anulado.png'), 45, 90, 90);
        }

        $pdf->Output('I', $nombrexml . '.pdf');
        unlink($ruta_qr);
    }

    public function ModeloTicket($emisor, $venta, $cliente)
    {
        // dd($venta);
        $ancho = 80;
        $pdf = new FPDF('P', 'mm', array($ancho, 250));
        $pdf->AddPage();
        // $pdf->SetFont('Arial', 'B', 8);    //Letra Arial, negrita (Bold), tam. 20

        $pdf->setY(4);
        $pdf->setX(3);
        $pdf->setMargins(3, 4, 3);
        // CABECERA
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->MultiCell(0, 4, utf8_decode($emisor->razon_social), 0, 'C');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->MultiCell(0, 4, utf8_decode($emisor->descripcion), 0, 'C');
        $pdf->Cell(0, 4, 'RUC: ' . $emisor->ruc, 0, 1, 'C');
        $pdf->Cell(0, 4, utf8_decode($emisor->direccion), 0, 1, 'C');
        $pdf->Cell(0, 4, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 1, 'C');
        $pdf->Cell(0, 4, 'Cel: ' . $emisor->telefono, 0, 1, 'C');
        $pdf->Ln(3);

        //CABECERA DE LA VENTA TIPO DE DOCUMENTO
        $pdf->SetFont('Helvetica', 'B', 9);
        if ($venta->tipodoc == "07" || $venta->tipodoc == "08" || $venta->tipodoc == "20") {
            $pdf->Cell(0, 4, utf8_decode($venta->nombre_tipodoc), 0, 1, 'C');
        } else {
            $pdf->Cell(0, 4, utf8_decode($venta->nombre_tipodoc) . ' ELECTRONICA', 0, 1, 'C');
        }
        $pdf->SetFont('Helvetica', '', 8);
        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->Cell(0, 4, $venta->serie . '-' . $correlativo, 0, 1, 'C');
        $newDate = date("d-m-Y", strtotime($venta->fecha_emision));
        $pdf->Cell(0, 4, $newDate, 0, 1, 'C');
        $pdf->Ln(5);


        //DATOS DEL CLIENTE
        $pdf->Cell(14, 4, 'Cliente', 0, 0, '');
        $pdf->Cell(0, 4, ': ' . utf8_decode($cliente->nombre), 0, 1, '');
        $pdf->Cell(14, 4, 'Dni', 0, 0, '');
        $pdf->Cell(0, 4, ': ' . $cliente->documento, 0, 1, '');
        $pdf->Cell(14, 4, utf8_decode('Dirección'), 0, 0, '');
        $pdf->MultiCell(0, 4, ': ' . utf8_decode($cliente->direccion), 0, '');
        $pdf->Ln(2);

        //CABECERA DE LA TABLA
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(0, 0, '', 'T', 1, 1); //BORDE DE ARRIBA
        $pdf->Cell(44, 5, utf8_decode('Descripción'), 0);
        $pdf->Cell(7, 5, 'Cant', 0, 0, 'R');
        $pdf->Cell(10, 5, 'Precio', 0, 0, 'R');
        $pdf->Cell(0, 5, 'Importe', 0, 1, 'R');
        $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO

        // DETALLE DE LA VENTA
        $pdf->SetFont('Helvetica', '', 7);
        $productos = json_decode($venta->productos);
        foreach ($productos as $key => $value) {
            $pdf->MultiCell(44, 4, utf8_decode($value->detalle), 0, 'L');
            $pdf->Cell(52, -4, $value->cantidad, 0, 0, 'R');
            $pdf->Cell(10, -4, number_format($value->precio_unitario, 2), 0, 0, 'R');
            $pdf->Cell(0, -4, number_format($value->precio_unitario * $value->cantidad, 2), 0, 1, 'R');
            $pdf->Ln(4);
        }
        $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO
        $pdf->Ln(2);

        // TOTALES
        $h = 4;
        if ($venta->op_gratuitas !== '0.00') {
            $pdf->Cell(58, $h, 'OP. GRATUITAS', 0);
            $pdf->Cell(0, $h, 'S/ ' . $venta->op_gratuitas, 0, 1, 'R');
        }
        if ($venta->op_exoneradas !== '0.00') {
            $pdf->Cell(58, $h, 'OP. EXONERADAS', 0);
            $pdf->Cell(0, $h, 'S/ ' . $venta->op_exoneradas, 0, 1, 'R');
        }
        if ($venta->op_inafectas !== '0.00') {
            $pdf->Cell(58, $h, 'OP. INAFECTAS', 0);
            $pdf->Cell(0, $h, 'S/ ' . $venta->op_inafectas, 0, 1, 'R');
        }
        if ($venta->op_gravadas !== '0.00') {
            $pdf->Cell(58, $h, 'OP. GRAVADAS', 0);
            $pdf->Cell(0, $h, 'S/ ' . $venta->op_gravadas, 0, 1, 'R');
        }
        $pdf->Cell(58, $h, 'IGV (18%)', 0);
        $pdf->Cell(0, $h, 'S/ ' . $venta->igv_total, 0, 1, 'R');
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(58, $h, 'TOTAL', 0);
        $pdf->Cell(0, $h, 'S/ ' . $venta->total, 0, 1, 'R');
        $pdf->Ln(2);

        //FORMA DE PAGO Y CANTIDAD EN LETRAS
        $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO
        $classnumeroLetra = new NumeroALetras();
        $numeroLetra = $classnumeroLetra->toInvoice($venta->total, 2, 'soles');
        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', '', 6);
        $pdf->Cell(0, 4, utf8_decode('SON: ' . $numeroLetra), 0, 1, 'C');
        if ($venta->forma_pago == 'Contado') {
            $pdf->cell(0, 4, utf8_decode('CONDICIÓN DE PAGO: Contado'), 0, 1, 'C');
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell(0, 4, utf8_decode('CONDICIÓN DE PAGO: Credito'), 0, 1, 'C');
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell(0, 4, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 0, 1, 'L');
            }
        }
        $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO
        $pdf->Ln(2);

        //CODIGO QR
        //RUC|TIPO DOC|SERIE|CORRELATIVO|IGV|TOTAL|FECHA EMISION|TIPO DOC CLIENTE|NUMERO DOC CLI|
        $ruc = $emisor->ruc;
        $t_doc = $venta->tipodoc;
        $serie = $venta->serie;
        $correlativo = $venta->correlativo;
        $igv = $venta->igv_total;
        $total = $venta->total;
        $fecha = $venta->fecha_emision;
        $doc_cliente = $cliente->tipodoc;
        $n_cliente = $cliente->documento;

        $nombrexml = $ruc . '-' . $t_doc . '-' . $serie . '-' . $correlativo;
        $ruta_qr = DIR_IMG . $nombrexml . '.png';

        $texto_qr = $ruc . '|' . $t_doc . '|' . $serie . '|' . $correlativo . '|' . $igv . '|' . $total . '|' . $fecha . '|' . $doc_cliente . '|' . $n_cliente . '|';

        \QRcode::png($texto_qr, $ruta_qr, 'Q', 15, 0);

        $longitud = 18;
        $centrar = ($ancho / 2) - ($longitud / 2);
        $pdf->Image($ruta_qr, $centrar, $pdf->GetY(), $longitud, $longitud);
        $pdf->Ln($longitud + 2);

        //PIE DE PAGINA
        $name_comprobante = strtolower($venta->nombre_tipodoc);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->MultiCell(0, 3, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 'C');
        $buscarComprobante = route('searchDocuments.index');
        $pdf->MultiCell(0, 3, utf8_decode("Consultar en: $buscarComprobante"), 0, 'C');

        $pdf->Output('I', $venta->nombre_xml . '.pdf');
        unlink($ruta_qr);
    }
}
