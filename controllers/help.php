<?php

class Help extends Controller {

//    function __construct() {
//        parent::__construct();
//        echo "we are inside help controller <br/>";
//    }

    public function myfunc($arg1, $arg2) {
        //  echo "Myfunc method from help controller <br/>";
        //load model
        $help_model = $this->loadModel('HelpModel');
        $this->sum_numbers = $help_model->computeSum($arg1, $arg2);

        //load view
        $this->renderView('HelpView');
    }

    public function func2() {
        echo "func2 without args <br/>";
    }

}
