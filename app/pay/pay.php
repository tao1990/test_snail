<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json; charset=utf-8");
    require_once("../comm/comm.php");
    
    $ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
    
    if($ac == 'create'){
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
        //$total_amount = number_format(0.01,2,".","");
        $bizcontent = "{\"body\":\"这是一笔广告费\"," 
                        . "\"subject\": \"蜗牛时代广告墙广告\","
                        . "\"out_trade_no\": \"2018111314428888\","
                        . "\"timeout_express\": \"30m\"," 
                        . "\"total_amount\": \"0.01\","
                        . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                        . "}";
        $request->setNotifyUrl("http://58.247.87.162:4003/app/pay/zfb_cb.php");
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
    }
    
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
        "\"out_trade_no\":\"201811131442888\"," .
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
    
    
    
    
    
    