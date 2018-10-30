<?php
/**
 * video api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = VIDEO_ID;
$pageLimit = 10;
$cache_id = 'neso2017_videolist';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list = getVideoList($id,$pageLimit);
    
    $resArr = array(
        'list'=>$list,
    );
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();


function getVideoList($id,$pageLimit){
  
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon23` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY b.weight DESC LIMIT $pageLimit;";
    
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['aid'] = $row['aid'];
        $arr['title'] = $row['title'];
        $arr['video_info'] = $row['video_info'];
        $arr['video_img'] = IMG_SITE.$row['video_img'];
        $arr['video_link'] = $row['video_link'];
        
        $list[] = $arr;
    }
    return $list;
}



