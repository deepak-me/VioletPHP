<?php

require 'libraries/exception.php';

class Router {

    private $controller;
    private $controllerObject;
    private $method;
    private $parameters = Array();
    private $url;
    private $exceptionObject;
    private $exceptionCode;

    function __construct() {
        if (isset($_GET['url'])) {
            $this->url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            $this->url = explode('/', $this->url);
            // print_r($this->url);

            self::getParameters();

            self::getController();

            self::getMethod();
        }

      //  echo "Indise the router <br/>";
    }

    private function getParameters() {
        if (count($this->url > 2)) {
            $this->parameters = $this->url;
            array_shift($this->parameters);
            array_shift($this->parameters);
            // print_r($this->parameters);
        }
    }

    private function getController() {

        if (isset($this->url[0])) {
            $this->controller = $this->url[0];

            if (file_exists("controllers/$this->controller.php")) {
                require "controllers/$this->controller.php";
                $this->controllerObject = new $this->controller();
            } else {
                $this->exceptionCode = "100";
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode);
                exit(0);
            }
        }
    }

    private function getMethod() {
        if (isset($this->url[1])) {
            $this->method = $this->url[1];

            if (method_exists($this->controllerObject, $this->method)) {

                $reflection = new ReflectionMethod($this->controllerObject, $this->method);
                $requiredParams = $reflection->getNumberOfParameters();

                if (count($this->parameters) == $requiredParams) {
                    call_user_func_array(array($this->controllerObject, $this->method), $this->parameters);
                } else {
                    $this->exceptionCode = "102";
                    $this->exceptionObject = new ExceptionHandler($this->exceptionCode);
                    exit(0);
                }
            } else {
                $this->exceptionCode = "101";
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode);
                exit(0);
            }
        }
    }

}
