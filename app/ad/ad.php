<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);

/**
 * @SWG\Get(path="/app/ad/ad.php?ac=typelist", tags={"ad"},
 *   summary="获取广告类型",
 *   description="",
 * @SWG\Response(
 *   response=200,
 *   description="ok response",
 *   ),
 * @SWG\Response(
 *   response="default",
 *   description="unexpected error",
 *   )
 * )
 */
if($ac == 'typelist'){
  $list = getAdTypeList();
  if($list){
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
  }else{
    header('HTTP/1.1 500 ERROR');
    echo json_encode ( array('status'=>500, 'msg'=>'server error') );exit();
  }
}


/**
 * @SWG\Get(path="/app/ad/ad.php?ac=list", tags={"ad"},
 *   summary="获取广告列表",
 *   description="",
 *   @SWG\Parameter(name="ad_type", type="integer", required=true, in="query"
 *   ),
 * @SWG\Response(
 *   response=200,
 *   description="ok response",
 *   ),
 * @SWG\Response(
 *   response="default",
 *   description="unexpected error",
 *   )
 * )
 */
if($ac == 'list'){
  $ad_type = empty($_GET['ad_type'])? '':intval($_GET['ad_type']);
  if(!$ad_type){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'缺少参数') );exit();
  }else{
    $list = getAdListByType($ad_type);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
  }
}

function getAdTypeList(){
    global $conn;
    $list = array();
    $sql="SELECT * from `snail_ad_type`;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $list[] = $row;
    }
    return $list;
}


function getAdListByType($type){
    global $conn;
    $list = array();
    $sql="SELECT * from `snail_ad` WHERE ad_type = $type;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $list[] = $row;
    }
    return $list;
}
