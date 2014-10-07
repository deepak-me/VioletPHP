<?php

class Controller {

    public $view;
    private $template;
    private $getView;
    public $model;

//public $defaultMethod;
    function __construct() {
        //    echo "Inside violet controller(main)<br/>";
        //   $this->defaultMethod = "index";
    }

    function loadModel($model) {
        require 'models/' . $model . '.php';
       // return new $model();
       $this->model = new $model;
    }

    function initView($view) {
        $this->getView = $view;
        $this->view = new Template('views/' . $view . '.php');

    }

    function renderView() {
        if (TEMPLATE_ENGINE == 'on') {
            $this->view->processData($this->template);
        } else {
            require 'views/' . $this->getView . '.php';
        }
    }

//open databse , load model , load view [php files]
}
