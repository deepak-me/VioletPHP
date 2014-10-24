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
    private $limitCount;
    private $columnName;
    private $orderColumnName;
    private $havingSum = array();
    private $havingCount = array();
    private $havingMin = array();
    private $havingMax = array();
    private $havingAvg = array();
    private $count;
    private $min;
    private $max;
    private $sum;
    private $avg;

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
        $this->limitCount = $count;
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

    public function groupBy($column) {
        $this->columnName = $column;
        return $this;
    }

    public function orderByAsc($column) {
        $this->orderColumnName = $column . " ASC ";
        return $this;
    }

    public function orderByDesc($column) {
        $this->orderColumnName = $column . " DESC ";
        return $this;
    }

    public function havingSum($column) {
        $this->havingSum = &func_get_args();
        return $this;
    }

    public function havingCount($column) {
        $this->havingCount = &func_get_args();
        return $this;
    }

    public function havingMin($column) {
        $this->havingMin = &func_get_args();
        return $this;
    }

    public function havingMax($column) {
        $this->havingMax = &func_get_args();
        return $this;
    }

    public function havingAvg($column) {
        $this->havingAvg = &func_get_args();
        return $this;
    }

    public function count($column) {
        $this->count = $column;
        return $this;
    }

    public function sum($column) {
        $this->sum = $column;
        return $this;
    }

    public function min($column) {
        $this->min = $column;
        return $this;
    }

    public function max($column) {
        $this->max = $column;
        return $this;
    }

    public function avg($column) {
        $this->avg = $column;
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

            if (!empty($this->offset) or ! empty($this->limitCount)) {
                $this->query .= " LIMIT ";
            }
            if (!empty($this->offset)) {
                $this->query .= $this->offset . ",";
                //  $this->query .= "?, ";
                // $this->bindParameters[] = intval($this->offset);
            }
            if (!empty($this->limitCount)) {
                $this->query .= $this->limitCount;
                //  $this->query .= " ? ";
                //  $this->bindParameters[] = $this->count;
            }
            if (!empty($this->columnName)) {
                $this->query .= " GROUP BY " . $this->columnName;
            }
            if (!empty($this->havingSum)) {
                $this->query .= " HAVING SUM(" . $this->havingSum[0] . ") " . $this->havingSum[1] . " " . "?";
                $this->bindParameters[] = $this->havingSum[2];
            }
            if (!empty($this->havingCount)) {
                $this->query .= " HAVING COUNT(" . $this->havingCount[0] . ") " . $this->havingCount[1] . " " . "?";
                $this->bindParameters[] = $this->havingCount[2];
            }
            if (!empty($this->havingMin)) {
                $this->query .= " HAVING MIN(" . $this->havingMin[0] . ") " . $this->havingMin[1] . " " . "?";
                $this->bindParameters[] = $this->havingMin[2];
            }
            if (!empty($this->havingMax)) {
                $this->query .= " HAVING MAX(" . $this->havingMax[0] . ") " . $this->havingMax[1] . " " . "?";
                $this->bindParameters[] = $this->havingMax[2];
            }
            if (!empty($this->havingAvg)) {
                $this->query .= " HAVING AVG(" . $this->havingAvg[0] . ") " . $this->havingAvg[1] . " " . "?";
                $this->bindParameters[] = $this->havingAvg[2];
            }

            if (!empty($this->orderColumnName)) {
                $this->query .= " ORDER BY " . $this->orderColumnName;
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
        echo '<hr/>';
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
$id = '12';
$i = '2';
$j = '3';

$ob = new queryBuilder();
//$ob->select("*")->from("cars")->where("make", "=", "$make")->execute();
//------------------------------------------------------------------
//$ob->select("id", "make", "model", "color")
//$ob->select("*")
  $ob->select(count($column))      
        ->from("customers")
        //  ->where("id", "<", "$id")
        //    ->limitOffset("$i")
        //    ->limitCount("$j")
//        ->withAnd("color", "=", "$color")
//        ->withAnd("id", ">", "$id")
        ->groupBy("age")
        //       ->orderByAsc("model")
        ->havingCount("age", ">=", "2")
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
