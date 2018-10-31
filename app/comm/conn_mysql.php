<?php

//数据库连接信息
$cfg_dbtype = 'mysql';
$cfg_dbhost = '10.10.20.43';
//$cfg_dbname = 'dedecmsv57utf8sp2';
$cfg_dbname = 'snail';
$cfg_dbuser = 'root';
$cfg_dbpwd = 'root';
$cfg_dbprefix = 'snail_';
$cfg_db_language = 'utf8';

$conn=new mysqli($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$cfg_dbname,3306);

if(!$conn){
    header('HTTP/1.1 500 DBERROR');
    exit('{}');
}


?>
