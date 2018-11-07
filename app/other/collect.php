<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
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
    
    $list = getCollectList($uid);
    //$hashCode - $bodyData['hashCode'];
    $firstNum = substr( $mobile, 0, 1 );
    if(strlen($mobile)==11 && ($firstNum == 1 || $firstNum == 7)){
        
        $templateCode = SMS_TEMPLATE_CN;
        $code = rand(1000,9999);
        updateVerify($mobile,$code);
        if($firstNum == 7){
            $templateCode = SMS_TEMPLATE_RU;
            $mobile = "00".$mobile;
        }
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
   
    $res = sendSms($mobile,$templateCode,$code);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$res) );exit();
}

/**
 * @SWG\Post(path="/app/user/user.php?ac=login", tags={"user"},
 *   summary="用户登陆",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'mobile':'7XXX|1XXX','password':''}"
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
if($ac == 'login'){
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $mobile = $bodyData['mobile'];
    $password = $bodyData['password'];
    $check = checkUser($mobile,$password);
    if($check['uid']){
        $token = tokenCreate($check['uid']);
        $bonusInfo = getUserBonusInfo($check['uid']);
        $resArr = array(
            'uid'=>$check['uid'],
            'mobile'=>$check['mobile'],
            'token'=>$token,
            'bonusInfo'=>$bonusInfo
        );
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$resArr) );exit();
    }else{
        header('HTTP/1.1 403 error');
        echo json_encode ( array('status'=>403, 'msg'=>'验证失败') );exit();
    }
}






/****************************************************FUNC*************************************************************/

//给新用户发红包
function sendNewUserBonus($uid){
    global $conn;
    $time = time();
    $sendBonus = SEND_BONUS_IDS;
    $bonusRes = $conn->query("SELECT type_id,use_term from `snail_bonus_type` WHERE type_id IN ($sendBonus);");
    while ($row = mysqli_fetch_assoc($bonusRes))
    {
      $row['get_time']    = $time;
      $row['expiry_time'] = $time + (86400*$row['use_term']);
      $bonusList[] = $row;
    }
    
    foreach($bonusList as $v){
        $conn->query("INSERT INTO `snail_user_bnous` (bonus_type_id,uid,get_time,expiry_time) VALUES ('$v[type_id]',$uid,'$v[get_time]','$v[expiry_time]');");
        //$sql = "INSERT INTO `snail_user_bnous` (bonus_type_id,uid,get_time,expiry_time) VALUES ('$v[type_id]',$uid,'$v[get_time]','$v[expiry_time]');";
    }
}


//获取用户红包信息
function getUserBonusInfo($uid){
  global $conn;
  $result = $conn->query("SELECT * from `snail_user_bonus` A LEFT JOIN `snail_bonus_type` B  ON A.bonus_type_id = B.type_id WHERE A.uid = $uid;");
  while ($row = mysqli_fetch_assoc($result))
  {
      if($row['expiry_time'] != 0){
        $row['overdue'] =  $row['get_time'] + (86400*$row['use_term']) > $row['expiry_time'] ? 1:0;  
      }else{
        $row['overdue'] = 0;
      }
      $list[] = $row;
  }
  return $list;
}

function addUser($arr){
    global $conn;
    $username = "";
    $mobile = $arr['mobile'];
    $type   = $arr['type'];
    $password = md5($arr['password']);
    $res = $conn->query("INSERT INTO `snail_user` (username,mobile,type,password) VALUES ('$username','$mobile','$type','$password');");
    $uid = $conn->insert_id;
    return $uid;
}

function checkVerify($mobile,$code){
    if($mobile && $code){
        return $conn->query("SELECT * from `snail_verify` WHERE `mobile` = '$mobile' AND `code`='$code'; ")->fetch_row();
    }else{
        return null;
    }
}

function checkUser($mobile,$password){
    global $conn;
    $password = md5($password);
    return $conn->query("SELECT * from `snail_user` WHERE `mobile` = '$mobile' AND password='$password' ")->fetch_assoc();
}

function updateVerify($mobile,$code){
    global $conn;
    $have = $conn->query("SELECT * from `snail_verify` WHERE `mobile` = '$mobile' ")->fetch_row();
    if($have){
        $do = $conn->query("UPDATE `snail_verify` SET `code` = $code WHERE `mobile` = '$mobile';");
    }else{
        $do = $conn->query("INSERT INTO `snail_verify` (mobile,code) VALUES ('$mobile',$code);");
    }
}



