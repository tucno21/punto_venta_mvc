<?php

namespace App\Controller\BackView;

use App\Model\Ventas;
use System\Controller;
use App\Model\Clientes;
use App\Model\InfoEmpresa;
use App\Help\PrintPdf\PrintPdf;
use App\Model\Factura\TipoComprobante;

class SearchDocumentController extends Controller
{
    public function __construct()
    {
        //enviar 'auth' si ha creado session sin clave de lo contrario enviar la clave
        //$this->middleware('auth');
        //enviar el nombre de la ruta
        //$this->except(['users', 'users.create'])->middleware('loco');
    }

    public function index()
    {
        return view('searchDocument/index', [
            'title' => 'es una variable',
        ]);
    }

    public function tipoComprobante()
    {
        $tipoDoc = TipoComprobante::getTipoComprobante('venta');
        //cuando viene un solo objeto
        if (is_object($tipoDoc)) {
            $tipoDoc = [$tipoDoc];
        }
        //json
        echo json_encode($tipoDoc);
        exit;
    }

    public function store()
    {
        $data = $_POST;
        $data = (object)$data;

        $valid = $this->validate($data, [
            'tipodoc' => 'required',
            'fecha_emision' => 'required',
            'serie' => 'required',
            'correlativo' => 'required',
            'documento_cliente' => 'required',
            'total' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            // dd($data);
            $venta = Ventas::buscarVenta($data->tipodoc, $data->serie, $data->correlativo,  $data->total);
            //si el array esta vacio
            if (empty($venta)) {
                $response = ['status' => false, 'message' => 'No se encontraron resultados, verifique los datos ingresados.'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }

            if ($venta->documentocliente != $data->documento_cliente) {
                $response = ['status' => false, 'message' => 'El documento del cliente no coincide.'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }

            $venta->fecha_emision = date('Y-m-d', strtotime($venta->fecha_emision));
            if ($venta->fecha_emision != $data->fecha_emision) {
                $response = ['status' => false, 'message' => 'La fecha de emisión no coincide.'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }

            $response = ['status' => true, 'data' => $venta];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function downloadxml()
    {
        $data = $this->request()->getInput();
        $rutaxml = DIR_APP . '/Library/ApiFacturador/files_factura/xml_files/' . $data->xml . '.XML';

        if (file_exists($rutaxml)) {

            $carpetaXML = DIR_PUBLIC . '/xml_files/';
            if (!file_exists($carpetaXML)) {
                mkdir($carpetaXML, 0777, true);
            }
            $rutaPublic = DIR_PUBLIC . '/xml_files/' . $data->xml . '.XML';

            copy($rutaxml, $rutaPublic);

            $archivo = base_url('/xml_files/' . $data->xml . '.XML');
            header('Content-disposition: attachment; filename=' . $data->xml . '.XML');
            header('Content-type: application/xml');
            readfile($archivo);

            unlink($rutaPublic);
            rmdir($carpetaXML);

            exit;
        } else {
            echo 'No existe el archivo';
        }
    }

    public function downloadcdr()
    {
        $data = $this->request()->getInput();
        $rutaxmlZIP = DIR_APP . '/Library/ApiFacturador/files_factura/cdr_files/R-' . $data->xml . '.ZIP';

        if (file_exists($rutaxmlZIP)) {

            // descargar $rutaxmlZIP
            $carpetaZIP = DIR_PUBLIC . '/cdr_files/';
            if (!file_exists($carpetaZIP)) {
                mkdir($carpetaZIP, 0777, true);
            }
            $rutaPublic = DIR_PUBLIC . '/cdr_files/R-' . $data->xml . '.ZIP';

            copy($rutaxmlZIP, $rutaPublic);

            $archivo = base_url('/cdr_files/R-' . $data->xml . '.ZIP');
            header('Content-disposition: attachment; filename=R-' . $data->xml . '.ZIP');
            header('Content-type: application/zip');
            readfile($archivo);

            unlink($rutaPublic);
            exit;
        } else {
            echo 'No existe el archivo';
        }
    }

    public function reporte()
    {
        $data = $this->request()->getInput();

        $emisor = InfoEmpresa::first();
        $print = new PrintPdf;

        if (isset($data->pdfA5)) {
            $venta = Ventas::getVenta($data->pdfA5);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA5($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->pdfA4)) {
            $venta = Ventas::getVenta($data->pdfA4);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloA4($emisor, $venta, $cliente);
            return;
        }

        if (isset($data->ticket)) {
            $venta = Ventas::getVenta($data->ticket);
            $cliente = Clientes::getCliente($venta->cliente_id);
            $result = $print->ModeloTicket($emisor, $venta, $cliente);
            return;
        }

        echo 'error al generar el reporte';
        exit;
    }
}
