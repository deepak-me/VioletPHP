<?php

class queryBuilder {

    private $connection;
    private $query;
    private $select = array();
    private $table;
    private $where = array();
    private $and = array();
    private $or = array();
    private $prepare;
    private $bindParameters = array();
    private $insertPartition = array();
    private $values = array();
    private $set = array();
    private $updateTable;
    private $deleteTable;
    private $offset;
    private $count;
    private $columnName;

    function __construct() {
        $this->query = "";
        $this->connection = new PDO('mysql:host=localhost;dbname=test', 'deepak', 'password');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function select($select) {
        $this->select = func_get_args();
        return $this;
    }

    public function from($from) {
        $this->table = $from;
        return $this;
    }

    public function where($where) {
        $this->where = &func_get_args();
        return $this;
    }

    public function withAnd($and) {
        $this->and[][] = &func_get_args();
        return $this;
    }

    public function withOr($or) {
        $this->or[][] = func_get_args();
        return $this;
    }

    public function limitOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function limitCount($count) {
        $this->count = $count;
        return $this;
    }

    public function insertTo($table) {
        $this->table = $table;
        return $this;
    }

    public function columns() {
        $this->insertPartition = func_get_args();
        return $this;
    }

    public function values() {
        $this->values = func_get_args();
        return $this;
    }

    public function update($table) {
        $this->updateTable = $table;
        return $this;
    }

    public function set() {
        $this->set[][] = func_get_args();
        return $this;
    }

    public function deleteFrom($table) {
        $this->deleteTable = $table;
        return $this;
    }
    public function groupBy($column)
    {
        $this->columnName = $column;
        return $this;
    }

    public function execute() {

        /*
         * select command
         */
        if (!empty($this->select)) {
            $this->query = "SELECT " . implode(",", $this->select) . " FROM {$this->table}";

            if (!empty($this->where)) {
                $this->query .= " WHERE " . $this->where[0] . " " . $this->where[1] . " " . "?";
                $this->bindParameters[] = $this->where[2];
            }
            if (!empty($this->and)) {
                foreach ($this->and as $and) {
                    $this->query .= " AND " . $and[0][0] . " " . $and[0][1] . " " . "?";
                    $this->bindParameters[] = $and[0][2];
                }
            }
            if (!empty($this->or)) {
                foreach ($this->or as $or) {
                    $this->query .= " OR " . $or[0][0] . " " . $or[0][1] . " " . "?";
                    $this->bindParameters[] = $or[0][2];
                }
            }

            if (!empty($this->offset) or ! empty($this->count)) {
                $this->query .= " LIMIT ";
            }
            if (!empty($this->offset)) {
                $this->query .= $this->offset.",";
              //  $this->query .= "?, ";
               // $this->bindParameters[] = intval($this->offset);
            }
            if (!empty($this->count)) {
                $this->query .= $this->count;
              //  $this->query .= " ? ";
              //  $this->bindParameters[] = $this->count;
            }

           // echo "$this->query";
        }
        /*
         * insert command
         */ elseif (!empty($this->table)) {
            $this->query = "INSERT INTO {$this->table} ";

            if (!empty($this->insertPartition)) {
                $this->query .= "(" . implode(',', $this->insertPartition) . ")";
            }

            if (!empty($this->values)) {
                $this->query .= " VALUES " . "(";
                foreach ($this->values as $value) {
                    $this->bindParameters[] = $value;
                    $this->query .= "? ,";
                }
                $this->query = rtrim($this->query, ',');
                $this->query .= ")";
            }
        }
        /*
         * update command
         */ elseif (!empty($this->updateTable)) {
            $this->query = "UPDATE {$this->updateTable} ";

            if (!empty($this->set)) {
                $this->query .="SET ";
                foreach ($this->set as $set) {
                    $this->bindParameters[] = $set[0][1];
                    $this->query .= $set[0][0] . "= ?, ";
                }
                $this->query = rtrim($this->query, ', ');
            }
            if (!empty($this->where)) {
                $this->query .= " WHERE " . $this->where[0] . " " . $this->where[1] . " " . "?";
                $this->bindParameters[] = $this->where[2];
            }
            if (!empty($this->and)) {
                foreach ($this->and as $and) {
                    $this->query .= " AND " . $and[0][0] . " " . $and[0][1] . " " . "?";
                    $this->bindParameters[] = $and[0][2];
                }
            }
            if (!empty($this->or)) {
                foreach ($this->or as $or) {
                    $this->query .= " OR " . $or[0][0] . " " . $or[0][1] . " " . "?";
                    $this->bindParameters[] = $or[0][2];
                }
            }
        }
        /*
         * delete command
         */ elseif (!empty($this->deleteTable)) {

            $this->query = "DELETE FROM {$this->deleteTable} ";

            if (!empty($this->where)) {
                $this->query .= " WHERE " . $this->where[0] . " " . $this->where[1] . " " . "?";
                $this->bindParameters[] = $this->where[2];
            }
            if (!empty($this->and)) {
                foreach ($this->and as $and) {
                    $this->query .= " AND " . $and[0][0] . " " . $and[0][1] . " " . "?";
                    $this->bindParameters[] = $and[0][2];
                }
            }
            if (!empty($this->or)) {
                foreach ($this->or as $or) {
                    $this->query .= " OR " . $or[0][0] . " " . $or[0][1] . " " . "?";
                    $this->bindParameters[] = $or[0][2];
                }
            }
        }
        /*
         *  prepare query
         */
        $this->prepare = $this->query;
        self::prepare();
    }

    public function prepare() {
         print_r($this->prepare);

        try {
                $stmt = $this->connection->prepare($this->prepare);
                $stmt->execute($this->bindParameters);
                
              $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
              print_r($rows);
        } catch (PDOException $e) {
            trigger_error('Wrong SQL: ' . $this->prepare . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

}

$make = "suzuki";
$model = "kizashi";
$newcolor = "green";
$color = "red";
$id = '15';
$i = '2';
$j = '3';        

$ob = new queryBuilder();
//$ob->select("*")->from("cars")->where("make", "=", "$make")->execute();
//------------------------------------------------------------------
$ob->select("id", "make", "model", "color")
        ->from("cars")
      //  ->where("make", "=", "$make")
    //    ->limitOffset("$i")
        ->limitCount("$j")
//        ->withAnd("color", "=", "$color")
//        ->withAnd("id", ">", "$id")
        ->execute();
//---------------------------------------------------------------
//$ob->insertTo("cars")
//        ->columns("make", "model", "color")
//        ->values("$make", "$model", "$color")
//        ->execute();
//-----------------------------------------------------------------
//$ob->update("cars")
//        ->set("model", "$model")
//        ->set("make", "$make")
//        ->set("color", "$newcolor")
//        ->where("id", "=", "$id")
//        ->withAnd("color", "=", "$color")
//        ->execute();
//-----------------------------------------------------------------
//$ob->deleteFrom("cars")
//        ->where("make", "=", "$make")
//        ->withAnd("color","=","$color")
//        ->execute();
