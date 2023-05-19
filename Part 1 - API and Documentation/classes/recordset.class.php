<?php

/**
* Abstract class that creates a database connection and returns a recordset
* @author Mario Nemecek
*/

abstract class RecordSet {
    protected $conn;
    protected $stmt;
    
    function __construct($dbname){
        $this->conn = PDOdb::getConnection($dbname);
    }
    
    /**
    * This function will execute the query with prepared statement if there is a
    * params array. If not, it executes as a regular statement.
    * 
    * @params string $query sql query for the record set
    * @params array $params optional associative array for prepared statements
    * @return PDO_STATEMENT
    */
    function getRecordSet($query, $params = null) {
        if(is_array($params)) {
            $this->stmt = $this->conn->prepare($query);
            $this->stmt->execute($params);
        }
        else {
            $this->stmt = $this->conn->query($query);
        }
        return $this->stmt;
    }
}

?>