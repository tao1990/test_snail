<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");
require_once("signatureRequest.php");
$action = empty($_GET['action'])? '':$_GET['action'];

$a = sendSms();
echo json_encode($a);exit();

function sendSms() {
    $params = array ();

    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIMd1LXayHKDA6";
    $accessKeySecret = "3nYeOlhy5EuD7csW6H6PDtr1UxzhQI";//

    // fixme 必填: 短信接收号码
    //$params["PhoneNumbers"] = "0079652998678";
    $params["PhoneNumbers"] = "17621090121";

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "略合科技";

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    //$params["TemplateCode"] = "SMS_145255795";//guonei
    $params["TemplateCode"] = "SMS_145295382";//国外

    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => 0412
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

