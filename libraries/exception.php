<?php

class ExceptionHandler {

    function __construct($exceptionCode) {
        //echo "Inside exception handler<br/>";
        switch ($exceptionCode) {
            case "100": echo "Controller not found.";
                break;
            case "101": echo "Method not found.";
                break;
            case "102": echo "Number of parameters does not match.";
                break;
        }
    }

}
