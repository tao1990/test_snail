<?php
/**
 * friend_link api
 *
 */
/* 
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$zone = !empty($_GET['zone'])? $_GET['zone'] : false;
$zone_md5 = false;

$f_id1 = FLINK_1;
$f_id2 = FLINK_2;
$f_id3 = FLINK_3;
if($zone && in_array($zone,$zone_array)){
    //赛区
    $f_id2 = FLINK_COMM_1;
    $f_id3 = FLINK_COMM_2;
    $zone_md5  = md5($zone);
}

$cache_id = 'neso2017_flink_'.$zone_md5;


$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list1 = getFlink_1($f_id1);
    $list2 = getFlink_other($f_id2,$zone);
    $list3 = getFlink_other($f_id3,$zone);
    $resArr = array(
        'list1'=>$list1,
        'list2'=>$list2,
        'list3'=>$list3
    );
   $mem->set($cache_id,$resArr,0,1800);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();

/*
/**
主办承办方
赛区的数据存储在dede_addon26
*/
function getFlink_1($id){
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon26` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id ORDER BY b.weight DESC LIMIT 4;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['title'] = $row['title'];
        $arr['img'] = IMG_SITE.$row['ad_img'];
        $arr['link'] = $row['ad_link'];
        
        $list[] = $arr;
    }
    return $list;
}

/**
合作媒体
公用数据存储在dede_addon26
赛区的数据存储在dede_addon31
*/
function getFlink_other($id,$zone){
    global $conn;
    global $zone;
    $list = array();
    if($zone){
        $sql = "SELECT * FROM `dede_addon31` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.tips = '".$zone."' ORDER BY b.weight DESC;";
        $result=$conn->query($sql);
        while ($row = mysqli_fetch_assoc($result)){
            $arr['title'] = $row['title'];
            $arr['img'] = IMG_SITE.$row['img1'];
            $arr['link'] = $row['title1'];
            
            $list[] = $arr;
        }
    }else{
        $sql = "SELECT * FROM `dede_addon26` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id ORDER BY b.weight DESC;";
        $result=$conn->query($sql);
        while ($row = mysqli_fetch_assoc($result)){
            $arr['title'] = $row['title'];
            $arr['img'] = IMG_SITE.$row['ad_img'];
            $arr['link'] = $row['ad_link'];
            
            $list[] = $arr;
        }
    }
    
    return $list;
}



