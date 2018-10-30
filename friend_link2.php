<?php
/**
 * friend_link api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$zone = !empty($_GET['zone'])? $_GET['zone'] : '';
$zone_md5 = false;

$f_id1 = FLINK_1;
$f_id2 = FLINK_2;
$f_id3 = FLINK_3;
if($zone && in_array($zone,$zone_array)){
    //赛区
	$f_id1 = FLINK_COMM_1;
    $f_id2 = FLINK_COMM_2;
    $zone_md5  = md5($zone);
}

//$cache_id = 'neso2017_flink_'.$zone_md5;
$cache_id = 'neso2017_flink_2_'.$zone_md5;


//$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list1 = getFlink_1($f_id1,$zone_md5);
    $list2 = getFlink_1($f_id2,$zone_md5);
    $list3 = getFlink_1($f_id3,$zone_md5);
    $resArr = array(
        'list1'=>$list1,
        'list2'=>$list2,
        'list3'=>$list3
    );
    if($zone){
        $list1 = getFlink_1($f_id1,$zone_md5);
        $list2 = getFlink_2($f_id2,$zone_md5);
        
        $groupList1 = getGroupArr($list1);
        
        $resArr = array(
            'diy_flink'=>$groupList1,
            'flink'=>$list2,
            
        );
    }
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();


/**
主办承办方
赛区的数据存储在dede_addon26
*/
function getFlink_1($id,$zone_md5){
    global $conn;
    global $zone;
    $list = array();
    if($zone_md5){
        $sql = "SELECT * FROM `dede_addon31` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.tips = '".$zone."' AND b.arcrank > -2 ORDER BY b.weight DESC;";
        $result=$conn->query($sql);
        
        while ($row = mysqli_fetch_assoc($result)){
            $arr['typename'] = $row['title'];
            $arr['title'] = $row['title1'];
            $arr['link'] = $row['title2'];
            $arr['img'] = IMG_SITE.$row['img1'];
            $list[] = $arr;
        }
    }else{
        $sql = "SELECT * FROM `dede_addon26` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY b.weight DESC;";
        $result=$conn->query($sql);
        while ($row = mysqli_fetch_assoc($result)){
            $arr['title'] = $row['title'];
            $arr['img'] = IMG_SITE.$row['ad_img'];
            $arr['link'] = $row['ad_link'];
            $arr['flag'] = $row['flag'] == "c"? 1:0;
            
            $list[] = $arr;
        }
    }
    return $list;
}

function getFlink_2($id,$zone_md5){
    global $conn;
    global $zone;
    $list = array();
    if($zone_md5){
        $sql = "SELECT * FROM `dede_addon31` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.tips = '".$zone."' AND b.arcrank > -2 ORDER BY b.weight DESC;";
        $result=$conn->query($sql);
        
        while ($row = mysqli_fetch_assoc($result)){
            $arr['title'] = $row['title'];
            $arr['link'] = $row['title1'];
            $arr['img'] = IMG_SITE.$row['img1'];
            $arr['flag'] = $row['flag'] == "c"? 1:0;
            $list[] = $arr;
        }
    }
    return $list;
}

function getGroupArr($unGroupArr){
    $resList = array();
    foreach($unGroupArr as $k=>$v){
        //分组
            $groupRes[$v['typename']][] = $v;
    }
    
    $i= 0;
    foreach($groupRes as $v){
        $arr['typename'] = $v[0]['typename'];
        $arr['data']     = $v;
        $resList[] = $arr;
        $i++;
        
    }
    
    return $resList;
}

