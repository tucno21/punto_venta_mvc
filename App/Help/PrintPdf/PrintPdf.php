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
        $pdf->cell(60, 4, $venta->nombre_tipodoc . ' ELECTRONICA', 'LR', 1, 'C', 0);
        $pdf->SetXY(83, 20);
        $pdf->cell(60, 6, $venta->serie . '-' . $correlativo, 'BLR', 0, 'C', 0);



        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(0);
        $pdf->cell(25, 6, 'Cliente', 'LT', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->nombre), 'TR', 1, 'L', 0);
        $pdf->Ln(-1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(25, 7, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->documento), 'R', 1, 'L', 0);
        $pdf->Ln(-1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell(25, 6, utf8_decode('Dirección'), 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell(112, 6, ': ' . utf8_decode($cliente->direccion), 'R', 1, 'L', 0);
        $pdf->Ln(-1);
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

        $pdf->cell(112, 6,  ': ' . $fecha_letra, 'RB', 1, 'L', 0);
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
        $pdf->cell(60, 4, $venta->nombre_tipodoc . ' ELECTRONICA', 'LR', 1, 'C', 0);
        $pdf->SetXY(142, 20);
        $pdf->cell(60, 6, $venta->serie . '-' . $correlativo, 'BLR', 0, 'C', 0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(15);
        $pdf->cell(35, 6, 'Cliente', 'LT', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . $cliente->nombre, 'TR', 1, 'L', 0);
        $pdf->Ln(-1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(35, 7, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . $cliente->documento, 'R', 1, 'L', 0);
        $pdf->Ln(-1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(35, 6, utf8_decode('Dirección'), 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell(160, 6, ': ' . $cliente->direccion, 'R', 1, 'L', 0);
        $pdf->Ln(-1);
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

        $pdf->cell(160, 6,  ': ' . $fecha_letra, 'RB', 1, 'L', 0);

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
        $pdf = new FPDF('P', 'mm', array(80, 250));
        $pdf->AddPage();
        $pdf->setMargins(3, 2, 3);

        $pdf->SetFont('Helvetica', '', 11);
        $pdf->Cell(60, 4, utf8_decode($emisor->razon_social), 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->MultiCell(74, 4, utf8_decode($emisor->descripcion), 0, 'C');
        $pdf->Cell(74, 4, 'RUC: ' . $emisor->ruc, 0, 1, 'C');
        $pdf->Cell(74, 4, utf8_decode($emisor->direccion), 0, 1, 'C');
        $pdf->Cell(74, 4, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 1, 'C');
        $pdf->Cell(74, 4, 'Cel: ' . $emisor->telefono, 0, 1, 'C');
        $pdf->Cell(74, 4, $venta->nombre_tipodoc . ' ELECTRONICA', 0, 1, 'C');
        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->Cell(74, 4, $venta->serie . '-' . $correlativo, 0, 1, 'C');
        $newDate = date("d/m/Y", strtotime($venta->fecha_emision));
        $pdf->Cell(74, 4, $newDate, 0, 1, 'C');

        // DATOS FACTURA        
        $pdf->Ln(5);
        $pdf->Cell(14, 4, 'Cliente', 0, 0, '');
        $pdf->Cell(50, 4, ': ' . utf8_decode($cliente->nombre), 0, 1, '');
        $pdf->Cell(14, 4, 'Dni', 0, 0, '');
        $pdf->Cell(50, 4, ': ' . $cliente->documento, 0, 1, '');
        $pdf->Cell(14, 4, 'Direccion', 0, 0, '');
        $pdf->Cell(50, 4, ': ' . utf8_decode($cliente->direccion), 0, 1, '');

        $pdf->SetAutoPageBreak('auto', 2);
        $pdf->SetDisplayMode(75);

        // COLUMNAS
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(74, 0, '', 'T' . 0, 1);
        $pdf->Ln(-2);
        $pdf->Cell(44, 10, utf8_decode('Descripción'), 0);
        $pdf->Cell(5, 10, 'Cant', 0, 0, 'R');
        $pdf->Cell(10, 10, 'Precio', 0, 0, 'R');
        $pdf->Cell(15, 10, 'Importe', 0, 0, 'R');
        $pdf->Ln(8);
        $pdf->Cell(74, 0, '', 'T');
        $pdf->Ln(1);

        // DETALLE
        $productos = json_decode($venta->productos);
        $pdf->SetFont('Helvetica', '', 7);
        foreach ($productos as $key => $value) {
            $pdf->MultiCell(44, 4, utf8_decode($value->detalle), 0, 'L');
            $pdf->Cell(47, -5, $value->cantidad, 0, 0, 'R');
            $pdf->Cell(11, -5, $value->precio_unitario, 0, 0, 'R');
            $pdf->Cell(15, -5, $value->precio_unitario * $value->cantidad, 0, 1, 'R');
            $pdf->Ln(5);
        }
        $pdf->Ln(3);
        $pdf->Cell(74, 0, '', 'T' . 0, 1);
        // SUMATORIO DE LOS PRODUCTOS Y EL IVA
        $pdf->Ln(0);
        $pdf->Cell(38, 10, 'OP. EXONERADAS', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, 'S/ ' . $venta->op_exoneradas, 0, 0, 'R');
        $pdf->Ln(3);
        $pdf->Cell(38, 10, 'OP. INAFECTAS', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, 'S/ ' . $venta->op_inafectas, 0, 0, 'R');
        $pdf->Ln(3);
        $pdf->Cell(38, 10, 'OP. GRAVADAS', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, 'S/ ' . $venta->op_gravadas, 0, 0, 'R');
        $pdf->Ln(3);
        $pdf->Cell(38, 10, 'IGV (18%)', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, 'S/ ' . $venta->igv_total, 0, 0, 'R');
        $pdf->Ln(3);
        $pdf->Cell(38, 10, 'TOTAL', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, 'S/ ' . $venta->total, 0, 1, 'R');

        $classnumeroLetra = new NumeroALetras();
        $numeroLetra = $classnumeroLetra->toInvoice($venta->total, 2, 'soles');
        $pdf->Cell(74, 0, '', 'T' . 0, 1);
        $pdf->SetFont('Helvetica', '', 6);
        $pdf->Cell(74, 4, utf8_decode('SON: ' . $numeroLetra), 0, 1, 'C');

        if ($venta->forma_pago == 'Contado') {
            $pdf->cell(74, 4, utf8_decode('CONDICIÓN DE PAGO: Contado'), 0, 1, 'C');
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell(74, 4, utf8_decode('CONDICIÓN DE PAGO: Credito'), 0, 1, 'C');
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell(74, 4, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 0, 1, 'L');
            }
        }

        $pdf->Cell(74, 0, '', 'T' . 0, 1);

        $pdf->ln(2);
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

        $pdf->Image($ruta_qr, 32, $pdf->GetY(), 17, 17);
        // $pdf->Ln(-3);

        $name_comprobante = strtolower($venta->nombre_tipodoc);
        $pdf->Ln(19);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->Cell(74, 3, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 1, 'C');
        // $pdf->Cell(74, 3, utf8_decode('Consultar en https://ww3.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm'), 0, 1, 'C');
        // $pdf->Write(4, utf8_decode('Consultar en https://ww3.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm'));
        $pdf->MultiCell(74, 3, utf8_decode('Consultar en https://ww3.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm'), 0, 'C');
        // $pdf->Ln(4);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(74, 4, utf8_decode('Gracias por Compras'), 0, 1, 'C');

        if ($venta->estado == 0) {
            $pdf->Image(base_url('/assets/img/anulado.png'), 6, 90, 50);
        }

        $pdf->Output('I', $nombrexml . '.pdf');
        unlink($ruta_qr);
        // exit;
    }
}
