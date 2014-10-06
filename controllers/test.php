<?php

class Test extends Controller {

    function __construct() {
        parent::__construct();
        echo "we are inside test controller <br/>";
    }

    public function index() {
        echo "message from index() of TestController<br/>";

        // load model
        $test_model = $this->loadModel('TestModel');
        $this->t = $test_model->otherFunction();

        // load view
        $this->renderView('testview');
    }

    public function f2() {
        echo "message from f2()";
    }

    public function threeargs($first, $second, $third) {
        echo "inside threeargs() <br/>";
        echo "First parameter:- $first <br/>";
        echo "Second parameter:- $second <br/>";
        echo "Third parameter:- $third <br/>";
        // print_r($this->paramList());
    }

    public function fourargs($a1, $a2, $a3, $a4) {
        echo "a1 - $a1 <br/>";
        echo "a2 - $a2 <br/>";
        echo "a3 - $a3 <br/>";
        echo "a4 - $a4 <br/>";
    }

    public function tpl() {
        //load model   
        $tpl_model = $this->loadModel('testTplModel');
        $this->dataFromModel = $tpl_model->tplModelMethod();
        $this->t = $tpl_model->testArray();

        // load views and template
        // variables are case sensitive
        $this->initView('testTplView');
        $this->view->setVar('title', 'my title');
        $this->view->setVar('sample', 'sample content');
        $this->view->setVar('modelData', $this->dataFromModel);
        $this->view->setVar('testArray', $this->t);
        $this->renderView();
    }

}
