<?php

/**
* Connect to the db and then create either an HTML or JSON page
* @author Mario Nemecek
*/

include "config/config.php";

$recordSet = new JSONRecordSet($ini['main']['database']['dbname']);

$page = new Router($recordSet);
new View($page);

?>