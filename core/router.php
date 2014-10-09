<?php

class Router {
    /*
     * store input url
     */

    private $url;
    /*
     * define routes
     */
    private $routes = Array();
    /*
     * store controller name 
     */
    private $controller;
    private $controllerObject;
    /*
     * store method name
     */
    private $method;
    /*
     * store parameter details
     */
    private $parameters = Array();
    private $paramList = Array();
    private $currentParamList;
    private $haveParams = 0;
    /*
     * store exception details
     */
    private $exceptionObject;
    private $exceptionData;
    private $exceptionCode;
    private $flag = 0;

    /*
     * creating a constructor function to load defaul contoller and method
     * check for debug mode
     */

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

    /*
     * accepting the url and calling router to get appropriate controller and method
     * if no controller found in the url -> then load default controller 
     */

    public function initialize() {
        if (isset($_GET['url'])) {
            $this->url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            $this->url = explode('/', $this->url);
            self::getRoute();
        } else {
            self::loadDefaultController();
        }
    }

    /*
     * loading the default controller
     */

    private function loadDefaultController() {
        if (file_exists("controllers/$this->defaultController.php")) {
            require "controllers/$this->defaultController.php";
            $this->controllerObject = new $this->defaultController();
            $this->controllerObject->{$this->defaultMethod}();
        }
    }

    /*
     * fetching parameters from the url (if any)
     */

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

    /*
     * load controller file if it exists, otherwise throw an exception
     */

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

    /*
     * check whether the requested method exist or not.
     */

    private function getMethod() {

        if (isset($this->method)) {
            if (method_exists($this->controllerObject, $this->method)) {

                $reflection = new ReflectionMethod($this->controllerObject, $this->method);
                $requiredParams = $reflection->getNumberOfParameters();

                /*
                 * check whether the number of parameters required by the method mateches 
                 * with the number of parameters fetched from the url.
                 */
                if (count($this->parameters) == $requiredParams) {
                    call_user_func_array(array($this->controllerObject, $this->method), $this->parameters);
                } else {
                    $this->exceptionCode = '102';
                    $this->exceptionData = array('method' => $this->method, 'requiredParams' => $requiredParams, 'passingParams' => count($this->parameters));
                    $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                    exit(0);
                }
                /*
                 * throw method not found exception
                 */
            } else {
                $this->exceptionCode = "101";
                $this->exceptionData = array('method' => $this->method, 'controller' => $this->controller);
                $this->exceptionObject = new ExceptionHandler($this->exceptionCode, $this->exceptionData);
                exit(0);
            }
        } else {
            /*
             * check for index(default) method
             * throw an exception if no index method found.
             */
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

    /*
     * add a new custom route to the routes[] array
     */

    public function addRoute($getUrl, $getController, $getMethod) {
        $this->routes[] = array("url" => "$getUrl", "controller" => "$getController", "method" => "$getMethod");
    }

    /*
     * function for managing the custom routes
     */

    public function getRoute() {
        $keys = null;
        $defaultRoute = 1;
        $match = 0;
        $found = 0;
        /*
         * specify the pattern for matching
         */
        $pattern = '/\{param\\}/';

        /*
         * fetch the routes[] array for processing
         */
        foreach ($this->routes as $route) {
            $tempUrl = $this->url;
            foreach (array_keys(preg_grep($pattern, explode("/", $route['url']))) as $k) {
                $tempUrl[$k] = explode("/", $route['url'])[$k];
            }
            /*
             * get current route,controller and method according to the url
             */
            if ($tempUrl == explode("/", $route['url'])) {
                $currentRoute = implode('/', explode("/", $route['url']));
                $currentController = $route['controller'];
                $currentMethod = $route['method'];
            }
            /*
             * check for default controller
             */
            if (implode("/", $this->url) == $route['url']) {
                $basicController = $route['controller'];
                $basicMethod = $route['method'];
                $found = 1;
            }
            /*
             * process parameter array
             */

            if (preg_match($pattern, $route['url'])) {
                trim(str_replace('//', '/', preg_replace($pattern, '', $route['url'])), "/");

                /*
                 * get controller and method from custom routes
                 */
                if (array_replace(explode('/', $route['url']), $this->url) == $this->url) {
                    $keys = array_keys(preg_grep($pattern, explode('/', $route['url'])));
                    $c = $route['controller'];
                    $m = $route['method'];
                    $this->haveParams = 1;
                }
            }
        }

        foreach ($this->routes as $route2) {
            if (explode('/', $route2['url']) == array_replace($this->url, explode('/', $route2['url']))) {
                $match = 1;
                break;
            } else {
                $match = 0;
            }
        }

        if ($this->haveParams == 1) {
            foreach ($keys as $key) {
                $this->paramList[] = $this->url[$key];
            }
        }

        if ($match == 1) {
            foreach ($this->routes as $route3) {
                $b1_keys = array_keys(preg_grep($pattern, explode("/", $route3['url'])));
                $tempUrl = $this->url;
                foreach ($b1_keys as $k) {
                    $tempUrl[$k] = explode("/", $route3['url'])[$k];
                }
                /*
                 * input url match with url given in router. 
                 * not a default router
                 */
                if ($tempUrl == explode("/", $route3['url'])) {
                    $defaultRoute = 0;
                }
            }

            if ($defaultRoute == 1) {
                /*
                 * this is a default router
                 * set controller and method from url
                 */
                $this->haveParams = 0;
                $keys = null;
                if (isset($this->url[0])) {
                    $this->controller = $this->url[0];
                }
                if (isset($this->url[1])) {
                    $this->method = $this->url[1];
                }
                if ($keys != null) {
                    $this->controller = $c;
                    $this->method = $m;
                }
            } else {
                /*
                 * this is not a default router
                 */
                $this->haveParams = 1;
            }

            if ($this->haveParams == 1) {
                $this->controller = $route2['controller'];
                $this->method = $route2['method'];
                if ($keys == null) {
                    $this->controller = $route['controller'];
                    $this->method = $route['method'];
                }
            }
        }

        if ($defaultRoute == 0 && $this->haveParams == 1) {
            /*
             * not a default router and also have parameters
             */
            foreach ($this->routes as $route) {
                if (preg_match($pattern, $route['url'])) {
                    /*
                     * accept parameters from url and store it in an array
                     */
                    $acceptParams[] = $route['url'];
                }
            }

            foreach ($acceptParams as $param) {
                if ($param == $currentRoute) {
                    /*
                     * match positions of parameters from route array with url
                     */
                    foreach (array_keys(preg_grep($pattern, explode('/', $param))) as $key) {
                        if (isset($this->url[$key])) {
                            $this->currentParamList[] = $this->url[$key];
                        }
                    }
                    $this->controller = $currentController;
                    $this->method = $currentMethod;
                    $this->flag = 1;
                }
            }
            /*
             * basic route withour parameters
             */
            if ($found == 1) {
                $this->controller = $basicController;
                $this->method = $basicMethod;
            }
        }
        /*
         * call getParameters method to fetch parameters
         */
        self::getParameters();
        /*
         * set controller
         */
        self::getController();
        /*
         * set method/action
         */
        self::getMethod();
    }

}
