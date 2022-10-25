<?php

namespace System;

use System\Request;
use System\CronosException;

/**
 * renderizar las vistas
 */

class RenderView
{
    // private static Request $request;
    private static Request $request;

    /**
     * enviar el contenido de la vista
     * @param string carpeta/archivo
     * @param array valores o array enviados desde la vista
     */
    public static function render(string $view, array $data = [])
    {
        self::initClass();
        $data = self::dataRedirect($data);

        //almacena la vista en una variable
        $content = Self::renderOnlyView($view, $data);

        //envia la vista al navegador
        include_once  'printView.php';

        self::removeSessionFlash();
    }

    /**
     * renderiza la vista desde el controlador
     */
    protected static function renderOnlyView(string $view,  array $dynamicVariable)
    {
        foreach ($dynamicVariable as $key => $value) {
            //$$ genrera una variable dinamica
            if (is_string($value) || is_bool($value) || is_numeric($value)) {
                $$key = $value;
            } else {
                $$key = RESULT_TYPE === 'array' ? (array)$value : (object)$value;
            }
        }

        //ruta del archivo de la renderizacion de la vista
        $path = DIR_APP . "/View/$view.php";

        if (file_exists($path)) {
            //ob_start captura el contenido de la vista
            ob_start();
            include_once $path;
            //ob_get_clean devuelve el contenido de la vista
            return ob_get_clean();
        } else {
            throw new CronosException("Upsss... No se encontro la vista: view(" . $view . ")");
        }
    }

    /**
     * instancia la clase Request
     */
    private static function initClass()
    {
        self::$request = new Request();
    }

    private static function dataRedirect(array $data = [])
    {
        if (session()->has('renderView')) {
            //traer datos de la session renderView
            $dataRedirect = (array)auth()->get('renderView');

            foreach ($dataRedirect as $key => $value) {
                $data[$key] = $value;
            }

            //carputar parte del URL actual y guardar en una session
            if (!session()->has('reserveRoute')) {
                $paramUrl = self::$request->getPath();
                session()->set('reserveRoute', [$paramUrl]);
            }
        }

        //si cambia la ruta actual, eliminar la session
        $sessionLInk = (array)auth()->get('reserveRoute');
        if ($sessionLInk[0] !== self::$request->getPath()) {
            session()->remove('renderView');
            session()->remove('reserveRoute');
        }
        return $data;
    }

    private static function removeSessionFlash()
    {
        if (session()->has('flashSession')) {
            session()->remove('flashSession');
        }
    }
}
