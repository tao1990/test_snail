<?php

//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/post/detail.php", tags={"post"},
 *   summary="获取详情(ok)",
 *   description="",
 * @SWG\Parameter(name="typeCode", type="string", required=true, in="query",example = "OCCUP|ADWALL|BOXSHOP|PACKAGE|HOUSE_RENT"),
 * @SWG\Parameter(name="id", type="integer", required=true, in="query",example = ""),
 * @SWG\Parameter(name="postId", type="integer", required=false, in="query",example = "从系统广告用postId"),
 * @SWG\Parameter(name="uid", type="integer", required=false, in="query",example = "传入uid时候返回collect"),
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
//$a =tokenCreate(15);
//print_r($a);die;
//$bodyData = @file_get_contents('php://input');
//$bodyData = json_decode($bodyData,true);
$type = empty($_GET['typeCode'])? "":$_GET['typeCode'];
$id = empty($_GET['id'])? 0:$_GET['id'];
$postId = empty($_GET['postId'])? 0:$_GET['postId'];
$uid = empty($_GET['uid'])? 0:$_GET['uid'];
if(($id>0 && in_array($type,array('OCCUP','ADWALL','PACKAGE','BOXSHOP','HOUSE_RENT','BRING','BUYING','RESTAURANT','TOURISTDEST'))) || $postId>0){
    $info = getDetail($type,$id,$postId,$uid);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$info) );exit();
}else{
    header('HTTP/1.1 403 error');
    echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
}


/****************************************************FUNC*************************************************************/

function getDetail($type,$id,$postId,$uid){
    global $conn;
    $info = [];
    
    if($postId > 0){
      $sql ="SELECT insert_id,post_type from `snail_post_log` WHERE id = $postId limit 1;";
      $a = $conn->query($sql)->fetch_assoc();
      $type = $a['post_type'];
      $id = $a['insert_id'];
    }
    
    if($type == 'OCCUP' ||$type == 'FULLTIME' || $type == 'PARTTIME' || $type == 'FIND'){
        $sql ="SELECT * from `snail_post_occup` WHERE id = $id limit 1;";
    }elseif($type == 'ADWALL'){
        $sql ="SELECT * from `snail_post_adwall` WHERE id = $id limit 1;";
    }elseif($type == 'PACKAGE'){
        $sql ="SELECT * from `snail_post_package` WHERE id = $id limit 1;";
    }elseif($type == 'BOXSHOP'){
        $sql ="SELECT * from `snail_post_boxshop` WHERE id = $id limit 1;";
    }elseif($type == 'HOUSE_RENT'){
        $sql ="SELECT * from `snail_post_house` WHERE id = $id limit 1;";
    }elseif($type == 'BRING'){
        $sql ="SELECT * from `snail_post_bring` WHERE id = $id limit 1;";
    }elseif($type == 'BUYING'){
        $sql ="SELECT * from `snail_post_buying` WHERE id = $id limit 1;";
    }elseif($type == 'RESTAURANT'){
        $sql ="SELECT * from `snail_post_restaurant` WHERE id = $id limit 1;";
    }elseif($type == 'TOURISTDEST'){
        $sql ="SELECT * from `snail_post_touristdest` WHERE id = $id limit 1;";
    }
    $info=$conn->query($sql)->fetch_assoc();
    if($info['tags'])$info['tags'] = json_decode($info['tags']); 
    if($info['imgs'])$info['imgs'] = json_decode($info['imgs']);
    
    if($info['type'] == "我要求职"){
        $info['age'] = $info['age']."岁";
    }
    if($info['salary'] == 0){
        $info['salary'] = "面议";
    }
    $info['typeName'] = $info['type'];
    $info['type'] = $type;
     
    if($uid>0){
        $info = addCollectStatus($info,$type,$uid);
    }
    return $info;
}

function addCollectStatus($info,$type,$uid){
    global $conn;
    $sql = "SELECT * FROM `snail_collect` WHERE uid = $uid AND type = '$type';";
    $result=$conn->query($sql);
    $collectArr = array();
    while ($row = mysqli_fetch_assoc($result))
    {
      array_push($collectArr,$row['insert_id']);
    }
    if(in_array($info['id'],$collectArr)){
        $info['collected'] = true;
    }else{
        $info['collected'] = false;
    }
    return $info;
}
