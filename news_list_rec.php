<?php
/**
 * news_recomm api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = NEWS_ID;
$page = 1;
$pageLimit = 4;

$cache_id = 'neso2017_newslist_rec';

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list = getNewsList($id,$page,$pageLimit);
    $resArr = array(
        'list'=>$list,
    );
   $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();



function getNewsList($id,$page,$pageLimit){
  
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id  AND b.arcrank > -2 AND LOCATE('h',b.flag)>0 ORDER BY weight DESC LIMIT $pageLimit;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result)){
        $arr['aid'] = $row['aid'];
        $arr['title'] = $row['title'];
        $arr['desc'] = $row['description'];
        $arr['date'] = date('Y-m-d H:i:s',$row['pubdate']);
        $arr['litpic'] = IMG_SITE.$row['litpic'];
        
        $list[] = $arr;
    }
    return $list;
}



