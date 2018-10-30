<?php
/**
 * area_list api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = ZONE_STEP;
$cache_id = 'neso2017_area_list';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $unGroupArr = area_list($id);
    $resArr = group_area_list($unGroupArr);

   // $resArr2 = getMapScore($id);
//    $resArr = array('map'=>$resArr1,'score'=>$resArr2);
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();

function area_list($id){
    global $conn;
    $list = array();
    $sql = "SELECT a.tips FROM `dede_addon32` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 GROUP BY a.tips;";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $list[] = $row['tips'];
    }        
    return $list;    
}

function group_area_list($unGroupArr){
    $resArr = array();
    $area1 = array("浙江","青岛","山东","厦门","江苏","福建","上海");
    $area2 = array("海南","深圳","广西","广东");
    $area3 = array("湖北","江西","湖南");
    $area4 = array("内蒙古","河北","山西");
    $area5 = array("宁夏","陕西","青海");
    $area6 = array("云南","重庆","四川");
    $area7 = array("吉林","黑龙江","辽宁");
    foreach($unGroupArr as $k=>$v){
        if(in_array($v,$area1)){
            $resArr[0]['name'] = '华东';
            $resArr[0]['list'][] = $v;
        }elseif(in_array($v,$area2)){
            $resArr[1]['name'] = '华南';
            $resArr[1]['list'][] = $v;
        }elseif(in_array($v,$area3)){
            $resArr[2]['name'] = '华中';
            $resArr[2]['list'][] = $v;
        }elseif(in_array($v,$area4)){
            $resArr[3]['name'] = '华北';
            $resArr[3]['list'][] = $v;
        }elseif(in_array($v,$area5)){
            $resArr[4]['name'] = '西北';
            $resArr[4]['list'][] = $v;
        }elseif(in_array($v,$area6)){
            $resArr[5]['name'] = '西南';
            $resArr[5]['list'][] = $v;
        }elseif(in_array($v,$area7)){
            $resArr[6]['name'] = '东北';
            $resArr[6]['list'][] = $v;
        }
    }
    $realRes = array();
    foreach($resArr as $v){
         array_push($realRes,$v);
    }
    return $realRes;
}
