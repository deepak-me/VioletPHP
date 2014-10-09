<?php

class mysqlDriver extends dbDriver {

    function __construct() {
        $dbType = DB_TYPE;
        $dbHost = DB_HOST;
        $dbUser = DB_USER;
        $dbPass = DB_PASS;
        $dbName = DB_NAME;
        if (!empty($dbType) and ! empty($dbHost) and ! empty($dbUser) and ! empty($dbPass) and ! empty($dbName)) {

            if (!$this->connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
                echo mysqli_connect_error();
            }
        } else {
            echo "enter username, password, host in configs";
            exit(0);
        }
    }

    public function count() {
        if (isset($this->results[$this->lastHash])) {
            $lastResult = $this->results[$this->lastHash];
            $count = mysqli_num_rows($lastResult);
        } else {

            $count = 0;
        }
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    public function prepareQuery($sql) {
        //delete from table returns 0 affected rows. need fix
        if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql)) {
            $sql = preg_replace("/^\s*DELETE\s+FROM\s+(\S+)\s*$/", "DELETE FROM \\1 WHERE 1=1", $sql);
        }
        return $sql;
    }

    public function escapeString($sql) {
        if (function_exists('mysqli_real_escape_string')) {
            return mysqli_real_escape_string($this->connection, $sql);
        } elseif (function_exists('mysqli_escape_string')) {
            return mysqli_escape_string($this->connection, $sql);
        } else {
            return addslashes($sql);
        }
    }

    public function executeQuery($sql) {
        //prepare query
        $sql = $this->prepareQuery($sql);

        $parts = explode(" ", trim($sql));
        $type = strtolower($parts[0]);
        $hash = md5($sql);
        $this->lastHash = $hash;

        if ($type == "select") {
            if (isset($this->results[$hash])) {
                if (is_resource($this->results[$hash])) {
                    return $this->results[$hash];
                }
            }
        } elseif ($type == "update" || $type == "delete") {
            $this->results = Array(); // clear the result cache
        }
        $query = mysqli_query($this->connection, $sql);
        if (!$query) {
            echo mysqli_error($this->connection);
        }
        $this->results[$hash] = $query;
        return $this->results[$hash];
    }

    public function affectedRows() {
        return mysqli_affected_rows($this->connection);
    }

    public function lastInsertId() {
        return mysqli_insert_id($this->connection);
    }

    public function beginTransaction() {
        mysqli_autocommit($this->connection, false);
        mysqli_begin_transaction($this->connection);
        return true;
    }

    public function commitTransaction() {
        mysqli_commit($this->connection);
        return true;
    }

    public function rollbackTransaction() {
        mysqli_rollback($this->connection);
        return true;
    }

    public function rewind() {
        $lastResult = $this->results[$this->lastHash];
        mysqli_data_seek($lastResult, 0);
    }

    public function getRow($fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        if ($fetchmode == 'FETCH_ASSOC') {
            $row = mysqli_fetch_assoc($lastResult);
        } elseif ($fetchmode == 'FETCH_ROW') {
            $row = mysqli_fetch_row($lastResult);
        } elseif ($fetchmode == 'FETCH_OBJECT') {
            $row = mysqli_fetch_object($lastResult);
        } else {
            $row = mysqli_fetch_array($lastResult, MYSQLI_BOTH);
        }
        return $row;
    }

    public function getRowAt($offset = null, $fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        if (!empty($offset)) {
            mysqli_data_seek($lastResult, $offset);
        }
        return $this->getRow($fetchmode);
    }

    public function getRows($start, $count, $fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        mysqli_data_seek($lastResult, $start);
        $rows = Array();
        for ($i = $start; $i <= ($start + $count); $i++) {
            $rows[] = $this->getRow($fetchmode);
        }
        return $rows;
    }

    public function getAllRows($fetchmode = 'FETCH_ASSOC') {
        $start = 0;
        $count = $this->count();
        $lastResult = $this->results[$this->lastHash];
        mysqli_data_seek($lastResult, $start);
        $rows = Array();
        for ($i = $start; $i < ($start + $count); $i++) {
            $rows[] = $this->getRow($fetchmode);
        }
        return $rows;
    }

}
