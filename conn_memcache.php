<?php

$mem = new Memcache ();
if(!$mem->connect('127.0.0.1',11211)){  
    header('HTTP/1.1 500 DBERROR');
    exit('{}');
}  


?>