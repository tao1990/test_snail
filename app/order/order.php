<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../../api/sms/signatureRequest.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Post(path="/app/order/order.php?ac=create", tags={"order"},
 *   summary="创建订单(no)",
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
if($ac == 'create'){
    //phone check
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $uid    = empty($bodyData['uid'])? 0 : intval($bodyData['uid']);
    $token  = empty($bodyData['token'])? '' : intval($bodyData['token']);
    $postId = empty($bodyData['postId'])? 0 : intval($bodyData['postId']);
    $bonusId = empty($bodyData['bonusId'])? 0 : intval($bodyData['bonusId']);
    $payMethod = empty($bodyData['payMethod'])? '' : $bodyData['payMethod'];
    
    
    
    if($uid > 0 && $postId > 0 && in_array($payMethod,array('WECHAT','ALIPAY'))){
        
    }
    die;
    
    $mobile     = @$_GET['mobile'];
    $type       = @$_GET['type'] == "PWD" ? "PWD":"REG";
    //$hashCode - $bodyData['hashCode'];

    $firstNum = substr( $mobile, 0, 1 );
    if(strlen($mobile)==11 && ($firstNum == 1 || $firstNum == 7)){
        
        $templateCode = $type=="REG" ? SMS_REG_TEMPLATE_CN:SMS_PWD_TEMPLATE_CN;
        //$templateCode = SMS_REG_TEMPLATE_CN;
        $code = rand(1000,9999);
        updateVerify($mobile,$code);
        if($firstNum == 7){
            //$templateCode = SMS_REG_TEMPLATE_RU;
            $templateCode = $type=="REG" ? SMS_REG_TEMPLATE_RU:SMS_PWD_TEMPLATE_RU;
            $mobile = "00".$mobile;
        }
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
   
    $res = sendSms($mobile,$templateCode,$code);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200,'msg'=>'发送成功','data'=>$res) );exit();
}





/****************************************************FUNC*************************************************************/


//获取用户红包信息
function getUserBonusInfo($uid){
  global $conn;
  $list = array();
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
