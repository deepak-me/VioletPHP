<?php
class Index extends Controller {

    function __construct() {
        parent::__construct(); 
    }
    
    public function index()
    {
        //load default view
        $this->renderView('Index');
    }

}