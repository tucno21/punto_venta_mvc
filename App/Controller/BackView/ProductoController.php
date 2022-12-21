<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Unidades;
use App\Model\Productos;
use App\Model\Categorias;
use App\Library\FPDF\FPDF;
use App\Model\Factura\TipoAfectacion;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Intervention\Image\ImageManagerStatic as Image;

class ProductoController extends Controller
{
    public function __construct()
    {
        //enviar 'auth' si ha creado session sin clave de lo contrario enviar la clave
        $this->middleware('auth');
        //enviar el nombre de la ruta
        //$this->except(['users', 'users.create'])->middleware('loco');
    }

    public function index()
    {
        return view('productos/index', [
            'titleGlobal' => 'Productos',
        ]);
    }

    public function dataTable()
    {
        $productos = Productos::getProductos();
        //cuando viene un solo objeto
        if (is_object($productos)) {
            $productos = [$productos];
        }
        //json
        echo json_encode($productos);
        exit;
    }

    public function create()
    {
        //return view('folder/file', [
        //   'var' => 'es una variable',
        //]);
    }

    public function store()
    {
        $data = $_POST;
        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }
        $data = (object)$data;

        $valid = $this->validate($data, [
            'codigo' => 'required',
            'detalle' => 'required',
            'stock' => 'required',
            'stock_minimo' => 'required',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
            'unidad_id' => 'required',
            'categoria_id' => 'required',
            'tipo_afectacion_id' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            if (!empty($data->imagen["tmp_name"])) {
                //generar nombre unico para la imagen
                $nameImagen = md5(uniqid(rand(), true)) . '.png';
                //modificar imagen
                $imageFoto = Image::make($data->imagen['tmp_name'])->fit(200, 200);
                //agregar al objeto
                $data->imagen = $nameImagen;

                //guardar imagen
                if (!is_dir(DIR_IMG)) {
                    mkdir(DIR_IMG);
                }

                $imageFoto->save(DIR_IMG . $nameImagen);
            } else {
                $data->imagen = null;
            }

            $data->user_id = session()->user()->id;

            Productos::create($data);
            $response = ['status' => true, 'data' => 'Creado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function edit()
    {
        $id = $this->request()->getInput();

        if (empty((array)$id)) {
            $producto = null;
        } else {
            $producto = Productos::first($id->id);
        }

        $response = ['status' => true, 'data' => $producto];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function update()
    {
        $data = $_POST;
        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }
        $data = (object)$data;

        $valid = $this->validate($data, [
            'codigo' => 'required',
            'detalle' => 'required',
            'stock' => 'required',
            'stock_minimo' => 'required',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
            'unidad_id' => 'required',
            'categoria_id' => 'required',
            'tipo_afectacion_id' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            if (!empty($data->imagen["tmp_name"])) {
                //generar nombre unico para la imagen
                $nameImagen = md5(uniqid(rand(), true)) . '.png';
                //modificar imagen
                $imageFoto = Image::make($data->imagen['tmp_name'])->fit(200, 200);
                //agregar al objeto
                $data->imagen = $nameImagen;

                $fotoProducto = Productos::first($data->id);

                //eliminar imagen anterior
                $photoExists = file_exists(DIR_IMG . $fotoProducto->imagen);
                if ($photoExists) {
                    unlink(DIR_IMG . $fotoProducto->imagen);
                }

                //guardar imagen
                if (!is_dir(DIR_IMG)) {
                    mkdir(DIR_IMG);
                }

                $imageFoto->save(DIR_IMG . $nameImagen);
            } else {
                $data->imagen = null;
            }

            // $data->user_id = session()->user()->id;

            Productos::update($data->id, $data);
            $response = ['status' => true, 'data' => 'Actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function destroy()
    {
        $data = $this->request()->getInput();
        $result = Productos::delete((int)$data->id);
        $response = ['status' => true, 'data' => 'Eliminado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function status()
    {
        $data = $this->request()->getInput();
        $unidad = Productos::select('id', 'estado')->where('id', $data->id)->get();
        // dd($user);
        $estado = ($unidad->estado == 1) ? 0 : 1;
        $result = Productos::update($data->id, ['estado' => $estado]);
        $response = ['status' => true, 'data' => 'Actualizado correctamente'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function unidades()
    {
        $unidades = Unidades::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($unidades)) {
            $unidades = [$unidades];
        }
        //json
        echo json_encode($unidades);
        exit;
    }

    public function categorias()
    {
        $categorias = Categorias::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($categorias)) {
            $categorias = [$categorias];
        }
        //json
        echo json_encode($categorias);
        exit;
    }

    public function afectacion()
    {
        $afectation = TipoAfectacion::where('estado', 1)->get();
        //cuando viene un solo objeto
        if (is_object($afectation)) {
            $afectation = [$afectation];
        }
        //json
        echo json_encode($afectation);
        exit;
    }

    public function verData()
    {
        $data = $this->request()->getInput();
        $producto = Productos::getProducto($data->id);
        //json
        echo json_encode($producto);
        exit;
    }

    public function barcode()
    {
        $data = $this->request()->getInput();
        $producto = Productos::productoCode($data->codigo);

        $response = ['status' => false, 'message' => 'No se encontro el producto'];

        if (!empty($producto)) {
            $response = ['status' => true, 'data' => $producto];
        }
        echo json_encode($response);
        exit;
    }

    public function inputSearch()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Productos::search($data->search);

        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->codigo . ' - ' . $value->detalle;
        }

        echo json_encode($response);
        exit;
    }
    public function barcodecompra()
    {
        $data = $this->request()->getInput();
        $producto = Productos::productoCodeCompra($data->codigo);

        $response = ['status' => false, 'message' => 'No se encontro el producto'];

        if (!empty($producto)) {
            $response = ['status' => true, 'data' => $producto];
        }
        echo json_encode($response);
        exit;
    }

    public function inputSearchcompra()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Productos::searchCompra($data->search);

        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->codigo . ' - ' . $value->detalle;
        }

        echo json_encode($response);
        exit;
    }

    public function barcodekardex()
    {
        $data = $this->request()->getInput();
        $producto = Productos::productoCodeKardex($data->codigo);

        $response = ['status' => false, 'message' => 'No se encontro el producto'];

        if (!empty($producto)) {
            $response = ['status' => true, 'data' => $producto];
        }
        echo json_encode($response);
        exit;
    }

    public function inputSearchkardex()
    {
        //busqueda para autocompletar
        $data = $this->request()->getInput();
        //obligatorio recibir ->search
        $response = Productos::searchKardex($data->search);

        if (is_object($response)) {
            $response = [$response];
        }
        foreach ($response as $key => $value) {
            //obligatorio agregar ->textItem
            $response[$key]->textItem = $value->codigo . ' - ' . $value->detalle;
        }

        echo json_encode($response);
        exit;
    }


    public function pdf()
    {
        $productos = Productos::getProductos();
        if (is_object($productos)) {
            $productos = [$productos];
        }
        //$productos si es un array vacio
        if (empty($productos)) {
            echo "No hay datos para mostrar";
            exit;
        }
        // dd($productos);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Reporte de productos');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Lista: productos', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetAutoPageBreak('auto', 2); // 2 es el margen inferior
        $pdf->SetDisplayMode(75); // zoom 75% (opcional)

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(8, 5, utf8_decode('N°'), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode('Codigo'), 1, 0, 'C');
        $pdf->Cell(90, 5, utf8_decode('Detalle'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('P. Compra'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('P. Venta'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Stock'), 1, 0, 'C');
        $pdf->Cell(35, 5, utf8_decode('Categoria'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Estado'), 1, 0, 'C');
        $pdf->Ln(5);

        $i = 1;
        foreach ($productos as $producto) {
            if ($producto->estado == 1)
                $producto->condicion = '';
            if ($producto->estado == 0)
                $producto->condicion = 'Inactivo';

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(8, 5, $i, 1, 0, 'C');
            $pdf->Cell(35, 5, utf8_decode($producto->codigo), 1, 0, 'C');
            $pdf->Cell(90, 5, utf8_decode($producto->detalle), 1, 0, 'L');
            $pdf->Cell(20, 5, utf8_decode($producto->precio_compra), 1, 0, 'C');
            $pdf->Cell(20, 5, utf8_decode($producto->precio_venta), 1, 0, 'C');
            $pdf->Cell(20, 5, utf8_decode($producto->stock), 1, 0, 'C');
            $pdf->Cell(35, 5, utf8_decode($producto->categoria), 1, 0, 'C');
            $pdf->Cell(20, 5, utf8_decode($producto->condicion), 1, 0, 'C');
            $pdf->Ln(5);
            $i++;
        }

        $pdf->Output("Reporte-ventas" . ".pdf", "I");
    }

    public function excel()
    {
        $productos = Productos::getProductos();
        if (is_object($productos)) {
            $productos = [$productos];
        }
        //$productos si es un array vacio
        if (empty($productos)) {
            echo "No hay datos para mostrar";
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator(session()->user()->name);
        $spreadsheet->getProperties()->setTitle("Reporte de productos");

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("productos");
        $hojaActiva->getColumnDimension('A')->setWidth(5); //orden
        $hojaActiva->getColumnDimension('B')->setWidth(15); //codigo
        $hojaActiva->getColumnDimension('C')->setWidth(30); //detalle
        $hojaActiva->getColumnDimension('D')->setWidth(10); //precio compra
        $hojaActiva->getColumnDimension('E')->setWidth(10); //precio venta
        $hojaActiva->getColumnDimension('F')->setWidth(10); //stock
        $hojaActiva->getColumnDimension('G')->setWidth(20); //categoria
        $hojaActiva->getColumnDimension('H')->setWidth(15); //estado

        //unir celda para el titulo
        $hojaActiva->mergeCells('A1:H1');
        $hojaActiva->mergeCells('A2:H2');
        //estilo titulo
        $hojaActiva->getStyle('A1:H1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:H1')->getFont()->setSize(16);
        $hojaActiva->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A2:H2')->getFont()->setBold(true);
        $hojaActiva->getStyle('A2:H2')->getFont()->setSize(12);
        $hojaActiva->getStyle('A2:H2')->getAlignment()->setHorizontal('center');

        //titulo
        $hojaActiva->setCellValue('A1', 'Reporte: productos');
        $hojaActiva->setCellValue('A2', 'Fecha de emision: ' . date('Y-m-d H:i:s'));

        //estilo cabecera
        $hojaActiva->getStyle('A4:H4')->getFont()->setBold(true);
        $hojaActiva->getStyle('A4:H4')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A4:H4')->getAlignment()->setVertical('center');
        $hojaActiva->getStyle('A4:H4')->getFill()->setFillType('solid');
        $hojaActiva->getStyle('A4:H4')->getFill()->getStartColor()->setARGB('FFEEEEEE');
        //cabecera
        $hojaActiva->setCellValue('A4', 'N°');
        $hojaActiva->setCellValue('B4', 'Codigo');
        $hojaActiva->setCellValue('C4', 'Detalle');
        $hojaActiva->setCellValue('D4', 'P. Compra');
        $hojaActiva->setCellValue('E4', 'P. Venta');
        $hojaActiva->setCellValue('F4', 'Stock');
        $hojaActiva->setCellValue('G4', 'Categoria');
        $hojaActiva->setCellValue('H4', 'Estado');
        //centrar
        $hojaActiva->getStyle('A4:H4')->getAlignment()->setHorizontal('center');
        $hojaActiva->getStyle('A4:H4')->getFont()->setBold(true);
        $hojaActiva->getStyle('A4:H4')->getBorders()->getAllBorders()->setBorderStyle('thin');

        $i = 1;
        foreach ($productos as $producto) {
            if ($producto->estado == 1)
                $producto->condicion = '';
            if ($producto->estado == 0)
                $producto->condicion = 'Inactivo';

            $hojaActiva->setCellValue('A' . ($i + 4), $i);
            $hojaActiva->setCellValue('B' . ($i + 4), $producto->codigo);
            $hojaActiva->setCellValue('C' . ($i + 4), $producto->detalle);
            $hojaActiva->setCellValue('D' . ($i + 4), $producto->precio_compra);
            $hojaActiva->setCellValue('E' . ($i + 4), $producto->precio_venta);
            $hojaActiva->setCellValue('F' . ($i + 4), $producto->stock);
            $hojaActiva->setCellValue('G' . ($i + 4), $producto->categoria);
            $hojaActiva->setCellValue('H' . ($i + 4), $producto->condicion);
            //bordes
            $hojaActiva->getStyle('A' . ($i + 4) . ':H' . ($i + 4))->getBorders()->getAllBorders()->setBorderStyle('thin');
            $i++;
        }

        $filename = 'productos-' . date('YmdHis');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public static function tablemodel()
    {
        $excel = new Spreadsheet();
        $hojaActiva = $excel->getActiveSheet();
        $hojaActiva->setTitle('Productos');
        $hojaActiva->getTabColor()->setRGB('FF0000');

        //COLOR CELDA
        $hojaActiva->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BDECB6');

        $hojaActiva->getColumnDimension('A')->setWidth(15);
        $hojaActiva->setCellValue('A1', 'CODIGO');
        $hojaActiva->getColumnDimension('B')->setWidth(30);
        $hojaActiva->setCellValue('B1', 'DETALLE');
        $hojaActiva->getColumnDimension('C')->setWidth(18);
        $hojaActiva->setCellValue('C1', 'PRECIO COMPRA');
        $hojaActiva->getColumnDimension('D')->setWidth(18);
        $hojaActiva->setCellValue('D1', 'PRECIO VENTA');
        $hojaActiva->getColumnDimension('E')->setWidth(7);
        $hojaActiva->setCellValue('E1', 'STOCK');
        $hojaActiva->getColumnDimension('F')->setWidth(12);
        $hojaActiva->setCellValue('F1', 'STOCK MIN');
        $hojaActiva->getColumnDimension('G')->setWidth(15);
        $hojaActiva->setCellValue('G1', 'CATEGORIA');
        $hojaActiva->getColumnDimension('H')->setWidth(12);
        $hojaActiva->setCellValue('H1', 'UNIDAD');
        $hojaActiva->getColumnDimension('I')->setWidth(25);
        $hojaActiva->setCellValue('I1', 'CODIGO TIPO AFECTACION');

        //EJEMPLO
        $hojaActiva->setCellValue('A2', '213153');
        $hojaActiva->setCellValue('B2', 'LAPTOP HP');
        $hojaActiva->setCellValue('C2', '1500');
        $hojaActiva->setCellValue('D2', '2000');
        $hojaActiva->setCellValue('E2', '10');
        $hojaActiva->setCellValue('F2', '5');
        $hojaActiva->setCellValue('G2', 'ELECTRONICA');
        $hojaActiva->setCellValue('H2', 'UNIDAD');
        $hojaActiva->setCellValue('I2', '10');



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="modeloProductos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($excel, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function codigoafectacion()
    {
        $tipo = TipoAfectacion::get();
        if (is_object($tipo)) {
            $tipo = [$tipo];
        }

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->setMargins(10, 10, 10);
        $pdf->setTitle('Tipos de Afectacion IGV');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'Tipos de Afectacion IGV', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 5, utf8_decode('Utilice el codigo para la afectación de venta de los productos, no use si el estado esta inactivo'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 7, utf8_decode('CÓDIGO'), 1, 0, 'C');
        $pdf->Cell(100, 7, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C');
        $pdf->Cell(20, 7, 'ESTADO', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        foreach ($tipo as $tip) {
            $pdf->Cell(20, 7, utf8_decode($tip->codigo), 1, 0, 'C');
            $pdf->Cell(100, 7, utf8_decode($tip->descripcion), 1, 0, 'L');
            if ($tip->estado == 1) {
                $pdf->Cell(20, 7, utf8_decode('Activo'), 1, 1, 'C');
            } else {
                $pdf->Cell(20, 7, utf8_decode('Inactivo'), 1, 1, 'C');
            }
        }

        $pdf->Output('I', 'codigoAfectacion.pdf');
    }

    public function uploaddatastore()
    {
        $data = $_POST;
        if (!empty($_FILES)) {
            $data = array_merge($data, $_FILES);
        }
        $data = (object)$data;

        $valid = $this->validate($data, [
            'dataexcel' => 'requiredFile',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'message' => 'No envio ningun archivo'];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {

            $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

            $codigoProductos = Productos::getCodigoProductos();
            $categorias = Categorias::getCategorias();
            $categorias = array_column($categorias, 'nombre', 'id');
            $unidades = Unidades::getUnidades();
            $unidades = array_column($unidades, 'descripcion', 'id');
            $tiposAfectaciones = TipoAfectacion::getTipoAfectacion();
            $tiposAfectaciones = array_column($tiposAfectaciones, 'codigo', 'id');
            // dd($tiposAfectaciones);

            if (in_array($data->dataexcel['type'], $allowedFileType)) {
                //movel el archivo a la carpeta temporal
                $excelPath = DIR_IMG . $data->dataexcel['name'];
                move_uploaded_file($data->dataexcel['tmp_name'], $excelPath);

                //revisar el documento
                $documentoExcel = IOFactory::load($excelPath);
                $hojaactual = $documentoExcel->getSheet(0);
                $numeroFilas = $hojaactual->getHighestDataRow();

                $dataProductos = [];
                for ($fila = 2; $fila <= $numeroFilas; $fila++) {
                    $codigo = $hojaactual->getCell('A' . $fila)->getValue();
                    $detalle = $hojaactual->getCell('B' . $fila)->getValue();
                    $precioCompra = $hojaactual->getCell('C' . $fila)->getValue();
                    $precioVenta = $hojaactual->getCell('D' . $fila)->getValue();
                    $stock = $hojaactual->getCell('E' . $fila)->getValue();
                    $stockMin = $hojaactual->getCell('F' . $fila)->getValue();
                    $categoria = $hojaactual->getCell('G' . $fila)->getValue();
                    $unidad = $hojaactual->getCell('H' . $fila)->getValue();
                    $codigoAfectacion = $hojaactual->getCell('I' . $fila)->getValue();

                    foreach ($codigoProductos as $coPro) {
                        if ($coPro->codigo == $codigo) {
                            $response = ['status' => false, 'message' => 'El codigo ' . $codigo . ' ya existe en la fila ' . $fila];
                            echo json_encode($response, JSON_UNESCAPED_UNICODE);
                            exit;
                        }
                    }

                    if (array_search($categoria, $categorias) === false) {
                        $response = ['status' => false, 'message' => 'La categoria ' . $categoria . ' no existe o esta inactiva en la fila ' . $fila];
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        exit;
                    } else {
                        $categoria = array_search($categoria, $categorias);
                    }

                    if (array_search($unidad, $unidades) === false) {
                        $response = ['status' => false, 'message' => 'La unidad ' . $unidad . ' no existe o esta inactiva en la fila ' . $fila];
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        exit;
                    } else {
                        $unidad = array_search($unidad, $unidades);
                    }

                    if (array_search($codigoAfectacion, $tiposAfectaciones) === false) {
                        $response = ['status' => false, 'message' => 'El codigo de afectacion ' . $codigoAfectacion . ' no existe o esta inactiva en la fila ' . $fila];
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        exit;
                    } else {
                        $codigoAfectacion = array_search($codigoAfectacion, $tiposAfectaciones);
                    }

                    //array push
                    array_push($dataProductos, [
                        'codigo' => $codigo,
                        'detalle' => $detalle,
                        'precio_compra' => $precioCompra,
                        'precio_venta' => $precioVenta,
                        'stock' => $stock,
                        'stock_minimo' => $stockMin,
                        'categoria_id' => $categoria,
                        'unidad_id' => $unidad,
                        'tipo_afectacion_id' => $codigoAfectacion,
                        'user_id' => session()->user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                // dd($dataProductos);
                $result = Productos::insertProductos($dataProductos);
                unlink($excelPath);
                $response = ['status' => true, 'message' => 'Productos registrados correctamente'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $response = ['status' => false, 'message' => 'Tipo de archivo no permitido'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }
}
