<?php

/**
* Create a singleton SQLite database connection
* @author Mario Nemecek
*/

class PDOdb {
    private static $dbConnection = null;
    
    private function __construct() {
    }
    
    /**
    * Return a database connection or create an initial connection
    * @return PDO object
    */
    
    public static function getConnection($dbname) {
        if(!self::$dbConnection) {
            self::$dbConnection = new PDO("sqlite:".$dbname);
            self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$dbConnection;
    }
}

?>