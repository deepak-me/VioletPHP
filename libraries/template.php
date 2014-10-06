<?php

class Template {

    private $values = Array();
    private $tpl;
    private $file;
                function __construct($file) {

        if (file_exists($file)) {
            $this->tpl = file_get_contents($file);
            $this->file = $file;
        } else {
            echo "template not found";
        }
    }

    public function setVar($searchString, $replaceString) {
        if (!empty($searchString)) {
            $this->values[$searchString] = $replaceString;
        }
    }

    public function showVar() {
       // print_r($this->values);
        if (count($this->values) != 0) {
            foreach ($this->values as $key => $value) {
                
                $this->tpl = str_replace('{{' . $key . '}}', json_encode($value), $this->tpl);
            }
        }
        echo $this->tpl;

    }

}
