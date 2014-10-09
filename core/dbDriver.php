<?php

abstract class dbDriver {

    protected $connection;
    protected $results = Array();
    protected $lastHash = "";

    public function count() {
        return 0;
    }

    public function prepareQuery($sql) {
        return $sql;
    }

    public function escapeString($sql) {
        return $sql;
    }

    public function executeQuery($sql) {
        return $sql;
    }

    public function affectedRows() {
        return 0;
    }

    public function lastInsertId() {
        return 0;
    }

    public function beginTransaction() {
        return false;
    }

    public function commitTransaction() {
        return false;
    }

    public function rollbackTransaction() {
        return false;
    }

    public function rewind() {
        return false;
    }

    public function getRow($fetchmode = 'FETCH_ASSOC') {
        return Array();
    }

    public function getRowAt($offset = null, $fetchmode = 'FETCH_ASSOC') {
        return Array();
    }

    public function getRows($start, $count, $fetchmode = 'FETCH_ASSOC') {
        return Array();
    }

    public function getAllRows($fetchmode = 'FETCH_ASSOC') {
        return Array();
    }

}
