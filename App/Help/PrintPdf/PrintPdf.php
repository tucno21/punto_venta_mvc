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
        $margenLaterales = 10;
        $margenSuperior = 8;
        $pdf->setX($margenLaterales);
        $pdf->setY($margenSuperior);
        // $pdf->setMargins($margenLaterales, $margenSuperior, $margenLaterales);
        // $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO
        $logo = base_url('/assets/img/' . $emisor->logo);
        $pdf->Image($logo, $margenLaterales, $margenSuperior, 23);


        //DATOS DE LA EMPRESA
        $h = 3;
        $x = 24;
        $sobra = 148 - $x - $margenLaterales * 2;
        // dd($sobra);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell($x, $h, '', 0);
        $pdf->MultiCell(56, $h + 1, utf8_decode($emisor->razon_social), 0, 'L');
        $pdf->SetFont('Arial', '', 6);
        $pdf->cell($x, $h, '', 0);
        $pdf->MultiCell(56, $h, utf8_decode($emisor->descripcion), 0, 'L');
        // $pdf->SetFont('Arial', '', 5);
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, utf8_decode($emisor->direccion), 0, 1, 'L');
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 1, 'L');
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, 'Celular: ' . utf8_decode($emisor->telefono) . ' / Correo: ' . utf8_decode($emisor->email), 0,  1, 'L');
        // $pdf->cell($x, $h, '', 0, 0, 'C');
        // $pdf->cell(104, $h, 'Celular: ' . utf8_decode($emisor->telefono), 0, 1, 'L');

        //CUADRO DE DATOS DE LA FACTURA
        $h = 6;
        $xy = 90;
        $x = 48;
        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($xy, $margenSuperior);
        $pdf->cell($x, $h, 'RUC: ' . $emisor->ruc, 'LTR', 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($xy, $margenSuperior + $h);
        if ($venta->tipodoc == "07" || $venta->tipodoc == "08" || $venta->tipodoc == "20" || $venta->tipodoc == "21") {
            $pdf->cell($x, $h, utf8_decode($venta->nombre_tipodoc), 'LR', 1, 'C');
        } else {
            $pdf->cell($x, $h, utf8_decode($venta->nombre_tipodoc)  . ' ELECTRONICA', 'LR', 1, 'C');
        }
        $pdf->SetXY($xy, $margenSuperior + $h + $h);
        $pdf->cell($x, $h, $venta->serie . '-' . $correlativo, 'LBR', 1, 'C');
        $pdf->Ln(8);

        //DATOS DEL CLIENTE
        $x = 25;
        $h = 4;
        $fontZise = 8;
        $sobra = 148 - $x - $margenLaterales * 2;
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, 'Cliente', 'LT', 0, 'L');
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->nombre), 'TR', 1, 'L');
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->documento), 'R', 1, 'L');
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, utf8_decode('Dirección'), 'L', 0, 'L');
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->direccion), 'R', 1, 'L');

        if ($venta->tipodoc == "07" || $venta->tipodoc == "08") {
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Doc. Afectado'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->serie_ref . "-" . $venta->correlativo_ref), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Tipo Nota'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->motivo), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Descripción'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->descripcion), 'R', 1, 'L');
        }
        if ($venta->tipodoc == "21") {
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Tiempo de Oferta'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h,  ': ' . utf8_decode($venta->tiempo . " Días"), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Fecha Vencimiento'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            //agregar $venta->fecha_emision los dias de tiempo de oferta
            $fecha_vencimiento = date('d-m-Y', strtotime($venta->fecha_emision . ' + ' . $venta->tiempo . ' days'));
            $pdf->cell($sobra, $h, ': ' . utf8_decode($fecha_vencimiento), 'R', 1, 'L');
        }

        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, utf8_decode('Fecha Emisión'), 'LB', 0, 'L');
        $fecha_letra =
            IntlDateFormatter::formatObject(
                new DateTime($venta->fecha_emision),
                // IntlDateFormatter::FULL,
                "eeee d MMMM 'de' y",
                // 'es_ES'
            );
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h,  ': ' . utf8_decode($fecha_letra), 'RB', 1, 'L');
        $pdf->Ln(3);

        //CABECERA DE LA TABLA
        $x = 10;
        $y = 5;
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell($x, $y, 'ITEM', 1, 0, 'C');
        $pdf->cell($x, $y, 'CANT', 1, 0, 'C');
        $pdf->cell($x + 60, $y, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C');
        $pdf->cell($x + 5, $y, 'V.U.', 1, 0, 'C', 0);
        $pdf->cell(0, $y, 'SUBTOTAL', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 7);
        $i = 1;
        $productos = json_decode($venta->productos);
        foreach ($productos as $producto) {
            $pdf->cell($x, $y, $i, 1, 0, 'C');
            $pdf->cell($x, $y, $producto->cantidad, 1, 0, 'C');
            $pdf->cell($x + 60, $y, utf8_decode($producto->detalle), 1, 0, 'L');
            $pdf->cell($x + 5, $y, number_format($producto->precio_unitario, 2), 1, 0, 'R');
            if ($producto->nombre_afectacion == 'GRA') {
                $pdf->cell(0, $y, number_format(0, 2), 1, 1, 'R');
            } else {
                $pdf->cell(0, $y, number_format($producto->precio_unitario * $producto->cantidad, 2), 1, 1, 'R');
            }
            $i++;
        }
        $pdf->Ln(2);

        //TOTAL
        $x = 105;
        $y = 4;
        if ($venta->op_gratuitas !== '0.00') {
            $pdf->cell($x, $y, 'OP. GRATUITAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_gratuitas, 0, 1, 'R', 0);
        }
        if ($venta->op_exoneradas !== '0.00') {
            $pdf->cell($x, $y, 'OP. EXONERADAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_exoneradas, 0, 1, 'R', 0);
        }
        if ($venta->op_inafectas !== '0.00') {
            $pdf->cell($x, $y, 'OP. INAFECTAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_inafectas, 0, 1, 'R', 0);
        }
        if ($venta->op_gravadas !== '0.00') {
            $pdf->cell($x, $y, 'OP. GRAVADAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_gravadas, 0, 1, 'R', 0);
        }
        $pdf->cell($x, $y, 'IGV (18%)  S/', 0, 0, 'R', 0);
        $pdf->cell(0, $y, $venta->igv_total, 0, 1, 'R', 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell($x, $y, 'IMPORTE TOTAL  S/', 0, 0, 'R', 0);
        $pdf->cell(0, $y, $venta->total, 0, 1, 'R', 0);
        $pdf->ln(6);

        //CODIGO QR
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

        $x = 95;
        $y = 5;
        $pdf->Image($ruta_qr, $x + 15, $pdf->GetY(), 25, 25);
        // $pdf->Ln(-3);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->cell($x, $y, utf8_decode('SON: ' . $numeroLetra), 'LRT', 1, 'L');
        $pdf->SetFont('Arial', '', 7);

        if ($venta->forma_pago == 'Contado') {
            $pdf->cell($x, $y, utf8_decode('CONDICIÓN DE PAGO: Contado'), 'BLR', 1, 'L');
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell($x, $y, utf8_decode('CONDICIÓN DE PAGO: Credito'), 'LR', 1, 'L');
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell($x, $y, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 'LR', 1, 'L');
            }
            $pdf->cell($x, $y, '', 'BLR', 1, 'L');
        }

        $name_comprobante = strtolower($venta->nombre_tipodoc);

        $pdf->Ln(7);
        $pdf->cell(0, $y, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 1, 'L', 0);
        $buscarComprobante = route('searchDocuments.index');
        $pdf->cell(0, $y, utf8_decode("Consultar en : $buscarComprobante"), 0, 1, 'L', 0);

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
        $margenLaterales = 10;
        $margenSuperior = 8;
        $pdf->setX($margenLaterales);
        $pdf->setY($margenSuperior);
        // $pdf->setMargins($margenLaterales, $margenSuperior, $margenLaterales);
        // $pdf->Cell(0, 0, '', 'T', 1, 1); // BORDE DE ABAJO
        $logo = base_url('/assets/img/' . $emisor->logo);
        $pdf->Image($logo, $margenLaterales, $margenSuperior, 29);


        //DATOS DE LA EMPRESA
        $h = 4;
        $x = 30;
        $sobra = 210 - $x - $margenLaterales * 2;
        // dd($sobra);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->cell($x, $h, '', 0);
        $pdf->MultiCell(100, $h + 1, utf8_decode($emisor->razon_social), 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->cell($x, $h, '', 0);
        $pdf->MultiCell(100, $h, utf8_decode($emisor->descripcion), 0, 'L');
        // $pdf->SetFont('Arial', '', 5);
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, utf8_decode($emisor->direccion), 0, 1, 'L');
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, utf8_decode($emisor->departamento . ' - ' . $emisor->provincia . ' - ' . $emisor->distrito), 0, 1, 'L');
        $pdf->cell($x, $h, '', 0);
        $pdf->cell($sobra, $h, 'Celular: ' . utf8_decode($emisor->telefono) . ' / Correo: ' . utf8_decode($emisor->email), 0,  1, 'L');
        // $pdf->cell($x, $h, '', 0, 0, 'C');
        // $pdf->cell(104, $h, 'Celular: ' . utf8_decode($emisor->telefono), 0, 1, 'L');

        //CUADRO DE DATOS DE LA FACTURA
        $h = 7;
        $xy = 140;
        $x = 56;
        $number = $venta->correlativo;
        $length = 8;
        $correlativo = substr(str_repeat(0, $length) . $number, -$length);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($xy, $margenSuperior);
        $pdf->cell($x, $h, 'RUC: ' . $emisor->ruc, 'LTR', 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($xy, $margenSuperior + $h);
        if ($venta->tipodoc == "07" || $venta->tipodoc == "08" || $venta->tipodoc == "20" || $venta->tipodoc == "21") {
            $pdf->cell($x, $h, utf8_decode($venta->nombre_tipodoc), 'LR', 1, 'C');
        } else {
            $pdf->cell($x, $h, utf8_decode($venta->nombre_tipodoc)  . ' ELECTRONICA', 'LR', 1, 'C');
        }
        $pdf->SetXY($xy, $margenSuperior + $h + $h);
        $pdf->cell($x, $h, $venta->serie . '-' . $correlativo, 'LBR', 1, 'C');
        $pdf->Ln(8);

        //DATOS DEL CLIENTE
        $x = 28;
        $h = 5;
        $fontZise = 9;
        $sobra = 210 - $x - $margenLaterales * 2;
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, 'Cliente', 'LT', 0, 'L');
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->nombre), 'TR', 1, 'L');
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, 'RUC/DNI', 'L', 0, 'L', 0);
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->documento), 'R', 1, 'L');
        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, utf8_decode('Dirección'), 'L', 0, 'L');
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h, ': ' . utf8_decode($cliente->direccion), 'R', 1, 'L');

        if ($venta->tipodoc == "07" || $venta->tipodoc == "08") {
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Doc. Afectado'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->serie_ref . "-" . $venta->correlativo_ref), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Tipo Nota'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->motivo), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Descripción'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h, ': ' . utf8_decode($venta->descripcion), 'R', 1, 'L');
        }
        if ($venta->tipodoc == "21") {
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Tiempo de Oferta'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            $pdf->cell($sobra, $h,  ': ' . utf8_decode($venta->tiempo . " Días"), 'R', 1, 'L');
            $pdf->SetFont('Arial', 'B', $fontZise);
            $pdf->cell($x, $h, utf8_decode('Fecha Vencimiento'), 'L', 0, 'L');
            $pdf->SetFont('Arial', '', $fontZise);
            //agregar $venta->fecha_emision los dias de tiempo de oferta
            $fecha_vencimiento = date('d-m-Y', strtotime($venta->fecha_emision . ' + ' . $venta->tiempo . ' days'));
            $pdf->cell($sobra, $h, ': ' . utf8_decode($fecha_vencimiento), 'R', 1, 'L');
        }

        $pdf->SetFont('Arial', 'B', $fontZise);
        $pdf->cell($x, $h, utf8_decode('Fecha Emisión'), 'LB', 0, 'L');
        $fecha_letra =
            IntlDateFormatter::formatObject(
                new DateTime($venta->fecha_emision),
                // IntlDateFormatter::FULL,
                "eeee d MMMM 'de' y",
                // 'es_ES'
            );
        $pdf->SetFont('Arial', '', $fontZise);
        $pdf->cell($sobra, $h,  ': ' . utf8_decode($fecha_letra), 'RB', 1, 'L');
        $pdf->Ln(4);

        //CABECERA DE LA TABLA
        $x = 10;
        $y = 5;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell($x, $y, 'ITEM', 1, 0, 'C');
        $pdf->cell($x, $y, 'CANT', 1, 0, 'C');
        $pdf->cell($x + 120, $y, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C');
        $pdf->cell($x + 5, $y, 'V.U.', 1, 0, 'C', 0);
        $pdf->cell(0, $y, 'SUBTOTAL', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $i = 1;
        $productos = json_decode($venta->productos);
        foreach ($productos as $producto) {
            $pdf->cell($x, $y, $i, 1, 0, 'C');
            $pdf->cell($x, $y, $producto->cantidad, 1, 0, 'C');
            $pdf->cell($x + 120, $y, utf8_decode($producto->detalle), 1, 0, 'L');
            $pdf->cell($x + 5, $y, number_format($producto->precio_unitario, 2), 1, 0, 'R');
            if ($producto->nombre_afectacion == 'GRA') {
                $pdf->cell(0, $y, number_format(0, 2), 1, 1, 'R');
            } else {
                $pdf->cell(0, $y, number_format($producto->precio_unitario * $producto->cantidad, 2), 1, 1, 'R');
            }
            $i++;
        }
        $pdf->Ln(2);

        //TOTAL
        $x = 165;
        $y = 4;
        $pdf->SetFont('Arial', '', 9);
        if ($venta->op_gratuitas !== '0.00') {
            $pdf->cell($x, $y, 'OP. GRATUITAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_gratuitas, 0, 1, 'R', 0);
        }
        if ($venta->op_exoneradas !== '0.00') {
            $pdf->cell($x, $y, 'OP. EXONERADAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_exoneradas, 0, 1, 'R', 0);
        }
        if ($venta->op_inafectas !== '0.00') {
            $pdf->cell($x, $y, 'OP. INAFECTAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_inafectas, 0, 1, 'R', 0);
        }
        if ($venta->op_gravadas !== '0.00') {
            $pdf->cell($x, $y, 'OP. GRAVADAS  S/', 0, 0, 'R', 0);
            $pdf->cell(0, $y, $venta->op_gravadas, 0, 1, 'R', 0);
        }
        $pdf->cell($x, $y, 'IGV (18%)  S/', 0, 0, 'R', 0);
        $pdf->cell(0, $y, $venta->igv_total, 0, 1, 'R', 0);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell($x, $y, 'IMPORTE TOTAL  S/', 0, 0, 'R', 0);
        $pdf->cell(0, $y, $venta->total, 0, 1, 'R', 0);
        $pdf->ln(6);

        //CODIGO QR
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

        $x = 150;
        $y = 5;
        $pdf->Image($ruta_qr, $x + 15, $pdf->GetY(), 30, 30);
        // $pdf->Ln(-3);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell($x, $y, utf8_decode('SON: ' . $numeroLetra), 'LRT', 1, 'L');
        $pdf->SetFont('Arial', '', 8);

        if ($venta->forma_pago == 'Contado') {
            $pdf->cell($x, $y, utf8_decode('CONDICIÓN DE PAGO: Contado'), 'BLR', 1, 'L');
        } else {
            $cuotas = json_decode($venta->cuotas);
            $pdf->cell($x, $y, utf8_decode('CONDICIÓN DE PAGO: Credito'), 'LR', 1, 'L');
            foreach ($cuotas as $key => $v) {
                $newDate = date("d/m/Y", strtotime($v->fecha));
                $pdf->cell($x, $y, utf8_decode($v->cuota . ' / Fecha: ' . $newDate . ' / Monto: S/' . $v->monto), 'LR', 1, 'L');
            }
            $pdf->cell($x, $y, '', 'BLR', 1, 'L');
        }

        $name_comprobante = strtolower($venta->nombre_tipodoc);

        $pdf->Ln(7);
        $pdf->cell(0, $y, utf8_decode("Representación Impresa de la $name_comprobante electrónica"), 0, 1, 'L', 0);
        $buscarComprobante = route('searchDocuments.index');
        $pdf->cell(0, $y, utf8_decode("Consultar en : $buscarComprobante"), 0, 1, 'L', 0);

        if ($venta->estado == 0) {
            $pdf->Image(base_url('/assets/img/anulado.png'), 35, 70, 80);
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
            if ($value->nombre_afectacion == 'GRA') {
                $pdf->Cell(0, -4, number_format(0, 2), 0, 1, 'R');
            } else {
                $pdf->Cell(0, -4, number_format($value->precio_unitario * $value->cantidad, 2), 0, 1, 'R');
            }
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
