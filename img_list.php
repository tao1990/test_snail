<?php
/**
 * img api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = IMG_ID;
$pageLimit = 8;
$cache_id = 'neso2017_imglist';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list = getImgList($id,$pageLimit);
    
    $resArr = array(
        'list'=>$list,
    );
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();


function getImgList($id,$pageLimit){
  
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addon26` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY b.weight DESC LIMIT $pageLimit;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['aid'] = $row['aid'];
        $arr['title'] = $row['title'];
        $arr['ad_img'] = IMG_SITE.$row['ad_img'];
        $arr['ad_link'] = $row['ad_link'];
        
        $list[] = $arr;
    }
    return $list;
}



