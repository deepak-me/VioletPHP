<?php

class dbModel {

    private $navLinks;

    function __construct() {
        
    }

    public function pcount() {
        return $this->navLinks;
    }

    public function getDataFromDb($pageNumber) {

        //new database connection
        $db = new Database();
        // $start = 0;
        //  $limit = 5;
        //execute query
        // $db->executeQuery("INSERT INTO cars(id,name,price) VALUES('9','maruti','5400')");
        //$db->executeQuery("INSERT INTO test_table(name,email) VALUES('kumaran','kumaran@gmail.com')");
        //  $db->executeQuery("INSERT INTO fruits(name,color) VALUES('mango','orange')");
        //  $db->executeQuery("DELETE FROM fruits WHERE color='violet'");
        $db->executeQuery("SELECT * FROM first_names");
        // $db->executeQuery("DELETE FROM test_table WHERE name='gfdg' OR name='tty' ");
        //  $db->executeQuery("DELETE FROM test_table WHERE name='kumaran'");
        //return result array
        // $db->getAllRows(); // ok
        $page = new Paginator($db->getAllRows(),10); //array , limit, pages count [optional. default is 10]
        $rows = $page->getPage($pageNumber);
        $this->navLinks = $page->pageCount();
        return $rows;

        // print_r ($db->getRows(1,3)); //ok
        //  print_r($db->getRowAt(1)); // ok 
        //  print_r ($db->getRow()); // return first row
        // print_r($db->affectedRows());  // ok
        //  print_r($db->count());  // ok
        //  print_r($db->lastInsertId());  //ok
        /*
         * to escape string
         *  $email="kumaran@gmail.com";
         *  $db->escapeString($email);
         */
    }

    public function dataSimpleArray() {
        $a = Array("orange", "apple", "grapes");
        return $a;
    }

    public function dataKeyArray() {
        $a = Array("id" => "10", "name" => "kumaran");
        return $a;
    }

    public function getDataFromDb2() {
$db = new Database();
 $db->executeQuery("SELECT * FROM cars");
 return $db->getAllRows();
 //       $a = NULL;
 //       $a = 100;
//        if (isset($a)) {
//            echo "set";
//        } else {
//            echo 'not set';
//        }
 //       return $a;
    }

    public function getDataFromDb3() {

        $a = NULL;
        $a = 100;
        return $a;
    }

    //------------------------------------------------------------------  
    public function getGetValue($value1, $value2) {
        $sum = $value1 + $value2;
        return $sum;
    }

    public function getPostValue() {
        /*
         * check if value is set 
         * otherwise it will show undefined index notice in debug mode
         */
        if (isset($_POST["myname"])) {  //if and isset not necessary
            $val = $_POST["myname"];
            return $val;
        }
    }

}
