<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("signatureRequest.php");
$action = empty($_GET['action'])? '':$_GET['action'];

$a = sendSms();
echo json_encode($a);exit();

function sendSms() {
    $params = array ();

    // *** ���û���д���� ***
    // fixme ����Ƿ�����https
    $security = false;

    // fixme ����: ����� https://ak-console.aliyun.com/ ȡ������AK��Ϣ
    $accessKeyId = "LTAIMd1LXayHKDA6";
    $accessKeySecret = "3nYeOlhy5EuD7csW6H6PDtr1UxzhQI";//

    // fixme ����: ���Ž��պ���
    //$params["PhoneNumbers"] = "0079652998678";
    $params["PhoneNumbers"] = "17621090121";

    // fixme ����: ����ǩ����Ӧ�ϸ�"ǩ������"��д����ο�: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "�ԺϿƼ�";

    // fixme ����: ����ģ��Code��Ӧ�ϸ�"ģ��CODE"��д, ��ο�: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    //$params["TemplateCode"] = "SMS_145255795";//guonei
    $params["TemplateCode"] = "SMS_145295382";//����

    // fixme ��ѡ: ����ģ�����, ����ģ���д��ڱ�����Ҫ�滻��Ϊ������
    $params['TemplateParam'] = Array (
        "code" => 0412
    );

    // fixme ��ѡ: ���÷��Ͷ�����ˮ��
    $params['OutId'] = time();

    // fixme ��ѡ: ���ж�����չ��, ��չ���ֶο�����7λ�����£������������û�����Դ��ֶ�
    $params['SmsUpExtendCode'] = "1234567";


    // *** ���û���д���ֽ���, ���´������ޱ�Ҫ������� ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // ��ʼ��SignatureRequestʵ���������ò�����ǩ���Լ���������
    $helper = new SignatureRequest();

    // �˴����ܻ��׳��쳣��ע��catch
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

