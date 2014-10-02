<?php

class Test extends VioletController {

//    function __construct() {
//        parent::__construct();
//        echo "we are inside test controller <br/>";
//    }
    public function index() {
        echo "message from index() of TestController<br/>";

        // load model
        $test_model = $this->loadModel('TestModel');
        $this->t = $test_model->otherFunction();

        // load view
        $this->renderView('testview');
    }

}
