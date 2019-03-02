<?php

//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);

/**
 * @SWG\Get(path="/app/other/message.php?ac=unreadCount", tags={"other"},
 *   summary="获取未读消息数(ok)",
 *   description="",
 * @SWG\Parameter(name="uid", type="string", required=true, in="query",example = ""),
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
if($ac == 'unreadCount'){
    $uid = empty($_GET['uid'])? 0:$_GET['uid'];
    if($uid > 0){
        $count = getUnreadNum($uid);
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$count) );exit();
    }else{
        header('HTTP/1.1 403 error');
        echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
    }
}

/**
 * @SWG\Get(path="/app/other/message.php?ac=list", tags={"other"},
 *   summary="获取消息列表(ok)",
 *   description="",
 * @SWG\Parameter(name="uid", type="string", required=true, in="query",example = ""),
 * @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
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
    //$a =tokenCreate(15);
    //print_r($a);die;
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $uid = empty($_GET['uid'])? 0:$_GET['uid'];
    $token = empty($_GET['token'])? '':$_GET['token'];
    if(tokenVerify($token,$uid) && $uid>0 && $token){
        $list = getMessageList($uid);
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }else{
        header('HTTP/1.1 403 error');
        echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
    }

}

/**
 * @SWG\Get(path="/app/other/message.php?ac=infoList", tags={"other"},
 *   summary="获取消息列表详情(ok)",
 *   description="",
 * @SWG\Parameter(name="uid", type="string", required=true, in="query",example = ""),
 * @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
 * @SWG\Parameter(name="type", type="string", required=true, in="query",example = "SYSTEM|ORDER"),
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
if($ac == 'infoList'){
    $uid = empty($_GET['uid'])? 0:$_GET['uid'];
    $token = empty($_GET['token'])? '':$_GET['token'];
    $type = empty($_GET['type'])? '':$_GET['type'];
    if(tokenVerify($token,$uid) && $uid>0 && $token && $type){
        $list = getMessageInfoList($uid,$type);
        snail_update('snail_message',array('unread'=>0),"uid=$uid AND type=$type");
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }else{
        header('HTTP/1.1 403 error');
        echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
    }
    
}






/****************************************************FUNC*************************************************************/

function getUnreadNum($uid){
    global $conn;
    return $conn->query("SELECT count(*) as count from `snail_message` WHERE `uid` = $uid AND `unread`=1; ")->fetch_assoc();
}

function getMessageList($uid){
    global $conn;
    $list = [];
    //select * from comment where id in(select max(id) from comment group by user_id) order by user_id;
    $collectRes = $conn->query("SELECT type,title,max(dateline) AS dateline FROM `snail_message` WHERE uid = $uid GROUP BY type;");
    while ($row = mysqli_fetch_assoc($collectRes))
    {
      $list[] = $row;
    }
    return $list;
}

function getMessageInfoList($uid,$type){
    global $conn;
    $list = [];
    $collectRes = $conn->query("SELECT type,title,content,dateline FROM `snail_message` WHERE uid = $uid AND type = '$type' ORDER BY dateline DESC;");
    while ($row = mysqli_fetch_assoc($collectRes))
    {
      $row['content'] = analysisContent($row['content']);
      $list[] = $row;
    }
    return $list;
}

function analysisContent($str){
    
       return explode('\n',$str);
    
}


