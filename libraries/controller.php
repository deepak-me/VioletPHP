<?php

class VioletController {

    function __construct() {
    //    echo "Inside violet controller(main)<br/>";
    }

    function loadModel($model) {
        require 'models/' . strtolower($model) . '.php';
        return new $model();
    }

    function renderView($view) {
        require 'views/'.  strtolower($view).'.php';
    }

//open databse , load model , load view [php files]
}
