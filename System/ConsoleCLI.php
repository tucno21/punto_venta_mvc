<?php

namespace System;

class ConsoleCLI
{
    private $matic = null;
    private $make = null;
    private $name = null;
    private $file = null;

    private $pathCont = null;
    private $pathModel = null;
    private $layout = null;



    public function __construct($data)
    {
        if (isset($data[0])) {
            $this->matic = $data[0];
        }
        if (isset($data[1])) {
            $this->make = $data[1];
        }
        if (isset($data[2])) {
            $this->name = $data[2];
        }
        if (isset($data[3])) {
            $this->file = $data[3];
        }
        /**
         * ruta del controlador y modelo
         */
        $this->pathCont = dirname(__DIR__) . '/App/Controller/';
        $this->pathModel = dirname(__DIR__) . '/App/Model/';
        /**
         * rura de la plantilla de controlador y modelos
         */
        $this->layout = dirname(__DIR__) . '/System/Layout/';
    }

    public function run()
    {
        if ($this->make !== null) {
            if ($this->make == 'make:controller') {
                $this->controller();
            } else if ($this->make == 'make:model') {
                $this->model();
            } else {

                $text =   "\n" . "Command not found" . "\n";
                $text2 =    "\n" . "make:controller name folderName(optional)" . "\n";
                $text3 =   "make:model name folderName(optional)" . "\n";

                print("\e[0;31m$text\e[0m");
                print("\e[0;36m$text2\e[0m");
                print("\e[0;36m$text3\e[0m");
            }
        }
    }

    private function controller()
    {
        if ($this->file === null) {
            $this->controllerAA();
        } else {
            $this->controllerBB();
        }
    }

    private function controllerAA()
    {
        $baseController = $this->layout . "conAA.txt";
        $nameController = ucfirst($this->name);
        $newController = $this->pathCont . $nameController . '.php';

        if (file_exists($newController)) {

            $text =   "\n" . "The $nameController.php file already exists" . "\n";
            print("\e[0;31m$text\e[0m");
        } else {
            file_put_contents($newController, str_replace('CAMBIO', $nameController, file_get_contents($baseController)));

            $text =   "\n" . "successfully created." . "\n";
            print("\e[0;34m$text\e[0m");
        }
    }

    private function controllerBB()
    {
        $baseController = $this->layout . "conBB.txt";
        $nameController = ucfirst($this->name);
        $newController = $this->pathCont . ucfirst($this->file) . '/' . $nameController . '.php';

        if (file_exists($newController)) {

            $text =   "\n" . "The $nameController.php file already exists" . "\n";
            print("\e[0;31m$text\e[0m");
        } else {
            if (!is_dir($this->pathCont . ucfirst($this->file))) {
                mkdir($this->pathCont . ucfirst($this->file));
            }

            $buscar = ['CAMBIONAME', 'CAMBIODIR'];
            $cambiar = [$nameController, ucfirst($this->file)];

            file_put_contents($newController, str_replace($buscar, $cambiar, file_get_contents($baseController)));

            $text =   "\n" . "successfully created." . "\n";
            print("\e[0;34m$text\e[0m");
        }
    }

    private function model()
    {
        if ($this->file === null) {
            $this->modelAA();
        } else {
            $this->modelBB();
        }
    }

    private function modelAA()
    {
        $baseModel = $this->layout . "moAA.txt";
        $nameModel = ucfirst($this->name);
        $newModel = $this->pathModel . $nameModel . '.php';

        if (file_exists($newModel)) {

            $text =   "\n" . "The $nameModel.php file already exists" . "\n";
            print("\e[0;31m$text\e[0m");
        } else {
            file_put_contents($newModel, str_replace('CAMBIO', $nameModel, file_get_contents($baseModel)));

            $text =   "\n" . "successfully created." . "\n";
            print("\e[0;34m$text\e[0m");
        }
    }

    private function modelBB()
    {
        $baseModel = $this->layout . "moBB.txt";
        $nameModel = ucfirst($this->name);
        $newModel = $this->pathModel . ucfirst($this->file) . '/' . $nameModel . '.php';

        if (file_exists($newModel)) {

            $text =   "\n" . "The $nameModel.php file already exists" . "\n";
            print("\e[0;31m$text\e[0m");
        } else {
            if (!is_dir($this->pathModel . ucfirst($this->file))) {
                mkdir($this->pathModel . ucfirst($this->file));
            }

            $buscar = ['CAMBIONAME', 'CAMBIODIR'];
            $cambiar = [$nameModel, ucfirst($this->file)];

            file_put_contents($newModel, str_replace($buscar, $cambiar, file_get_contents($baseModel)));

            $text =   "\n" . "successfully created." . "\n";
            print("\e[0;34m$text\e[0m");
        }
    }
}
