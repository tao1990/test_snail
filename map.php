<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = MAP;
$cache_id = 'neso2017_map';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $resArr1 = getMap($id);
    $resArr2 = getMapScore($id);
    $resArr = array('map'=>$resArr1,'score'=>$resArr2);
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();

function getMap($id){
    global $conn;
    $groupRes = array();
    $unGroupRes = getMapData($id);
    $groupRes = getDataOrder($unGroupRes);
    return $groupRes;
}

//按省重新组织数组
function getDataOrder($unGroupRes){
    
    $resList = array();
    $gdArr = array('广东','深圳');
    $sdArr = array('山东','青岛');
    $fjArr = array('福建','厦门');
    
    foreach($unGroupRes as $k=>$v){
        if(in_array($v['area'],$gdArr)){//广东 深圳
            $resList['广东'][] = $v;
        }elseif(in_array($v['area'],$sdArr)){//山东 青岛
            $resList['山东'][] = $v;
        }elseif(in_array($v['area'],$fjArr)){//福建 厦门
            $resList['福建'][] = $v;
        }else{
            $resList[$v['area']][] = $v;
        }
    }
    
    return $resList;
}

//获取map数据
function getMapData($id){
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon33` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ;";
    
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        
        $arr['area'] = $row['tips'];
        $arr['lol'] = $row['game1'];
        $arr['sc2'] = $row['game2'];
        $arr['war3'] = $row['game3'];
        $arr['hs'] = $row['game4'];
        $arr['wzry'] = $row['game5'];
        $arr['people'] = $row['people'];
        $arr['count'] = $row['over_match_count'];
        $arr['icon'] = array();
        $arr['lol']? array_push($arr['icon'],'lol') : '';
        $arr['sc2']? array_push($arr['icon'],'sc2') : '';
        $arr['war3']? array_push($arr['icon'],'war3') : '';
        $arr['hs']? array_push($arr['icon'],'hs') : '';
        $arr['wzry']? array_push($arr['icon'],'wzry') : '';
        $list[] = $arr;
    }
    return $list;    
}

function getMapScore($id){
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon33` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY a.team_score DESC;";
    $result = $conn->query($sql);
    $i = 1;
    while ($row = mysqli_fetch_assoc($result)){
        //$arr['rank'] = $i;
        $arr['area'] = $row['tips'];
        $arr['score'] = $row['team_score'];
        $i++;
        $list[] = $arr;
    }
    $sortList = array();
    foreach ($list as $v) {
      $sortList[] = $v['score'];
    }
    array_multisort($sortList, SORT_DESC, $list);
    foreach($list as $k=>$v){
      $list[$k]['rank'] = $k+1;
    }
    return $list;  
}