<?php
/**
 * zone_venue api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$zone = !empty($_GET['zone'])? $_GET['zone'] : '';
$zone_md5 = false;
if($zone && in_array($zone,$zone_array)){
    $zone_md5  = md5($zone);
}else{
    header('HTTP/1.1 500 ERROR');
    exit('{}');
}

$cache_id = 'neso2017_zone_'.$zone_md5;
$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    require_once("./zone_step.php");
    require_once("./zone_notice.php");
    require_once("./zone_venue.php");
    require_once("./zone_final_match.php");
    require_once("./zone_pre_match.php");
    require_once("./zone_pre_detail.php");
    require_once("./zone_friend_link.php");
    
    $zoneStep = getZoneStep(ZONE_STEP,$zone);   
    $zoneNotice = getZoneNotice(ZONE_NOTICE_ID,$zone);
    $zoneVenue  = getZoneVenue(ZONE_VENUE_ID,$zone);
    $zoneFinalMatch = getZoneFinalMatch(FINAL_MATCH_ID,$zone);
    $zonePreMatch = getZonePreMatch(PRE_MATCH_ID,$zone);
    $zonePreMatchRule = getZonePreMatchDetail(PRE_MATCH_DETAIL,$zone);
    
    $flink_1 = getFlink_1(FLINK_1);
    $flink_2 = getFlink_other(FLINK_COMM_1,$zone);
    $flink_3 = getFlink_other(FLINK_COMM_2,$zone);
    
    $resArr = array(
        'zone_step'=>$zoneStep,
        'zone_notice'=>$zoneNotice,
        'zone_venue'=>$zoneVenue,
        'zone_final_match'=>$zoneFinalMatch,
        'zone_pre_match'=>$zonePreMatch,
        'zone_rule'=>$zonePreMatchRule
        //'flink'=>array($flink_1,$flink_2,$flink_3)
    );
    
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();
