<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\Unidades;
use App\Model\Productos;
use App\Model\Categorias;
use App\Model\TipoAfectacion;
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
}
