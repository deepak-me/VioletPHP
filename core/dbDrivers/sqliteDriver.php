<?php

class sqliteDriver extends dbDriver {

    protected $connection;
    protected $lastResult;

    function __construct() {
        $dbType = DB_TYPE;
        $dbName = DB_NAME;
        if (!empty($dbType) and !empty($dbName)) {

            try {
                $this->connection = new PDO('sqlite:' . $dbName);
            } catch (PDOException $ex) {
                $ex->getMessage();
            }
        } else {
            echo "enter database name  in configs";
            exit(0);
        }
    }

    public function count() { 
        if (isset($this->lastResult)) {
            $rows = $this->lastResult->fetchAll(PDO::FETCH_NUM);
            $count = count($rows);
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
        if (function_exists('addslashes')) {
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
        try {
            $query = $this->connection->query($sql);
            $this->lastResult = $query;
        } catch (PDOException $ex) {
            $ex->getMessage();
        }

        return $this->lastResult;
        //  return $this->results[$hash];
    }

    public function affectedRows() { // not working
        return $this->lastResult->rowCount();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function commitTransaction() {
        $this->connection->commit();
    }

    public function rollbackTransaction() {
        $this->connection->rollBack();
    }

    public function rewind() { 
     //not applicable
    }

    public function getRow($fetchmode = 'FETCH_ASSOC') {
        if ($fetchmode == 'FETCH_ASSOC') {
            $this->lastResult->setFetchMode(PDO::FETCH_ASSOC);
            $row = $this->lastResult->fetch();
        } elseif ($fetchmode == 'FETCH_ROW') {
            $this->lastResult->setFetchMode(PDO::FETCH_NUM);
            $row = $this->lastResult->fetch();
        } elseif ($fetchmode == 'FETCH_OBJECT') {
            $this->lastResult->setFetchMode(PDO::FETCH_OBJ);
            $row = $this->lastResult->fetchObject();
        } else {
            $this->lastResult->setFetchMode(PDO::FETCH_BOTH);
            $row = $this->lastResult->fetch();
        }
        return $row;
    }

    public function getRowAt($offset = null, $fetchmode = 'FETCH_ASSOC') {
        $row = Array();
        if (!empty($offset) or $offset >= 0) {
            $rows = $this->lastResult->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i<=$offset; $i++) {
             $row = $rows[$i];
            }
        }
        return $row;
    }

    public function getRows($start, $count, $fetchmode = 'FETCH_ASSOC') {
       // $rows = Array();
        $resultRows = $this->lastResult->fetchAll(PDO::FETCH_ASSOC);
            for ($i = $start; $i < ($start + $count); $i++) {
                if(isset($resultRows[$i]))
                {
                    $rows[] = $resultRows[$i];
                }
            }
        return $rows;
    }

    public function getAllRows($fetchmode = 'FETCH_ASSOC') {
        $this->lastResult->setFetchMode(PDO::FETCH_ASSOC);
        return $this->lastResult->fetchAll();
    }

}
