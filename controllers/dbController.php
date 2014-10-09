<?php

class dbController extends Controller {

    function __construct() {
        parent::__construct();
    }
    public function dbTest()
    {
        //load model   
        $this->loadModel('dbModel');
        $this->dataFromModel = $this->model->getDataFromDb();
        $this->dataFromModel2 = $this->model->getDataFromDb2();

        // set header
        $this->initView('dbView');
        $this->view->setVar('dbValue',  $this->dataFromModel);
        $this->view->setVar('dbValue2', $this->dataFromModel2);
        $this->renderView();

    }
    public function getTest($value1,$value2)
    {
        //load model
        $this->loadModel('dbModel');
        
        //load view
        $this->initView('dbView');
        $this->view->setVar('dbValue', $this->model->getGetValue($value1,$value2));
        $this->renderView();
    }
    
    public function postTest()
    {
        //load model
        $this->loadModel('dbModel');   
        
        //load view
        $this->initView('dbView');
        $this->view->setVar('dbValue', $this->model->getPostValue());
        $this->renderView();
        
    }
   
    public function postTest2()
    {
        //load model
        $this->loadModel('dbModel');   
        
        //load view
        $this->initView('postView');
        $this->view->setVar('value', $this->model->getPostValue());
        $this->renderView();
    }

}