<?php
/**
 * news api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = NEWS_ID;
$page = $_GET['page']>1? $_GET['page'] : 0;
$resPage = $page == 0? 1 : $page;
$pageLimit = 10;
$cache_id = 'neso2017_newslist_'.$id.'_'.$page;

//（当前页数 - 1 ）X 每页条数 ， 每页条数
$pageStart = ($resPage-1)*$pageLimit;

$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $list = getNewsList($id,$pageStart,$pageLimit);
    $num = getNewsListNum($id);
    
    $resArr = array(
        'list'=>$list,
        'page'=>$resPage,
        'pageLimit'=>$pageLimit,
        'totalNum'=>$num
    );
    $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();



function getNewsListNum($id){
    
    global $conn;
    $sql = "SELECT count(*) AS num FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2;";
    $result=$conn->query($sql);
    $row = mysqli_fetch_assoc($result);
    $num = $row['num'];
    return $num;
}

function getNewsList($id,$page,$pageLimit){
    
    global $conn;
    $list = array();
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY pubdate DESC LIMIT $page,$pageLimit;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $arr['id'] = $row['aid'];
        $arr['title'] = $row['title'];
        $arr['desc'] = $row['description'];
        $arr['date'] = date('Y-m-d H:i:s',$row['pubdate']);
        $arr['litpic'] = $row['litpic']? IMG_SITE.$row['litpic'] : "";
        $list[] = $arr;
    }
    return $list;
}
