<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");
require_once("../../api/sms/signatureRequest.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/user/user.php?ac=getCode", tags={"user"},
 *   summary="获取注册验证码",
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
if($ac == 'getCode'){
    
    //phone check
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $mobile = $_GET['mobile'];
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

if($ac == 'register'){
    
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $type = $bodyData['type'];
    $mobile = $bodyData['mobile'];
    $password1 = $bodyData['password1'];
    $password2 = $bodyData['password2'];
    $verifyCode = $bodyData['verifyCode'];
    
    $verify = checkVerify();
    if($verify && $password1 && $password2 && ($password1 == $password2) && $mobile && $type){
        
        $uid = addUser();
        if($uid){
            //need add bonus info
            //求职+广告墙价格 100 其余都是200
            //注册送300（100+200）
            
            
            
            
             header('HTTP/1.1 200 注册成功');
            echo json_encode ( array('status'=>200, 'data'=>array('uid'=>$uid)) );exit();
        }else{
            header('HTTP/1.1 500 注册失败');
            echo json_encode ( array('status'=>500, 'msg'=>'注册失败') );exit();
        }
        
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
    
    
    
}


if($ac == 'login'){
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $mobile = $bodyData['mobile'];
    $password = $bodyData['password'];
    $check = checkUser($mobile,$password);
    if($check['uid']){
        $token = tokenCreate($check['uid']);
        
        
        //need add bonus info
        //求职+广告墙价格 100 其余都是200
        //注册送300（100+200）
        
        $bonusInfo = getUserBonusInfo($check['uid']);
        
        
        $resArr = array(
            'username'=>$check['username'],
            'mobile'=>$check['mobile'],
            'token'=>$token,
            'bonusInfo'=>$bonusInfo
        );
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$resArr) );exit();
    }else{
        header('HTTP/1.1 403 验证失败');
        echo json_encode ( array('status'=>403, 'msg'=>'验证失败') );exit();
    }
}






/****************************************************FUNC*************************************************************/

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
    return $conn->query("SELECT * from `snail_verify` WHERE `mobile` = '$mobile' AND `code`='$code'; ")->fetch_row();
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



function sendSms($phone,$templateCode,$code) {
    $params = array ();

    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = SMS_ACCESS_KEY;
    $accessKeySecret = SMS_ACCESS_SECRET;//
    
    // fixme 必填: 短信接收号码
    //$params["PhoneNumbers"] = "0079652998678";
    //$params["PhoneNumbers"] = "17621090121";
    $params["PhoneNumbers"] = $phone;
    

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = SMS_SIGN_NAME;

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    //$params["TemplateCode"] = "SMS_145255795";//国内
    //$params["TemplateCode"] = "SMS_145295382";//国外
    $params["TemplateCode"] = $templateCode;
    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $code
    );

    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = time();

    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
    $params['SmsUpExtendCode'] = "1234567";


    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // 初始化SignatureRequest实例用于设置参数，签名以及发送请求
    $helper = new SignatureRequest();

    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );

    return $content;
}
