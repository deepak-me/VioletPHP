<?php

class dbModel {

    function __construct() {
        
    }

    public function getDataFromDb() {
      
        //new database connection
         $db = new Database();

        //execute query
       // $db->executeQuery("INSERT INTO cars(id,name,price) VALUES('9','maruti','5400')");
       //$db->executeQuery("INSERT INTO test_table(name,email) VALUES('kumaran','kumaran@gmail.com')");
      // $db->executeQuery("SELECT * FROM cars");
       // $db->executeQuery("DELETE FROM test_table WHERE name='gfdg' OR name='tty' ");
      //  $db->executeQuery("DELETE FROM test_table WHERE name='kumaran'");

        //return result array
        // return $db->getAllRows(); // ok
       // return $db->getRows(3,3); // ok
       // return $db->getRowAt(2); // ok
        //return $db->getRow(); // ok 
       //  print_r($db->affectedRows()); //ok[not for select]
        //return $db->count(); // ok
       // print_r($db->lastInsertId()); // ok
       /*
        * to escape string
        *  $email="kumaran@gmail.com";
        *  $db->escapeString($email);
        */
    }

    public function getDataFromDb2() {

        $a = Array("country" => "india", "state" => "kerala", "district" => "kochi", "location" => "perumbavoor");
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
