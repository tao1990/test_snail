<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
error_reporting(E_ALL);
require_once "../../api/wxpay/wxpay.php";
$wxpay = new WxpayClass;  //实例化微信支付类
$verify_result = $wxpay->verifyNotify();

//$verify_result = $_POST;
$bodyData = @file_get_contents('php://input');
$bodyData = json_decode($bodyData,true);
$verify_result =$bodyData;
//print_r($verify_result);die;
if($verify_result['return_code'] == 'SUCCESS' && $verify_result['result_code'] == 'SUCCESS'){
    
    $out_trade_no = $verify_result['out_trade_no'];
    $trade_no     = $verify_result['transaction_id'];
    $trade_status = $verify_result['result_code'];
    $total_fee    = $verify_result['total_fee']/100;
    
    $orderInfo = $conn->query("SELECT * from `snail_order_info` WHERE `order_sn` = '$out_trade_no' LIMIT 1; ")->fetch_assoc();
    
    if($orderInfo['final_amount'] == $total_fee){
        changeOrderStatus($out_trade_no,"PAIDED");
        exit('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
    }
}else{
    exit('<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[ERROR]]></return_msg></xml>');
}


