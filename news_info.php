<?php
/**
 * news info api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

$id = NEWS_ID;
$aid = intval($_GET['aid']);
$cache_id = 'neso2017_newsinfo_'.$aid;
$resArr = $mem->get($cache_id);

if( empty($resArr) )
{
    require_once("./conn_mysql.php");
    $info = getNewsInfo($id,$aid);
    $nextPrev = getNextPrev($id,$aid);
    $hot  = getHostNews($id);
    $near = getNearNews($id);
    $resArr = array(
        'info'=>$info,
        'nextPrev'=>$nextPrev,
        'hot'=>$hot,
        'near'=>$near
    );
    $mem->set($cache_id,$resArr,0,CACHE_TIME);
}

header('HTTP/1.1 200 OK');
echo json_encode($resArr);exit();



function getNewsInfo($id,$aid){
    
    global $conn;
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.aid = $aid  AND b.arcrank > -2 LIMIT 1;";
    $result=$conn->query($sql);
    $row = mysqli_fetch_assoc($result);
    
    $res['aid'] = $row['aid'];
    $res['title'] = $row['title'];
    $res['pubdate'] = date('Y-m-d H:i:s',$row['pubdate']);
    $res['litpic'] = $row['litpic'];
    $res['body'] = $row['body'];
    
    return $res;
}


function getNextPrev($id,$aid){
    global $conn;
    $sql1 = "SELECT title,aid FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.aid < $aid  AND b.arcrank > -2 LIMIT 1;";
    $result1=$conn->query($sql1);
    $row1= mysqli_fetch_assoc($result1);
   
    
    $sql2 = "SELECT title,aid FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND a.aid > $aid  AND b.arcrank > -2 LIMIT 1;";
    $result2=$conn->query($sql2);
    $row2 = mysqli_fetch_assoc($result2);
    
    $res['next'] = $row1;
    $res['prev'] = $row2;
    
    return $res;
}


function getHostNews($id){
    
    global $conn;
    $res = array();
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND LOCATE('c',b.flag)>0 AND b.arcrank > -2    ORDER BY weight DESC LIMIT 10;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $arr['id'] = $row['aid'];
        $arr['title'] = $row['title'];
        $res[] = $arr;
    }
    return $res;
}

function getNearNews($id){
    
    global $conn;
    $res = array();
    $sql = "SELECT * FROM `dede_addonarticle` AS a LEFT JOIN `dede_archives` AS b ON a.aid = b.id WHERE a.typeid= $id AND b.arcrank > -2 ORDER BY pubdate DESC LIMIT 10;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $arr['id'] = $row['aid'];
        $arr['title'] = $row['title'];
        $res[] = $arr;
    }
    return $res;
}



