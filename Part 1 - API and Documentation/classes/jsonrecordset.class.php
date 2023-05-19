<?php
/**
* Return a JSON recordset
* @author Mario Nemecek
*/

class JSONRecordSet extends RecordSet {
    
/**
 * function to return a record set in JSON format
 *
 * @param string $query the sql query as a string to retrieve the record set
 * @param array $params associative array of parameters for preparted statement 
 *
 * @return string  a json documnent
 */
    function getJSONRecordSet($query, $params = null) {
        $stmt = $this->getRecordSet($query, $params);
        $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nRecords = count($recordSet);
        return json_encode(array("count"=>$nRecords, "data"=>$recordSet));                 
    }
}

?>