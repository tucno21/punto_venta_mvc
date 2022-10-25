<?php

/**
 * CronosException
 * controlar las excepciones de la aplicacion
 */

namespace System;

use Exception;

class CronosException extends Exception
{
    public function cronosMessage()
    {
        $errorMsg = "<div>
        <div style=' background-color:#A93226; padding:0.3rem;text-align:center;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#ffffff;margin:0;'>Error Detectado</p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>Mensaje</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: " . $this->getMessage() . "</p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>Archivo</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: " . $this->getFile() . "</p>
        </div>
        <div style=' background-color:#F1C40F; padding:0.3rem;font-size:20px;font-weight:bold;border: 1px solid #17202A;width:80%;margin: 0 auto;'>
            <p style='color:#000000;margin:0;width:15%; float:left; display:block'>En la linea</p>
            <p style='font-weight:normal;color:#273746;margin:0;display: inline'>: " . $this->getLine() . "</p>
        </div>
    </div>";
        return $errorMsg;
    }
}
