<?php
class testTplModel {

    function __construct() {
        
    }
    public function tplModelMethod()
    {
        $a = 100;
        return $a;
    }
    public function testArray()
    {
        // $t = Array("controller"=>"this is controller" ,"model"=>"this is model");
        $t = Array("first","second","third");
        return $t;
    }

}