<?php
/**
 * zone_intro` api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = ZONE_INTRO_ID;
$cache_id = 'neso2017_zone_intro';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list = getZoneIntro($id);
    
    $resArr = array(
        'list'=>$list,
    );
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();


function getZoneIntro($id){
  
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY b.weight DESC;";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        
        $arr['title'] = $row['title'];
        //$arr['body'] = preg_replace('/[ ]/', '',$row['body']);
        $arr['body'] = $row['body'];
        
        $list[] = $arr;
    }
    return $list;
}



