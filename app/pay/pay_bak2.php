<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json; charset=utf-8");
    require_once("../comm/comm.php");
    
    require_once "../../api/alipay/aop/AopClient.php";
    require_once "../../api/alipay/aop/request/AlipayTradeAppPayRequest.php";
    
    $notify_url='http://58.247.87.162:4003/app/pay/zfb_cb.php';
    
    $config = array(
    
    'appid' =>2018110862076568,//
    
    'rsaPrivateKey' =>"MIIEpAIBAAKCAQEAw9rqnSjZRdtmRISzRlhdTpc5hw6ppuQ0mf/U24n95x1sJZR7fFVCdrsbdKhome4MYYoc9M1BHlpPwasAuDBG2jTs8PiVaSQGOGuG/Mq1v3vrnQVZY6JV5NhS7t4wqpC3fKF4zVnScZ+KsnaNqjDWQ+MxSErj5UfMJ6G4SZ2DmGzYER+HRtVMwTWztvcmLuRd1CZQKe2pnYQFy5/RC2t3vGaKFNI5jnqmV8wpL0O9NDTPkn+oNYKuDoo9OrGWRL5Be50Fvmq+BRcsqzSIac3/kBhPNM9ZxR2Y5Rr5gw/gTaF8iHnXFdJwD/zLPJbbFEpZWGEH2UV4Gt0B3UB9W9bTbQIDAQABAoIBAC5Z4uhqwHDt6ZgRp7PgOcTduTmjWLcsjt5bU27Zi4NkzTFfoJHeQ6qBwY+sQ4Uad0emPhAZe23JhdKZu8PbeQkvOVwWGJYXdnlnyTyOZND6bNpuZ6dSxe0w3P+3CeFz+li+hi5jMZC+Zz6xG/nJMQxD+mfXgXPDuCcw+MJDSt+JiDkmibTEIKWpu4ZOuF9KP2OGtWNsxt6qn+0LbKtnZuJ+YLHmIuy9WYFN7nYPB1CIMPme3o5rkX9abVerAoH1P1hd3WE0MQ53O2zhD2gilIoG9Fes4d5ToDDvFtdw1cS3grg5Xq/gGPHmJk57tcyb1F3JeH4paVhotN+rJN1akWkCgYEA+oupeMmeR4+jm28yBPXh772hzSD5RdZY0ECr/r7nOmFl1z3o9ES041j6GURdCmj7qnMlUyl7vh9ViAtS4H3DOk7CFnZo4N2LtQkxRByXyy/KLgquNV4VILTMoKy3QSo2DZUN6tAgiWa+nyZWaaJIdF6CAl1V8+2G/VBXeHAAuU8CgYEAyB50U21wPOtqhzJPEkTATEFlspGNIGltDmWxmzCfAb1J3UEHGVYMTVVidLu0M7V6JteW7/qpSViLjFDeyil9RIyItWU6o3DCIqC3okwOezUrENjFLmKE2KMyMT+XKE5pbdU3Dq6fss0Kq/X/72B8Fb1U7pShH5h9HVXhc28IAIMCgYEA3aPfcDMsCjJPkZl0rl62WNdw435gBh/wwYn0nY9UDplK9naWNkDxpI158hBAHo1w1QC37DGufipKB+e8kUuwAUza9fQaI5LZnHVdV9vTjLPiL4jTQ/LOzfgjbaBdHRCycKGDhk5H+kUiLLhFiX0+i3Nvn5fiCb/+wujn+GtcuJECgYEAvuNd7t6re1DMMt4oUrLGA3c2PNleFxa5ckxK2E/OvOgEd0q6LM7JuVLDMbqCr/hh5n8reQpPRKlzo4rYmVpuJV8wYGeJQbIjXMiVofiOr7QNumor3I0ZT1SMYjHYTBhPtPb3J8gmiXXQwitL0NjmRA5v34xjDTJ11e+/uE6nAWUCgYBgrnz1cuOSumQBOjoszv5Re/CgQcOS4cEgnIHoKFark379Jjw6plo7NIVARLtTrVg5ut6nLnThbIq4lO45C2icChtb09o5V7gkm7/pRFWsYV9mzOrQeVA/fqHLWqx2FzF5MdKsTHuXUkmZ7rdRs/y4dIxkfCgyRoUFdlU4k9FFOQ==",//开发者私钥私钥
    
    'alipayrsaPublicKey'=>"MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2lggOh2XIpcEjKl7Ya6UCQPDtTA1qgMgRSW5sDShHK7KDxWImG7kFd3ny7O6xvz6FD1zJCshMNK5kuvu8YacPQbZuFNI3kDMB/hfmULaO7wPsXan6eiXerzp5/sy4BqG0GU53andoA+50fHq9IM4Pi4Le4frLm77Xspuqq91g4V3hjJ7PgJzcWICPMsB1vgDIkt2wpJjaex/Itf8e9/OtuFhLWr7KmG9ObH03+Cb6QAAa9QrztHIMWR88NS5U3/uPDyDqvIH2+AwV8oynZI9vSlirO5OHXWp1cOXZxEbxGPIvGOAXl5eTDrkuiAtNfmlcTb5uhj7R/WNpnpgaf/diQIDAQAB",//支付宝公钥
    
    'charset'=>strtolower('utf-8'),//编码
    
    'notify_url' =>$notify_url,//回调地址(支付宝支付成功后回调修改订单状态的地址)
    
    'payment_type' =>1,//(固定值)
    
    'seller_id' =>'',//收款商家账号
    
    'charset' => 'utf-8',//编码
    
    'sign_type' => 'RSA2',//签名方式
    
    'timestamp' =>date("Y-m-d H:i:s"),
    
    'version' =>"1.0",//固定值
    
    'url'  => 'https://openapi.alipay.com/gateway.do',//固定值
    
    'method' => 'alipay.trade.app.pay',//固定值
    
    );

   
    
    $aop = new \AopClient();
    
    $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
    
    $aop->appId = $config['appid'];
    
    $aop->rsaPrivateKey = $config['rsaPrivateKey'];
    
    $aop->format = "json";
    
    $aop->charset = "UTF-8";
    
    $aop->signType = "RSA2";
    
    $aop->alipayrsaPublicKey=$config['alipayrsaPublicKey'];
    
    //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
    
    $request = new \AlipayTradeAppPayRequest();
    
    //SDK已经封装掉了公共参数，这里只需要传入业务参数
    
     
    
    $bizcontent = json_encode([
    
      //'body'=>'**',
    
      'subject'=>'蜗牛时代广告墙广告',
    
      'out_trade_no'=> '2018111314428888',//此订单号为商户唯一订单号
    
      'total_amount'=>number_format(0.01,2,".",""),//保留两位小数
    
      'product_code'=>'QUICK_MSECURITY_PAY'
    
    ]);
    
    $request->setNotifyUrl($config['notify_url']);
    
    $request->setBizContent($bizcontent);
    
    //这里和普通的接口调用不同，使用的是sdkExecute
    
    $response = $aop->sdkExecute($request);
    
    //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
    
    $datas=$response;//就是orderString 可以直接给客户端请求，无需再做处理。
    
    $arr['code']=0;
    
    $arr['msg']='2018111314428888';
    
    $arr['info']=$datas;
    
    print_r($arr);die;
    
    echo json_encode($this->arr);exit;