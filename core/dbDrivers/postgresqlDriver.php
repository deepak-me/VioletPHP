<?php

class postgresqlDriver extends dbDriver {

    function __construct() {
        $dbType = DB_TYPE;
        $dbHost = DB_HOST;
        $dbUser = DB_USER;
        $dbPass = DB_PASS;
        $dbName = DB_NAME;
        $dbPort = DB_PORT;
        if (!empty($dbType) and ! empty($dbHost) and ! empty($dbPort) and ! empty($dbUser) and ! empty($dbPass) and ! empty($dbName)) {
            if (!$this->connection = pg_connect("host=$dbHost port=$dbPort dbname=$dbName user=$dbUser password=$dbPass")) {
                echo pg_last_error($this->connection);
            }
        } else {
            echo "enter username, password, host , port in configs";
            exit(0);
        }
    }

    public function count() {
        $lastResult = $this->results[$this->lastHash];
        $count = pg_num_rows($lastResult);
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    public function prepareQuery($sql) {
        //delete returns 0 affected row. this fix it
        if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql)) {
            $sql = preg_replace("/^\s*DELETE\s+FROM\s+(\S+)\s*$/", "DELETE FROM \\1 WHERE 1=1", $sql);
        }
        return $sql;
    }

    public function escapeString($sql) {
        if (function_exists('pg_escape_string')) {
            return pg_escape_string($sql);
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
                if (is_resource($this->results[$hash]))
                    return $this->results[$hash];
            }
        }
        else if ("update" == $type || "delete" == $type) {
            $this->results = array(); //clear the result cache
        }
        $query = pg_query($this->connection, $sql);
        if (!$query) {
            echo pg_errormessage($this->connection);
        }
        $this->results[$hash] = $query;
        return $this->results[$hash];
    }

    public function affectedRows() {
        $lastResult = $this->results[$this->lastHash];
        return pg_affected_rows($lastResult);
    }

    public function lastInsertId() {
        $_temp = $this->lastHash;
        $lastResult = $this->results[$this->lastHash];
        $this->executeQuery("SELECT version() AS ver");
        $row = $this->getRow();
        $ver = pg_version($this->connection);
        $v = $ver['server'];
        $table = func_num_args() > 0 ? func_get_arg(0) : null;
        $column = func_num_args() > 1 ? func_get_arg(1) : null;
        if ($table == null && $v == '8.1') {
            $sql = "SELECT LASTVAL() AS ins_id";
        } elseif ($table != null && $column != null && $v >= '8.0') {
            $sql = sprintf("SELECT pg_get_serial_sequence('%s','%s') as seq", $table, $column);
            $this->execte($sql);
            $row = $this->getRow();
            $sql = sprintf("SELECT CURRVAL('%s') as ins_id", $row['seq']);
        } elseif ($table != null) {
            // seq_name passed in table parameter
            $sql = sprintf("SELECT CURRVAL('%s') as ins_id", $table);
        } else {
            return pg_last_oid($lastResult);
        }
        $this->execute($sql);
        $row = $this->getRow();
        $this->lasthash = $_temp;
        return $row['ins_id'];
    }

    public function beginTransaction() {
        return pg_exec($this->connection, "BEGIN");
    }

    public function commitTransaction() {
        return pg_exec($this->connection, "COMMIT");
    }

    public function rollbackTransaction() {
        return pg_exec($this->connection, "ROLLBACK");
    }

    public function rewind() {
        $lastResult = $this->results[$this->lastHash];
        pg_result_seek($lastResult, 0);
    }

    public function getRow($fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        if ($fetchmode == 'FETCH_ASSOC') {
            $row = pg_fetch_assoc($lastResult);
        } elseif ($fetchmode == 'FETCH_ROW') {
            $row = pg_fetch_row($lastResult);
        } elseif ($fetchmode == 'FETCH_OBJECT') {
            $row = pg_fetch_object($lastResult);
        } else {
            $row = pg_fetch_array($lastResult, PGSQL_BOTH);
        }
        return $row;
    }

    public function getRowAt($offset = null, $fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        if (!empty($offset)) {
            pg_result_seek($lastResult, $offset);
        }
        return $this->getRow($fetchmode);
    }

    public function getRows($start, $count, $fetchmode = 'FETCH_ASSOC') {
        $lastResult = $this->results[$this->lastHash];
        pg_result_seek($lastResult, $start);
        $rows = Array();
        for ($i = $start; $i <= ($start - 1 + $count); $i++) {
            $rows[] = $this->getRow($fetchmode);
        }
        return $rows;
    }

    public function getAllRows($fetchmode = 'FETCH_ASSOC') {
        $start = 0;
        $count = $this->count();
        $lastResult = $this->results[$this->lastHash];
        pg_result_seek($lastResult, $start);
        $rows = Array();
        for ($i = $start; $i < ($start + $count); $i++) {
            $rows[] = $this->getRow($fetchmode);
        }
        return $rows;
    }

}
