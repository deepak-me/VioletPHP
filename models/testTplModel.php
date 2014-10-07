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
       public function testArray1()
    {
        // $t = Array("controller"=>"this is controller" ,"model"=>"this is model");
        $t = Array("orange","apple","grape","mango");
        return $t;
    }
        public function testArray2()
    {
        // $t = Array("controller"=>"this is controller" ,"model"=>"this is model");
            $t=Array(Array("name"=>"deepak", "age"=>"23", "location"=>"kochi"),
                     Array("name"=>"kumaran", "age"=>"24", "location"=>"alappuzha"),
                     Array("name"=>"jango", "age"=>"30", "location"=>"thrissur")
                );
     //   $t = Array("orange","apple","grape");
        return $t;
    }

}