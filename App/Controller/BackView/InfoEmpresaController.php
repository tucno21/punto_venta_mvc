<?php

namespace App\Controller\BackView;

use System\Controller;
use App\Model\InfoEmpresa;
use Intervention\Image\ImageManagerStatic as Image;

class InfoEmpresaController extends Controller
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
        return view('infoEmpresas/index', [
            'titleGlobal' => 'Informacion Empresa',
        ]);
    }

    public function edit()
    {
        $id = 1;

        if (empty((array)$id)) {
            $empresa = null;
        } else {
            $empresa = InfoEmpresa::first($id);
        }

        $response = ['status' => true, 'data' => $empresa];
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
            'tipodoc' => 'required',
            'ruc' => 'required|numeric|min:11|max:11',
            'razon_social' => 'required|string|min:3',
            'nombre_comercial' => 'required|string|min:3',
            'direccion' => 'required',
            'pais' => 'required',
            'departamento' => 'required|string|min:3',
            'provincia' => 'required|string|min:3',
            'distrito' => 'required|string|min:3',
            'ubigeo' => 'required|numeric|min:3',
            'telefono' => 'required|numeric|min:9|max:9',
            'email' => 'required|email',
            'usuario_secundario' => 'required|alpha|min:3',
            'clave_usuario_secundario' => 'required|alpha|min:3',
            'descripcion' => 'required',
            // 'logo' => 'required',
        ]);

        if ($valid !== true) {
            //mensaje de error
            $response = ['status' => false, 'data' => $valid];
            //json_encode
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            if (!empty($data->logo["tmp_name"])) {
                //generar nombre unico para la imagen
                $nameImagen = md5(uniqid(rand(), true)) . '.png';
                //modificar imagen
                // $imageFoto = Image::make($data->logo['tmp_name'])->fit(200, 200);
                $imageFoto = Image::make($data->logo['tmp_name'])->widen(200);
                //agregar al objeto
                $data->logo = $nameImagen;

                $empresa = InfoEmpresa::first($data->id);

                //eliminar imagen anterior
                $photoExists = file_exists(DIR_IMG . $empresa->logo);
                if ($photoExists) {
                    unlink(DIR_IMG . $empresa->logo);
                }

                //guardar imagen
                if (!is_dir(DIR_IMG)) {
                    mkdir(DIR_IMG);
                }

                $imageFoto->save(DIR_IMG . $nameImagen);
            } else {
                $data->logo = null;
            }
            InfoEmpresa::update($data->id, $data);
            $response = ['status' => true, 'data' => 'actualizado correctamente'];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
