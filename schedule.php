<?php
/**
 * schedule api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = FINAL_SCHEDULE;
$cache_id = 'neso2017_schedule';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $resArr = getSchedule($id);

   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();

function getSchedule($id){
    global $conn;
    $resList = array();
    
    $sql = "SELECT id,typename FROM `dede_arctype` WHERE reid = $id";
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['id'] = $row['id'];
        $arr['typename'] = $row['typename'];
        $id_list[] = $arr;
    }
    foreach($id_list as $k=>$v){
        $groupRes = array();
        $unGroupRes = getScheduleData($v['id']);
        
        //分组
        foreach($unGroupRes as $k2=>$v2){
            $groupRes[$v2['tips']][] = $v2;
        }
        
        $tempArr = getDataArray($groupRes);
        $resList[] = array('index'=>$k,'date'=>$v['typename'],'value'=>$tempArr);
        
    }
    return $resList;
}

//分组数组重新组织结构
function getDataArray($groupRes){
    $resList = array();
    $i = 0;
    foreach($groupRes as $k=>$v){
        $resList[] = array('index'=>$i,'title'=>$k,'value'=>$v);
        $i++;
    }
    return $resList;
}

//获取日程数据
function getScheduleData($id){
    global $conn;
    $list = array();
    $sql = "SELECT a.tips,b.title,a.title1,a.title2,a.title3,a.title4,a.start_time,a.end_time FROM `dede_addon30` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ;";
    
    $result = $conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['tips'] = $row['tips'];
        $arr['title'] = $row['title'];
        $arr['detail'] = $row['title1'];
        $arr['schedule'] = $row['title2'];
        $arr['color'] = $row['title3'];
        $arr['border'] = $row['title4'];
        $arr['start_date'] = date("Y-m-d H:i:s",$row['start_time']);
        $arr['end_date'] = date("Y-m-d H:i:s",$row['end_time']);
        $list[] = $arr;
    }
    return $list;    
}
