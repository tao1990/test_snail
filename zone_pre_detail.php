<?php
/**
 * zone_pre_detail api
 *
 */
 /*
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$zone = !empty($_GET['zone'])? $_GET['zone'] : '';
$zone_md5 = false;
if($zone && in_array($zone,$zone_array)){
    $id = PRE_MATCH_DETAIL;
    $zone_md5  = md5($zone);
}else{
    header('HTTP/1.1 500 ERROR');
    exit('{}');
}

$cache_id = 'neso2017_pre_match_detail_'.$zone_md5;

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $resArr = getZonePreMatchDetail($id,$zone);
   $mem->set($cache_id,$resArr,0,1800);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();
*/
function getZonePreMatchDetail($id,$zone){
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon31` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.tips = '".$zone."' AND b.arcrank > -2 ORDER BY b.weight ASC;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['title'] = $row['title'];
        $arr['desc'] = $row['text1'];
        $list[] = $arr;
    }
    return $list;
}