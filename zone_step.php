<?php
/**
 * zone_step api
 */
/* 
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$zone = !empty($_GET['zone'])? $_GET['zone'] : '';
$zone_md5 = false;
if($zone && in_array($zone,$zone_array)){
    $id = ZONE_STEP;
    $zone_md5  = md5($zone);
}else{
    header('HTTP/1.1 500 ERROR');
    exit('{}');
}

$cache_id = 'neso2017_pre_match_'.$zone_md5;
$resArr = $mem->get($cache_id);
if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $resArr = getZoneStep($id,$zone);
   $mem->set($cache_id,$resArr,0,1800);
}
header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();
*/
function getZoneStep($id,$zone){
    global $conn;
    $sql = "SELECT * FROM `dede_addon32` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.tips = '".$zone."' AND b.arcrank > -2 ORDER BY b.weight DESC LIMIT 1;";
    $result=$conn->query($sql);
    $row = mysqli_fetch_assoc($result);
    $tempStepArr = explode(',',$row['step']);
    //$list['step'] = $row['step'];
    $enum = array(
        array('name'=>'创建赛事','status'=>0),
        array('name'=>'报名开始','status'=>0),
        array('name'=>'报名结束','status'=>0),
        array('name'=>'比赛开始','status'=>0)
        );
    if(in_array('创建赛事',$tempStepArr)){
        $enum[0]['status'] = 1;
        $enum[0]['time_slot'] = $row['time_slot1'];
    }
    if(in_array('报名开始',$tempStepArr)){
        $enum[1]['status'] = 1;
        $enum[1]['time_slot'] = $row['time_slot2'];
    }
    if(in_array('报名结束',$tempStepArr)){
        $enum[2]['status'] = 1;
        $enum[2]['time_slot'] = $row['time_slot3'];
    }
    if(in_array('比赛开始',$tempStepArr)){
        $enum[3]['status'] = 1;
        $enum[3]['time_slot'] = $row['time_slot4'];
    }
    return $enum;
}

