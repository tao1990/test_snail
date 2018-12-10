<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json; charset=utf-8");
    require_once("../comm/comm.php");
    
    function snail_alipay_create_order($orderSn,$amount,$subject,$body=""){
        require_once "../../api/alipay/aop/AopClient.php";
        require_once "../../api/alipay/aop/request/AlipayTradeAppPayRequest.php";
        $aop = new AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = ZFB_APPID;
        $aop->rsaPrivateKey = ZFB_APP_PRIVATE_KEY;
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA";
        $aop->alipayrsaPublicKey = ZFB_PUBLIC_KEY;
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $total_amount = number_format($amount,2,".","");
        $bizcontent = "{\"body\":\"$body\"," 
                        . "\"subject\": \"$subject\","
                        . "\"out_trade_no\": \"$orderSn\","
                        . "\"timeout_express\": \"30m\"," 
                        . "\"total_amount\": \"$total_amount\","
                        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                        . "}";
        $request->setNotifyUrl(NOTIFY_ZFB);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
    }
    
    
    function snail_wxpay_create_order($orderSn,$amount){
        require_once "../../api/wxpay/wxpay.php";
        $wxpay = new Wxpay;  //实例化微信支付类
        $wxpay->config = array(
            'appid' => "", /*微信开放平台上的应用id*/
            'mch_id' => "", /*微信申请成功之后邮件中的商户id*/
            'api_key' => "", /*在微信商户平台上自己设定的api密钥 32位*/
        );
        $wxpay->notify_url = NOTIFY_WX;
        $res = $wxpay->Wxpay($amount,$orderSn); //调用weixinpay方法
        return $res;
    }
    /*
    
    $ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);

    if($ac == 'query'){
        require_once "../../api/alipay/aop/AopClient.php";
        require_once "../../api/alipay/aop/request/AlipayTradeQueryRequest.php";
        
        
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = ZFB_APPID;
        $aop->method = "alipay.trade.query";
        $aop->rsaPrivateKey = ZFB_APP_PRIVATE_KEY;
        $aop->alipayrsaPublicKey = ZFB_PUBLIC_KEY;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayTradeQueryRequest();
        $request->setBizContent("{" .
        "\"out_trade_no\":\"2018111314428888\"," .
        "\"trade_no\":\"\"," .
        "\"org_pid\":\"\"" .
        "  }");
        $result = $aop->execute($request); 
        
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
        echo "成功";
        } else {
        echo "失败";
        }
  
    }
        */
    
    
    
    
    