<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = VS_ID;
$cache_id = 'neso2017_against';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $resArr = getVs($id);

   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();

function getVs($id){
    global $conn;
    $groupRes = array();
    $unGroupRes = getVsData($id);
    $groupRes = getGroupArr($unGroupRes);
    $finalArr = getFinalArr($groupRes);
    return $finalArr;
}


//获取对阵图数据
function getVsData($id){
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon34` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ;";
    
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        
        if($row['tips'] == '英雄联盟'){
            $arr['game'] = 1;
        }elseif($row['tips'] == '星际争霸2'){
            $arr['game'] = 2;
        }elseif($row['tips'] == '魔兽争霸3'){
            $arr['game'] = 3;
        }elseif($row['tips'] == '炉石传说'){
            $arr['game'] = 4;
        }elseif($row['tips'] == '王者荣耀'){
            $arr['game'] = 5;
        }elseif($row['tips'] == '英雄联盟邀请组'){
            $arr['game'] = 6;
        }elseif($row['tips'] == '王者荣耀邀请'){
            $arr['game'] = 7;
        }
        $arr['type'] = $row['match_type'] == '八强' ? 'single':'group';
        $arr['mid'] = $row['match_id'];
        $list[] = $arr;
    }
    
    return $list;    
}

function getGroupArr($unGroupArr){
    $groupRes = array();
    //分组
    foreach($unGroupArr as $k=>$v){
            $groupRes[$v['game']][] = $v;
    }
    return $groupRes;
}

function getFinalArr($arr){
    $resArr = array();
    $i = 0;
    foreach($arr as $k=>$v){
        $resArr[$i]['id']= $k;
        $resArr[$i]['list'] = $v;
        $i++;
    }
    return $resArr;
}