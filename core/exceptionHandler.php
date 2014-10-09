<?php

class ExceptionHandler {

    function __construct($exceptionCode, $exceptionData) {
        if (DEBUG_MODE == 'on') {
            switch ($exceptionCode) {
                case "100": echo 'Controller <b>' . $exceptionData['controller'] . ' </b>not found.';
                    break;
                case "101": echo 'Method <b>' . $exceptionData['method'] . ' </b> not found in the <b>' . $exceptionData['controller'] . '</b> controller';
                    break;
                case "102": echo 'Method <b>' . $exceptionData['method'] . '</b> requires <b>' . $exceptionData['requiredParams'] . '</b> parameter(s). Currently passing <b>' . $exceptionData['passingParams'] . '</b> parameter(s)';
                    break;
                case "103": echo 'Index method not found in <b>'.$exceptionData['controller'].'</b> controller';
                    break;
            }
        } else {
            ob_start();
            header('Location:' . BASE_URL . '' . PAGE_404);
        }
    }

}
