<?php

//require 'libraries/exception.php';

class Router {

    private $controller;
    private $controllerObject;
    private $method;
    private $parameters = Array();
    private $url;
    private $exceptionObject;
    private $exceptionCode;
    private $routes = Array();
    private $match = 0;
    private $paramList = Array();
    private $paramArray = Array();

    private $keys = null;
    private $currentUrl;


    private $haveParams =0;
                function __construct() {
        $this->defaultController = DEFAULT_CONTROLLER;
        $this->defaultMethod = DEFAULT_METHOD;
    }

    public function initialize() {

        if (isset($_GET['url'])) {
            $this->url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            $this->url = explode('/', $this->url);
            // print_r($this->url);
            self::getRoute();
        } else {
            self::loadDefaultController();
        }
    }

    public function addRoute($getUrl, $getController, $getMethod) {
        $this->routes[] = array("url" => "$getUrl", "controller" => "$getController", "method" => "$getMethod");
    }


    public function getRoute() {

        $matchUrl = null;
        foreach ($this->url as $newUrl) {
            $matchUrl.="$newUrl" . "/";
        }
        $matchUrl = trim($matchUrl, "/");


        //  echo "$matchUrl";
        $pattern = '/\{\d+\}/';

// $this->currentUrl = explode('/', $route['url']);
        $this->currentUrl=  $this->url;
 

        foreach ($this->routes as $route) {
            if (preg_match($pattern, $route['url'])) {
                $this->paramArray = explode('/', $route['url']);
               // $this->paramArrayList = explode('/', $route['url']);
                $str = preg_replace($pattern, '', $route['url']);
                $result = trim(str_replace('//', '/', $str), "/"); // replaced string
//                echo '<br/>url:-';echo $route['url']; echo'<br/>';
// print_r($this->url);
//                echo'<br/>paramArray:-'; print_r($this->paramArray);echo'<br/>';

                $temp = array_replace($this->paramArray,$this->url);
                
                

//              echo '<br/>temp:-';print_r($temp); echo'<br/>';
//               echo'<br/>method:-' . $route['method'] . '<br/>';
//               echo'<br/>paramArray:-'; print_r($this->paramArray);echo'<br/>';
//                echo'<br/>CURL:-'; print_r($this->currentUrl);echo'<br/>';

                if ($temp == $this->currentUrl) {
                   //     echo '<br/>condition ok <br/>';
                    $result2 = preg_grep($pattern, $this->paramArray);
                    $this->keys = array_keys($result2);
                    $c = $route['controller'];
                    $m = $route['method'];
                    $this->haveParams=1;
                 //  break;
                }
            }
// match was here
        } // end of foreach
        

        foreach ($this->routes as $route2) {

            // echo $route['url'].'<br/>';
            if ($matchUrl == $route2['url']) {
                $this->match = 1;
                break;
            } else {
                $this->match = 0;
                
            }
        }



    //    echo 'p' . $this->haveParams;

        if ($this->haveParams == 1) {
         //   echo 'keys set';
            foreach ($this->keys as $key) {
                $this->paramList[] = $this->url[$key];
            }
        }
//        echo '<br/>paramList ';
//        print_r($this->paramList);
//        echo "<br/>";

        if ($this->match == 1) {
           // echo 'match found';
            $this->controller = $route2['controller'];
            $this->method = $route2['method'];
        } else {
           // echo 'no matchfound';
            if (isset($this->url[0])) {
                $this->controller = $this->url[0];
            }

            if (isset($this->url[1])) {
                $this->method = $this->url[1];
            }
            if ($this->keys != null) {
                $this->controller = $c;
                $this->method = $m;
            }
        }


        self::getParameters();

        self::getController();

        self::getMethod();
    }

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
            // print_r($this->parameters);
        }
        if($this->haveParams = 1)
        {
            $this->parameters=  $this->paramList;
        }
    }

    private function getController() {

        // if (isset($this->url[0])) {
        // $this->controller = $this->url[0];
        if (isset($this->controller)) {

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
        // if (isset($this->url[1])) {
        //   $this->method = $this->url[1];
        if (isset($this->method)) {

            if (method_exists($this->controllerObject, $this->method)) {

                $reflection = new ReflectionMethod($this->controllerObject, $this->method);
                $requiredParams = $reflection->getNumberOfParameters();

//                echo "<b>expected params:- $requiredParams $this->method</b> <br/>";
//                echo "<b>passing params:- ".  count($this->parameters)."</b> <br/>";
               // echo print_r($this->parameters);
                

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
        } else {
            if (method_exists($this->controllerObject, $this->defaultMethod)) {
                // echo 'index method found';
                $this->controllerObject->{$this->defaultMethod}();
            } else {
                echo 'index not found';
            }
        }
    }

}
