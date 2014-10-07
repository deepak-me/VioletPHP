<?php

class Template {

    private $tpl;
    private $file;
    private $match;
    private $loopString;
    private $loopLine;

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

    public function processData() {
        $string = preg_match_all('~\{{(\w+)\}}~', $this->tpl);
        $loop = preg_match_all('#\\{{loop\\}}(.*?)\\{{/loop\\}}#s', $this->tpl, $matches, PREG_SET_ORDER);

        if (count($this->values) != 0) {

            if ($string) {
                foreach ($this->values as $key => $value) {
                    if (!is_array($value)) {
                        $this->tpl = str_replace('{{' . $key . '}}', $value, $this->tpl);
                    }
                }
            }
            if ($loop) {
                for ($i = 0; $i < $loop; $i++) {
                    preg_match_all('~\{{(\w.+)\}}~', $matches[$i][1], $loopStack, PREG_SET_ORDER);
                    $this->match = $matches[$i][1];
                    $originalArrayName = str_replace(array('{{', '}}'), '', $loopStack[0][0]);
                    $arrayName = current(explode('.', $originalArrayName));
                    $this->loopString = null;
                    if (is_array($this->values["$arrayName"])) {
                        foreach ($this->values["$arrayName"] as $value) {
                            if (is_array($value)) {
                                $valCount = preg_match_all('~\{{(\w.+)\}}~', $matches[$i][1], $loopStack, PREG_SET_ORDER);
                                $this->loopLine = $this->match;
                                for ($j = 0; $j < $valCount; $j++) {
                                    $attribute = explode('.', $loopStack[$j][1]);
                                    $attribute = end($attribute);
                                    $this->loopLine = str_replace('{{' . $loopStack[$j][1] . '}}', $value[$attribute], $this->loopLine);
                                }
                                $this->loopString .= $this->loopLine;
                            } else { // normal array
                                $this->loopString .= str_replace('{{' . $arrayName . '}}', $value, $this->match);
                            }
                        }
                        $this->tpl = str_replace($this->match, $this->loopString, $this->tpl);
                        $this->tpl = str_replace(array('{{loop}}', '{{/loop}}'), '', $this->tpl);
                    }
                } // end of for loop
            } // end of loop function(condition)
        }
        echo $this->tpl; // printing output
    }

}
