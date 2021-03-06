<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../../api/sms/signatureRequest.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/user/user.php?ac=getCode", tags={"user"},
 *   summary="获取验证码(OK)",
 *   description="",
 * @SWG\Parameter(name="mobile", type="string", required=true, in="query",example = "79XXX|1XXXX",example = "REG|PWD"),
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
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $mobile     = @$_GET['mobile'];
    $type       = @$_GET['type'] == "PWD" ? "PWD":"REG";
    //$hashCode - $bodyData['hashCode'];
    snail_log(json_encode($_GET));
    $firstNum = substr( $mobile, 0, 1 );
    /*临时兼容前端提交的数据start*/
    if($firstNum != 1) $mobile = '7'.$mobile;
    /*临时兼容前端提交的数据start*/
    if(strlen($mobile)==11){
        
        $templateCode = $type=="REG" ? SMS_REG_TEMPLATE_CN:SMS_PWD_TEMPLATE_CN;
        //$templateCode = SMS_REG_TEMPLATE_CN;
        $code = rand(1000,9999);
        updateVerify($mobile,$code);
        if($firstNum != 1){
            //$templateCode = SMS_REG_TEMPLATE_RU;
            $templateCode = $type=="REG" ? SMS_REG_TEMPLATE_RU:SMS_PWD_TEMPLATE_RU;
            $mobile = "00".$mobile;
        }
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
    $res = sendSms($mobile,$templateCode,$code);
    header('HTTP/1.1 200 发送成功,请勿连续点击');
    echo json_encode ( array('status'=>200,'msg'=>'发送成功,请勿连续点击','data'=>$res) );exit();
}

/**
 * @SWG\Post(path="/app/user/user.php?ac=register", tags={"user"},
 *   summary="用户注册(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'type':'CN|RU',	'mobile':'7XXX|1XXX','password1':'','password2':'',	'verifyCode':'xxxx'}"
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
if($ac == 'register'){
    
    $bodyData = @file_get_contents('php://input');
    //$logFile = fopen("./log.log", "w");
//    $txt = "$bodyData -- ".date('Y-m-d H:i:s',time())."\n";
//    fwrite($logFile, $txt);
//    fclose($logFile); 
    snail_log($bodyData);
    $bodyData = json_decode($bodyData,true);
    @$type = $bodyData['type'];
    @$mobile = $bodyData['mobile'];
    @$password1 = $bodyData['password1'];
    @$password2 = $bodyData['password2'];
    @$verifyCode = $bodyData['verifyCode'];
    
    /*临时兼容前端提交的数据start*/
    $firstNum = substr( $mobile, 0, 1 );
    if($firstNum != 1) $mobile = '7'.$mobile;
    /*临时兼容前端提交的数据start*/

    $verify = checkVerify($mobile,$verifyCode);
  
    if($verify && $password1 && $password2 && ($password1 == $password2) && $mobile && $type){
        
        $addUser = array('mobile'=>$mobile,'type'=>$type,'password'=>$password1);
        $uid = addUser($addUser);
        if($uid){
            //need add bonus info
            //求职+广告墙价格 100 其余都是200
            //注册送380（100+200+50+20+10）
            sendNewUserBonus($uid);
            $token = tokenCreate($uid);
            $bonusInfo = getUserBonusInfo($uid);
            $resArr = array(
                'uid'=>$uid,
                'mobile'=>$mobile,
                'token'=>$token,
                'bonusInfo'=>$bonusInfo
            );
            header('HTTP/1.1 200 OK');
            echo json_encode ( array('status'=>200, 'msg'=>'注册成功','data'=>$resArr) );exit();
            
        }else{
            header('HTTP/1.1 500 改号码已注册');
            echo json_encode ( array('status'=>500, 'msg'=>'改号码已注册') );exit();
        }
        
    }else{
        header('HTTP/1.1 400 请正确填写注册信息');
        echo json_encode ( array('status'=>400, 'msg'=>'请正确填写注册信息') );exit();
    }
    
    
    
}

/**
 * @SWG\Post(path="/app/user/user.php?ac=login", tags={"user"},
 *   summary="用户登陆(OK)",
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
    snail_log($bodyData);
    $bodyData = json_decode($bodyData,true);
    $mobile = $bodyData['mobile'];
    $password = $bodyData['password'];
	/*临时兼容前端提交的数据start*/
    $firstNum = substr( $mobile, 0, 1 );
    if($firstNum != 1) $mobile = '7'.$mobile;
    /*临时兼容前端提交的数据start*/
    $check = checkUser($mobile,$password);
    if($check['uid']){
        $token = tokenCreate($check['uid']);
        $bonusInfo = getUserBonusInfo($check['uid']);
        $resArr = array(
            'uid'=>$check['uid'],
            'mobile'=>$check['mobile'],
            'token'=>$token,
            'username'=>$check['username'],
            'sex'=>$check['sex'],
            'bonusInfo'=>$bonusInfo
        );
        header('HTTP/1.1 200 登录成功');
        echo json_encode ( array('status'=>200,'msg'=>'登录成功', 'data'=>$resArr) );exit();
    }else{
        header('HTTP/1.1 403 验证失败');
        echo json_encode ( array('status'=>403, 'msg'=>'验证失败') );exit();
    }
}

/**
 * @SWG\Post(path="/app/user/user.php?ac=forgetPassword", tags={"user"},
 *   summary="用户忘记密码(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'mobile':'7XXX|1XXX','password1':'','password2':'','type':'CN|RU','verifyCode':''}"
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
if($ac == 'forgetPassword'){
    $bodyData = @file_get_contents('php://input');
    //$logFile = fopen("./log.log", "w");
//    $txt = "$bodyData -- ".date('Y-m-d H:i:s',time())."\n";
//    fwrite($logFile, $txt);
//    fclose($logFile); 
    $bodyData = json_decode($bodyData,true);
    @$type = $bodyData['type'];
    @$mobile = $bodyData['mobile'];
    @$password1 = $bodyData['password1'];
    @$password2 = $bodyData['password2'];
    @$verifyCode = $bodyData['verifyCode'];
	/*临时兼容前端提交的数据start*/
    $firstNum = substr( $mobile, 0, 1 );
    if($firstNum != 1) $mobile = '7'.$mobile;
    /*临时兼容前端提交的数据start*/
    $verify = checkVerify($mobile,$verifyCode);

    if($verify && $password1 && $password2 && ($password1 == $password2) && $mobile && $type){
        
        $arr = array('mobile'=>$mobile,'type'=>$type,'password'=>$password1);
        $change = changePw($arr);
        if($change){
            header('HTTP/1.1 200 OK');
            echo json_encode ( array('status'=>200, 'msg'=>'密码修改成功') );exit();
            
        }else{
            header('HTTP/1.1 500  failed');
            echo json_encode ( array('status'=>500, 'msg'=>'failed') );exit();
        }
        
    }else{
        header('HTTP/1.1 400 params error');
        echo json_encode ( array('status'=>400, 'msg'=>'params error') );exit();
    }
}

/**
 * @SWG\Post(path="/app/user/user.php?ac=changePassword", tags={"user"},
 *   summary="用户修改密码(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'uid':'','token':'','password1':'旧密码','password2':'新密码'}"
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
if($ac == 'changePassword'){
    global $conn;
    $bodyData = @file_get_contents('php://input');
    snail_log($bodyData);
    $bodyData = json_decode($bodyData,true);
    @$uid = $bodyData['uid'];
    @$token = $bodyData['token'];
    @$password1 = $bodyData['password1'];
    @$password2 = $bodyData['password2'];

    if(tokenVerify($token,$uid) && $password1 && $password2){
        
        $oldPW = md5($password1);
        $ck = $conn->query("SELECT * from `snail_user` WHERE `uid` = '$uid' AND password = '$oldPW'; ")->fetch_assoc();
        if(!$ck){
            header('HTTP/1.1 500  旧密码输入错误');
            echo json_encode ( array('status'=>500, 'msg'=>'旧密码输入错误') );exit();
        }
        
        $arr = array('uid'=>$uid,'password'=>$password2);
        $change = changePwByUid($arr);
        if($change){
            header('HTTP/1.1 200 密码修改成功');
            echo json_encode ( array('status'=>200, 'msg'=>'密码修改成功') );exit();
            
        }else{
            header('HTTP/1.1 500  修改失败');
            echo json_encode ( array('status'=>500, 'msg'=>'修改失败') );exit();
        }
        
    }else{
        header('HTTP/1.1 400 请填写完整的信息');
        echo json_encode ( array('status'=>400, 'msg'=>'请填写完整的信息') );exit();
    }
}


/**
 * @SWG\Post(path="/app/user/user.php?ac=updateOther", tags={"user"},
 *   summary="修改用户昵称性别(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'uid':'','token':'','username':'','sex':'男','wechat':'','bind_phone':''}"
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
if($ac == 'updateOther'){
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    @$uid = $bodyData['uid'];
    @$token = $bodyData['token'];
    @$username = $bodyData['username'];
    @$sex = $bodyData['sex'];
    @$wechat = $bodyData['wechat'];
    @$bind_phone = $bodyData['bind_phone'];
    
    if(tokenVerify($token,$uid) && $uid && $token){
        
        $arr = array();
        if($username) $arr = array_merge($arr,array('username'=>$username));
        if($sex) $arr = array_merge($arr,array('sex'=>$sex));
        if($wechat) $arr = array_merge($arr,array('wechat'=>$wechat));
        if($bind_phone) $arr = array_merge($arr,array('bind_phone'=>$bind_phone));
        $update = snail_update("snail_user",$arr,"uid=$uid");
        if($update){
            header('HTTP/1.1 200 OK');
            echo json_encode ( array('status'=>200, 'msg'=>'修改成功') );exit();
        }else{
            header('HTTP/1.1 500 params error');
            echo json_encode ( array('status'=>500, 'msg'=>'error') );exit();
        }
    }else{
        header('HTTP/1.1 400 params error');
        echo json_encode ( array('status'=>400, 'msg'=>'params error') );exit();
    }
}


/**
 * @SWG\Get(path="/app/user/user.php?ac=info", tags={"user"},
 *   summary="获取用户信息",
 *   @SWG\Parameter(name="uid", type="integer", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
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
if($ac == 'info'){
    
    $uid    = empty($_GET['uid'])? 0 : intval($_GET['uid']);
    $token  = empty($_GET['token'])? 0 : $_GET['token'];
    
    
    if($uid > 0 && tokenVerify($token,$uid)){
        $info = getuserInfo($uid);
        $list = array();
        $list = getUserBonusInfo($uid);
        $info['bonus'] = $list;
        
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'data'=>$info) );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }

}
/****************************************************FUNC*************************************************************/

function getuserInfo($uid){
    global $conn;
    return $conn->query("SELECT username,mobile,sex,wechat,admin from `snail_user` WHERE `uid` = '$uid' ; ")->fetch_assoc();
}

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
        $a= $conn->query("INSERT INTO `snail_user_bonus` (bonus_type_id,uid,get_time,expiry_time) VALUES ('$v[type_id]',$uid,'$v[get_time]','$v[expiry_time]');");
        //$sql = "INSERT INTO `snail_user_bonus` (bonus_type_id,uid,get_time,expiry_time) VALUES ('$v[type_id]',$uid,'$v[get_time]','$v[expiry_time]');";
        
    }
}


//获取用户红包信息
function getUserBonusInfo($uid){
  global $conn;
  $list = array();
  $result = $conn->query("SELECT * from `snail_user_bonus` A LEFT JOIN `snail_bonus_type` B  ON A.bonus_type_id = B.type_id WHERE A.uid = $uid AND used_time = 0;");
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

function changePw($arr){
    global $conn;
    $username = "";
    $mobile = $arr['mobile'];
    $type   = $arr['type'];
    $password = md5($arr['password']);
    $do = $conn->query("UPDATE `snail_user` SET `password` = '$password' WHERE `mobile` = '$mobile' AND `type` = '$type';");
    return $do;
}

function changePwByUid($arr){
    global $conn;
    $uid = $arr['uid'];
    $password = md5($arr['password']);
    $do = $conn->query("UPDATE `snail_user` SET `password` = '$password' WHERE `uid` = '$uid';");
    return $do;
}

function checkVerify($mobile,$code){
    global $conn;
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
