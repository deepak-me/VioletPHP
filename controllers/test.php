<?php

class Test extends Controller {

    function __construct() {
        parent::__construct();
        // echo "we are inside test controller <br/>";
    }

    public function index() {
        echo "message from index() of TestController<br/>";

        // load model
        $this->loadModel('TestModel');
        $this->t = $this->model->otherFunction();

        // load view
        $this->initView('testview');
        $this->view->setVar('value', $this->t);
        $this->renderView();
        //$this->renderView('testview');
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
        $this->loadModel('testTplModel');
        $this->dataFromModel = $this->model->tplModelMethod();

        // set header
        $this->initView('tplHeader');
        $this->view->setVar('title', 'thi is my title');
        $this->renderView();

        // load views and template
        $this->initView('testTplView');
        // set variables. variables are case sensitive
        $this->view->setVar('sample', 'sample content');
        $this->view->setVar('modelData', $this->dataFromModel);
        $this->view->setVar('testArray', $this->model->testArray());
        $this->view->setVar('testArray1', $this->model->testArray1());
        $this->view->setVar('testArray2', $this->model->testArray2());
        $this->view->setVar('testArray3', $this->model->testArray3());
        // render view
        $this->renderView();

        // set footer
        $this->initView('tplFooter');
        $this->view->setVar('footerVar', 'footer content');
        $this->renderView();
    }

}
