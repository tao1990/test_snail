<?php

require_once(dirname(__FILE__)."/../data/common.inc.php");
$conn=new mysqli($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$cfg_dbname,3306);  

if(!$conn){  
    header('HTTP/1.1 500 DBERROR');
    exit('{}');
}  


?>