<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");


    $post = json_decode($_POST);
    $logFile = fopen("./log.log", "w");
    $txt = "$post -- ".date('Y-m-d H:i:s',time())."\n";
    fwrite($logFile, $txt);
    fclose($logFile); 
echo true;