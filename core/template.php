<?php

class Template {
    /*
     * store template file
     */

    private $tpl;
    /*
     * store matches
     */
    private $match;
    /*
     * construct output string for {{loop}}
     */
    private $loopString;
    /*
     * used for {{loops}} having associative array
     */
    private $loopLine;
    /*
     * store values of varibles
     */
    private $values;

    /*
     * check whether file exists or not. 
     */

    function __construct($file) {

        if (file_exists($file)) {
            $this->tpl = file_get_contents($file);
        } else {
            echo "template not found $file";
        }
    }

    /*
     * set a new variable
     */

    public function setVar($searchString, $replaceString) {
        if (!empty($searchString)) {
            $this->values[$searchString] = $replaceString;
        }
    }

    /*
     * process the template
     */

    public function processTemplate() {
        /*
         * patterns for normal string and loop
         */
        $string = preg_match_all('~\{{(\w+)\}}~', $this->tpl);
        $arrayKey = preg_match_all('~\{{(\w.+)\}}~', $this->tpl, $keyMatches, PREG_SET_ORDER);
        $loop = preg_match_all('#\\{{loop\\}}(.*?)\\{{/loop\\}}#s', $this->tpl, $matches, PREG_SET_ORDER);

        if (count($this->values) != 0) {
            /*
             * if the variable is a string, replace variable name by it's value
             */
            if ($string) {
                foreach ($this->values as $key => $value) {
                    if (!is_array($value)) {
                        $this->tpl = str_replace('{{' . $key . '}}', $value, $this->tpl);
                    }
                }
            }

            /*
             * if araay with keys
             */
            if ($arrayKey) {
                for ($i = 0; $i < $arrayKey; $i++) {
                    preg_match_all('~\{{(\w.+)\}}~', $keyMatches[$i][1], $keyLoopStack, PREG_SET_ORDER);
                    $this->match = $keyMatches[$i][0];
                    $originalArrayName = str_replace(array('{{', '}}'), '', $this->match);
                    /*
                     * get the attributes from array name
                     */
                    $arrayName = current(explode('.', $originalArrayName));
                    /*
                     * reset loopString to avoid repetition
                     */
                    $this->loopString = null;
                    if (isset($this->values["$arrayName"])) {
                        if (is_array($this->values["$arrayName"])) {
                            $this->loopLine = $this->match;
                            /*
                             * fetch keys and corresponding values from array
                             */
                            foreach ($this->values["$arrayName"] as $key => $value) {
                                if (!is_array($value)) {

                                    $this->loopLine = str_replace('{{' . $arrayName . '.' . $key . '}}', $value, $this->loopLine);
                                }
                            }
                            /*
                             * append each values to loopLine
                             */
                            $this->loopString .= $this->loopLine;
                        }
                    }
                    /*
                     * update template file with replaced values
                     */
                    $this->tpl = str_replace($this->match, $this->loopString, $this->tpl);
                }
            }



            /*
             * check the existence of {{loop}} in the template
             */
            if ($loop) {
                for ($i = 0; $i < $loop; $i++) {
                    /*
                     * fetch variables inside {{loop}} and {{/loop}}
                     */
                    preg_match_all('~\{{(\w.+)\}}~', $matches[$i][1], $loopStack, PREG_SET_ORDER);
                    $this->match = $matches[$i][1];
                    $originalArrayName = str_replace(array('{{', '}}'), '', $loopStack[0][0]);
                    /*
                     * get the names of array variables
                     */
                    $arrayName = current(explode('.', $originalArrayName));
                    $this->loopString = null;
                    if (is_array($this->values["$arrayName"])) {
                        foreach ($this->values["$arrayName"] as $value) {
                            if (is_array($value)) {
                                /*
                                 * this is an associative array
                                 */
                                $valCount = preg_match_all('~\{{(\w.+)\}}~', $matches[$i][1], $loopStack, PREG_SET_ORDER);
                                $this->loopLine = $this->match;
                                for ($j = 0; $j < $valCount; $j++) {
                                    /*
                                     * fetch attributes of associative array
                                     */
                                    foreach ($value as $k => $v) {

                                        $this->loopLine = str_replace('{{' . $arrayName . '.' . $k . '}}', $v, $this->loopLine);
                                    }
                                }

                                $this->loopString .= $this->loopLine;
                            } else {
                                /*
                                 * this is a normal array
                                 */
                                $this->loopString .= str_replace('{{' . $arrayName . '}}', $value, $this->match);
                            }
                        }
                        /*
                         * replace the variables in template file with corresponding values 
                         */
                        $this->tpl = str_replace($this->match, $this->loopString, $this->tpl);
                        $this->tpl = str_replace(array('{{loop}}', '{{/loop}}'), '', $this->tpl);
                    }
                }
            }
        }
        /*
         * print the output
         */
        echo $this->tpl;
    }

}
