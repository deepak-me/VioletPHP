<?php

class Router {

    private $controller;
    private $controllerObject;
    private $method;
    private $parameters = Array();
    private $url;
    private $exceptionObject;
    private $exceptionData;
    private $exceptionCode;
    private $routes = Array();
    private $match = 0;
    private $paramList = Array();
    private $paramArray = Array();
    private $keys = null;
    private $currentUrl;
    private $defaultRoute = 1;
    private $haveParams = 0;
    private $c;
    private $m;
    private $acceptParams = Array();
    private $currentRoute;
    private $currentController;
    private $currentMethod;
    private $currentParamList;
    private $flag = 0;
    private $basicController;
    private $basicMethod;
    private $found = 0;

    function __construct() {
        $this->defaultController = DEFAULT_CONTROLLER;
        $this->defaultMethod = DEFAULT_METHOD;
        if (DEBUG_MODE == 'on') {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        } else {
            error_reporting(0);
            ini_set("display_errors", 0);
        }
    }

    public function initialize() {

        if (isset($_GET['url'])) {
            $this->url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            $this->url = explode('/', $this->url);
            self::getRoute();
        } else {
            self::loadDefaultController();
        }
    }

    public function addRoute($getUrl, $getController, $getMethod) {
        $this->routes[] = array("url" => "$getUrl", "controller" => "$getController", "method" => "$getMethod");
    }

    // optimize this function getRoute() ---------------  
    public function getRoute() {

        $pattern = '/\{\d+\}/';
        $this->currentUrl = $this->url;
        $tempUrl2 = implode("/", $this->url);

        foreach ($this->routes as $route) {
            $a1 = explode("/", $route['url']);
            $b1 = preg_grep($pattern, $a1);
            $b1_keys = array_keys($b1);
            $tempUrl = $this->url;
            foreach ($b1_keys as $k) {
                $tempUrl[$k] = $a1[$k];
            }
            if ($tempUrl == $a1) {
                $this->currentRoute = implode('/', $a1);
                $this->currentController = $route['controller'];
                $this->currentMethod = $route['method'];
            }
            if ($tempUrl2 == $route['url']) {
                $this->basicController = $route['controller'];
                $this->basicMethod = $route['method'];
                $this->found = 1;
            }

            if (preg_match($pattern, $route['url'])) {
                $this->paramArray = explode('/', $route['url']);
                $str = preg_replace($pattern, '', $route['url']);
                $result = trim(str_replace('//', '/', $str), "/");

                $temp = array_replace($this->paramArray, $this->url);

                if ($temp == $this->currentUrl) {
                    $result2 = preg_grep($pattern, $this->paramArray);
                    $this->keys = array_keys($result2);
                    $this->c = $route['controller'];
                    $this->m = $route['method'];
                    $this->haveParams = 1;
                }
            }
        }// end of foreach

        foreach ($this->routes as $route2) {
            $temp2 = explode('/', $route2['url']);
            $temp3 = array_replace($this->url, $temp2);
            if ($temp2 == $temp3) {
                $this->match = 1;
                break;
            } else {
                $this->match = 0;
            }
        } // end of foreach


        if ($this->haveParams == 1) {
            foreach ($this->keys as $key) {
                $this->paramList[] = $this->url[$key];
            }
        }

        if ($this->match == 1) {
            foreach ($this->routes as $temp4) {
                $a1 = explode("/", $temp4['url']);
                $b1 = preg_grep($pattern, $a1);
                $b1_keys = array_keys($b1);
                $tempUrl = $this->url;
                foreach ($b1_keys as $k) {
                    $tempUrl[$k] = $a1[$k];
                }

                if ($tempUrl == $a1) {
                    $this->defaultRoute = 0;
                }
            }

            if ($this->defaultRoute == 1) {
                $this->haveParams = 0;
                $this->keys = null;
                if (isset($this->url[0])) {
                    $this->controller = $this->url[0];
                }
                if (isset($this->url[1])) {
                    $this->method = $this->url[1];
                }
                if ($this->keys != null) {
                    $this->controller = $this->c;
                    $this->method = $this->m;
                }
            } else {
                $this->haveParams = 1;
            }
            

            if ($this->haveParams == 1) {
                $this->controller = $route2['controller'];
                $this->method = $route2['method'];
                if ($this->keys == null) {
                    $this->controller = $route['controller'];
                    $this->method = $route['method'];
                }
            }
        }
        
        if ($this->defaultRoute == 0 && $this->haveParams == 1) {
            foreach ($this->routes as $route) {
                if (preg_match($pattern, $route['url'])) {
                    $this->acceptParams[] = $route['url'];
                }
            } // end foreach
            foreach ($this->acceptParams as $param) {
                if ($param == $this->currentRoute) {
                    $p = explode('/', $param);
                    $r = preg_grep($pattern, $p);
                    $this->ke = array_keys($r);
                    foreach ($this->ke as $key) {
                        if (isset($this->url[$key])) {
                            $this->currentParamList[] = $this->url[$key];
                        }
                    }
                    $this->controller = $this->currentController;
                    $this->method = $this->currentMethod;
                    $this->flag = 1;
                }
            } // end foreach

            if ($this->found == 1) {
                $this->controller = $this->basicController;
                $this->method = $this->basicMethod;
            }  
        }

        self::getParameters();

        self::getController();

        self::getMethod();
    }

    //no edits below this -----------------------------------------------------------------------

    private function loadDefaultController() {


        if (file_exists("controllers/$this->defaultController.php")) {
            require "controllers/$this->defaultController.php";
            $this->controllerObject = new $this->defaultController();
            $this->controllerObject->{$this->defaultMethod}();
        }
    }

    private function getParameters() {
        if (count($this->url > 2)) {
            $this->parameters = $this->url;
            array_shift($this->parameters);
            array_shift($this->parameters);
        }
        if ($this->haveParams == 1) {
            $this->parameters = $this->paramList;
        }
        if ($this->flag == 1) {
            $this->parameters = $this->currentParamList;
        }
    }

    private function getController() {

        if (isset($this->controller)) {

            if (file_exists("controllers/$this->controller.php")) {
                require "controllers/$this->controller.php";
                $this->controllerObject = new $this->controller();
            } else {
                $this->exceptionCode = '100';
                $this->exceptionData = array('controller' => $this->controller);
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                exit(0);
            }
        }
    }

    private function getMethod() {

        if (isset($this->method)) {

            if (method_exists($this->controllerObject, $this->method)) {

                $reflection = new ReflectionMethod($this->controllerObject, $this->method);
                $requiredParams = $reflection->getNumberOfParameters();

                if (count($this->parameters) == $requiredParams) {
                    call_user_func_array(array($this->controllerObject, $this->method), $this->parameters);
                } else {
                    $this->exceptionCode = '102';
                    $this->exceptionData = array('method' => $this->method, 'requiredParams' => $requiredParams, 'passingParams' => count($this->parameters));
                    $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                    exit(0);
                }
            } else {
                $this->exceptionCode = "101";
                $this->exceptionData = array('method' => $this->method, 'controller' => $this->controller);
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                exit(0);
            }
        } else {
            if (method_exists($this->controllerObject, $this->defaultMethod)) {
                $this->controllerObject->{$this->defaultMethod}();
            } else {
                $this->exceptionCode = "103";
                $this->exceptionData = array('controller' => $this->controller);
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                exit(0);
            }
        }
    }

}
