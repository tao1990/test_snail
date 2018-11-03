<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/ad/ad.php?ac=list", tags={"ad"},
 *   summary="获取系统广告列表",
 *   description="",
 *   @SWG\Parameter(name="ad_type", type="string", required=true, in="query",example="INDEX|MINE"
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
  $ad_type = empty($_GET['ad_type'])? '':addslashes($_GET['ad_type']);
  if(!$ad_type){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'缺少参数') );exit();
  }else{
    $list = getAdListByType($ad_type);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
  }
}


/**
 * @SWG\Post(path="/app/ad/ad.php?ac=create", tags={"ad"},
 *   summary="创建系统广告",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'ad_name':'首页广告02',	'ad_img':'/upload/20181031/33d2360b6fb024e170425f9ce57a14c1.jpg',	'ad_remark':'test',	'ad_type':'INDEX',	'ad_show':1}"
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
if($ac == 'create'){
  $bodyData = @file_get_contents('php://input');
  $bodyData = json_decode($bodyData,true);
  $arr['ad_name']  = empty($bodyData['ad_name'])? '':$bodyData['ad_name'];
  $arr['ad_img']   = empty($bodyData['ad_img'])? '':$bodyData['ad_img'];
  $arr['ad_remark']= empty($bodyData['ad_remark'])? '':$bodyData['ad_remark'];
  $arr['ad_type']  = empty($bodyData['ad_type'])? '':$bodyData['ad_type'];
  $arr['ad_show']  = empty($bodyData['ad_show'])? '':$bodyData['ad_show'];
  if(!$arr['ad_name'] || !$arr['ad_img'] || !$arr['ad_type']){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'缺少参数') );exit();
  }else{
    $create = createAd($arr);
    if($create){
      header('HTTP/1.1 200 OK');
      echo json_encode ( array('status'=>200, 'msg'=>'ok') );exit();
    }else{
      header('HTTP/1.1 500 ERROR');
      echo json_encode ( array('status'=>500, 'msg'=>'服务器错误') );exit();
    }
  }

}














/*******************************************************************func***************************************************************************/


function createAd($arr){
  global $conn;
  $sql="INSERT INTO `snail_ad` (ad_name,ad_img,ad_remark,ad_type,ad_show)
  VALUES ('".$arr['ad_name']."','".$arr['ad_img']."','".$arr['ad_remark']."','".$arr['ad_type']."',".$arr['ad_show'].")";
  return $conn->query($sql);
}

function getAdListByType($type){
    global $conn;
    $list = array();
    $sql="SELECT * from `snail_ad` WHERE ad_type = '".$type."';";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $list[] = $row;
    }
    return $list;
}
