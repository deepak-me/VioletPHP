<?php
class indexController extends VioletController {

    function __construct() {
        
    }
    public function Index()
    {
        //load model
        $indexObject = $this->LoadModel('indexModel');
        $this->var1 = $indexObject->functionOne();
        $this->var2 = $indexObject->functionTwo($arg1 , $arg2);
        
        //load view
        $this->renderView('indexView');
    }

}