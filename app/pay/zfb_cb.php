<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");

error_reporting(E_ALL);

require_once "../../api/alipay/aop/AopClient.php";
require_once "../../api/alipay/aop/request/AlipayTradeAppPayRequest.php";

snail_log(json_decode($_POST),'zfbpay');
    
$aop = new AopClient();
$aop->alipayrsaPublicKey = ZFB_PUBLIC_KEY;
//�˴���ǩ��ʽ�������µ�ʱ��ǩ����ʽһ��
$result = $aop->rsaCheckV1($_POST, $aop->alipayrsaPublicKey, "RSA");

if($result){
    if($_POST['trade_status'] == 'TRADE_SUCCESS' ){
    
       //����ǩͨ�����ʵ���²���out_trade_no��total_amount��seller_id
       //���޸Ķ�����
        $out_trade_no   = $_POST['out_trade_no'];
        $trade_no       = $_POST['trade_no'];
        $trade_status   = $_POST['trade_status'];
        $amount         = intval($_POST['total_amount']);
        
        $orderInfo = $conn->query("SELECT * from `snail_order_info` WHERE `order_sn` = '$out_trade_no' LIMIT 1; ")->fetch_assoc(); 
        if($orderInfo['order_sn'] == $out_trade_no && $orderInfo['final_amount'] == $amount){
            changeOrderStatus($out_trade_no,"PAIDED");
            echo 'success';
        }
        
    }elseif($_POST['trade_status'] == 'TRADE_FINISHED'){
        
    }else{
        echo 'fail';
    }
}