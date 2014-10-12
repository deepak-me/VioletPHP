<?php

class Database {

    private $dbEngine;

    function __construct() {

        $dbDriverType = DB_TYPE;
        $dbDriverType = strtolower($dbDriverType);
        $dbDriverType = $dbDriverType . 'Driver';
        $file = "core/dbDrivers/" . $dbDriverType . ".php";
        if (file_exists("$file")) {
            require_once $file;
            $dbEngine = new $dbDriverType();
            $this->dbEngine = $dbEngine;
        } else {
            echo "<b>file not found!</b>";
        }
    }

    public function __call($method, $args) {
        if (empty($this->dbEngine)) {
            return 0;
        }
        if (!method_exists($this, $method)) {
            return call_user_func_array(array($this->dbEngine, $method), $args);
        }
    }

    public function __get($property) {
        if (property_exists($this->dbEngine, $property)) {
            return $this->dbEngine->$property;
        }
    }

}
