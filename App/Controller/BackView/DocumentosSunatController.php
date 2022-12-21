<?php

namespace App\Controller\BackView;

use ZipArchive;
use System\Controller;

class DocumentosSunatController extends Controller
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
        return view('documentosunat/index', [
            'titleGlobal' => 'Documentos Sunat',
        ]);
    }

    public function xml()
    {
        $folder = DIR_APP . '/Library/ApiFacturador/files_factura/xml_files';

        //comprimir la carpeta folder
        $zip = new ZipArchive();
        $zip->open('xml_files.zip', ZipArchive::CREATE);
        $files = scandir($folder);
        foreach ($files as $file) {
            if (is_file($folder . '/' . $file)) {
                $zip->addFile($folder . '/' . $file, $file);
            }
        }
        $zip->close();

        //descargar el archivo
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=xml_files.zip');
        header('Content-Length: ' . filesize('xml_files.zip'));
        readfile('xml_files.zip');
        unlink('xml_files.zip');
    }

    public function cdr()
    {
        $folder = DIR_APP . '/Library/ApiFacturador/files_factura/cdr_files';

        //comprimir la carpeta folder
        $zip = new ZipArchive();
        $zip->open('cdr_files.zip', ZipArchive::CREATE);
        $files = scandir($folder);
        foreach ($files as $file) {
            if (is_file($folder . '/' . $file)) {
                $zip->addFile($folder . '/' . $file, $file);
            }
        }
        $zip->close();

        //descargar el archivo
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=cdr_files.zip');
        header('Content-Length: ' . filesize('cdr_files.zip'));
        readfile('cdr_files.zip');
        unlink('cdr_files.zip');
    }
}
