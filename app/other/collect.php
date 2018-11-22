<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/other/collect.php?ac=list", tags={"other"},
 *   summary="获取收藏列表",
 *   description="",
 * @SWG\Parameter(name="mobile", type="string", required=true, in="query",example = "79XXX|1XXXX"),
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
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $uid = $_GET['uid'];
    if(!$uid){
        header('HTTP/1.1 400 error');
        echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
    }else{
        $list = getCollectList($uid);
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }
}

/**
 * @SWG\Get(path="/app/other/collect.php?ac=collect", tags={"other"},
 *   summary="收藏",
 *   description="",
 * @SWG\Parameter(name="uid", type="integer", required=true, in="query"),
 * @SWG\Parameter(name="type", type="string", required=true, in="query"),
 * @SWG\Parameter(name="postId", type="integer", required=true, in="query"),
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
if($ac == 'collect'){
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $uid    = empty($_GET['uid'])? 0:$_GET['uid'];
    $type   = empty($_GET['type'])? '':$_GET['type'];
    $postId = empty($_GET['postId'])? '':$_GET['postId'];
    if($uid >0 && $type && $postId){
        $res = doCollect($uid,$type,$postId);
        if($res){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200, 'msg'=>'ok') );exit();
        }else{
            header('HTTP/1.1 400 error');
            echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
        }
    }else{
        header('HTTP/1.1 400 error');
        echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
    }
    
}






/****************************************************FUNC*************************************************************/

function getCollectList($uid){
    global $conn;
    $collectRes = $conn->query("SELECT * FROM `snail_collect` WHERE uid = $uid;");
    while ($row = mysqli_fetch_assoc($collectRes))
    {
      $list[] = $row;
    }
    return $list;
}

function doCollect($uid,$type,$postId){
    global $conn;
    $have = $conn->query("SELECT * FROM `snail_collect` WHERE uid = $uid AND type ='$type' AND post_id = $postId;")->fetch_row();
    if($have){
       $do = $conn->query("DELETE FROM `snail_collect` WHERE uid = $uid AND type ='$type' AND post_id = $postId;");
    }else{
       $do = $conn->query("INSERT INTO `snail_collect` (uid,type,post_id) VALUES ($uid,'$type',$postId);");
    }
    return $do;
}

