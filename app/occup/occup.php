<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/occup/occup.php?ac=list", tags={"occup"},
 *   summary="获取招聘求职列表",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=true, in="query",example="FUULTIME|PARTTIME|FIND"),
 *   @SWG\Parameter(name="page", type="integer", required=true, in="query",example="1"),
 *   @SWG\Parameter(name="pageCount", type="integer", required=true, in="query",example="10"),
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
  $type = empty($_GET['type'])? '':addslashes($_GET['type']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = $_GET['pageCount'];
  if(!in_array($type,array('FIND','FULLTIME','PARTTIME')) || !$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
  }else{
    $list = getOccupListByType($type,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/occup/occup.php?ac=create", tags={"occup"},
 *   summary="创建求职招聘未实现",
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
















function createAd($arr){
  global $conn;
  $sql="INSERT INTO `snail_ad` (ad_name,ad_img,ad_remark,ad_type,ad_show)
  VALUES ('".$arr['ad_name']."','".$arr['ad_img']."','".$arr['ad_remark']."','".$arr['ad_type']."',".$arr['ad_show'].")";
  return $conn->query($sql);
}

function getOccupListByType($type,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    if($type == "FIND"){//找工作

      $total = $conn->query("SELECT * from `snail_job_find` WHERE `show` = 1 AND `start_date` < $time AND `end_date` > $time;")->num_rows;
      $sql="SELECT * from `snail_job_find` WHERE `show` = 1 AND `start_date` < $time AND `end_date` > $time limit $offset,$pageCount;";
      $result=$conn->query($sql);
      while ($row = mysqli_fetch_assoc($result))
      {
          $list[] = $row;
      }
        
    }elseif($type == "FULLTIME" || $type == "PARTTIME"){ //全职兼职
      $total = $conn->query("SELECT * from `snail_job_release` WHERE `show` = 1 AND `type` = '$type' AND `start_date` < $time AND `end_date` > $time;")->num_rows;
      $sql="SELECT * from `snail_job_release` WHERE `show` = 1 AND `type` = '$type' AND `start_date` < $time AND `end_date` > $time limit $offset,$pageCount;";
      $result=$conn->query($sql);
      while ($row = mysqli_fetch_assoc($result))
      {
          $list[] = $row;
      }
    }

    return array('total'=>$total,'list'=>$list);
}
