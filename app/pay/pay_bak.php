<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
//public $appId = "2018110862076568"; //记得是appid 不是以前的pid
//
////私钥值 2048的那种私钥 用户自己生成的
$rsaPrivateKey="MIIEpAIBAAKCAQEAw9rqnSjZRdtmRISzRlhdTpc5hw6ppuQ0mf/U24n95x1sJZR7fFVCdrsbdKhome4MYYoc9M1BHlpPwasAuDBG2jTs8PiVaSQGOGuG/Mq1v3vrnQVZY6JV5NhS7t4wqpC3fKF4zVnScZ+KsnaNqjDWQ+MxSErj5UfMJ6G4SZ2DmGzYER+HRtVMwTWztvcmLuRd1CZQKe2pnYQFy5/RC2t3vGaKFNI5jnqmV8wpL0O9NDTPkn+oNYKuDoo9OrGWRL5Be50Fvmq+BRcsqzSIac3/kBhPNM9ZxR2Y5Rr5gw/gTaF8iHnXFdJwD/zLPJbbFEpZWGEH2UV4Gt0B3UB9W9bTbQIDAQABAoIBAC5Z4uhqwHDt6ZgRp7PgOcTduTmjWLcsjt5bU27Zi4NkzTFfoJHeQ6qBwY+sQ4Uad0emPhAZe23JhdKZu8PbeQkvOVwWGJYXdnlnyTyOZND6bNpuZ6dSxe0w3P+3CeFz+li+hi5jMZC+Zz6xG/nJMQxD+mfXgXPDuCcw+MJDSt+JiDkmibTEIKWpu4ZOuF9KP2OGtWNsxt6qn+0LbKtnZuJ+YLHmIuy9WYFN7nYPB1CIMPme3o5rkX9abVerAoH1P1hd3WE0MQ53O2zhD2gilIoG9Fes4d5ToDDvFtdw1cS3grg5Xq/gGPHmJk57tcyb1F3JeH4paVhotN+rJN1akWkCgYEA+oupeMmeR4+jm28yBPXh772hzSD5RdZY0ECr/r7nOmFl1z3o9ES041j6GURdCmj7qnMlUyl7vh9ViAtS4H3DOk7CFnZo4N2LtQkxRByXyy/KLgquNV4VILTMoKy3QSo2DZUN6tAgiWa+nyZWaaJIdF6CAl1V8+2G/VBXeHAAuU8CgYEAyB50U21wPOtqhzJPEkTATEFlspGNIGltDmWxmzCfAb1J3UEHGVYMTVVidLu0M7V6JteW7/qpSViLjFDeyil9RIyItWU6o3DCIqC3okwOezUrENjFLmKE2KMyMT+XKE5pbdU3Dq6fss0Kq/X/72B8Fb1U7pShH5h9HVXhc28IAIMCgYEA3aPfcDMsCjJPkZl0rl62WNdw435gBh/wwYn0nY9UDplK9naWNkDxpI158hBAHo1w1QC37DGufipKB+e8kUuwAUza9fQaI5LZnHVdV9vTjLPiL4jTQ/LOzfgjbaBdHRCycKGDhk5H+kUiLLhFiX0+i3Nvn5fiCb/+wujn+GtcuJECgYEAvuNd7t6re1DMMt4oUrLGA3c2PNleFxa5ckxK2E/OvOgEd0q6LM7JuVLDMbqCr/hh5n8reQpPRKlzo4rYmVpuJV8wYGeJQbIjXMiVofiOr7QNumor3I0ZT1SMYjHYTBhPtPb3J8gmiXXQwitL0NjmRA5v34xjDTJ11e+/uE6nAWUCgYBgrnz1cuOSumQBOjoszv5Re/CgQcOS4cEgnIHoKFark379Jjw6plo7NIVARLtTrVg5ut6nLnThbIq4lO45C2icChtb09o5V7gkm7/pRFWsYV9mzOrQeVA/fqHLWqx2FzF5MdKsTHuXUkmZ7rdRs/y4dIxkfCgyRoUFdlU4k9FFOQ==";
////支付宝公钥（非生成私钥对应的那个公钥，生成的公钥叫应用公钥。上传到支付宝后台后会生成支付公钥)
//public $alipayrsaPublicKey="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2lggOh2XIpcEjKl7Ya6UCQPDtTA1qgMgRSW5sDShHK7KDxWImG7kFd3ny7O6xvz6FD1zJCshMNK5kuvu8YacPQbZuFNI3kDMB/hfmULaO7wPsXan6eiXerzp5/sy4BqG0GU53andoA+50fHq9IM4Pi4Le4frLm77Xspuqq91g4V3hjJ7PgJzcWICPMsB1vgDIkt2wpJjaex/Itf8e9/OtuFhLWr7KmG9ObH03+Cb6QAAa9QrztHIMWR88NS5U3/uPDyDqvIH2+AwV8oynZI9vSlirO5OHXWp1cOXZxEbxGPIvGOAXl5eTDrkuiAtNfmlcTb5uhj7R/WNpnpgaf/diQIDAQAB";
//
// //签名类型 
//public $signType = "RSA2";

require_once "../../api/alipay/AopSdk.php";
$c = new AopClient;        
$request = new AlipayTradeAppPayRequest();

/**业务参数**/
// $content['body'] = "Iphone6 16G"; //对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。非必填参数
$content['subject'] = "给用户充值10000钻石";//商品的标题/交易标题/订单标题/订单关键字等。
$content['out_trade_no'] = "2018111314428888";//商户网站唯一订单号
// $content['timeout_express'] = "30m";//该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。注：若为空，则默认为15d。
$content['total_amount'] =number_format(0.01,2,".","");//    订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]，
$content['product_code'] = "QUICK_MSECURITY_PAY";//    销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
// $content['goods_type'] = "0";//    商品主类型：0—虚拟类商品，1—实物类商品注：虚拟类商品不支持使用花呗渠道  非必填参数
$con = json_encode($content);//$content是biz_content的值,将之转化成字符串

/**业务参数**/

/**公共参数**/
$param = array();
$param['app_id'] = 2018110862076568;//支付宝分配给开发者的应用ID
$param['method'] = 'alipay.trade.app.pay';//接口名称
$param['charset'] = 'utf-8';//请求使用的编码格式
$param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类型
$param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
$param['version'] = '1.0';//调用的接口版本，固定为：1.0
$param['notify_url'] = 'http://58.247.87.162:4003/app/pay/zfb_cb.php';//支付宝服务器主动通知地址
$param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式
/**公共参数**/

 //生成签名
$paramStr = $c->getSignContent($param);
$sign = $c->alonersaSign($paramStr,$rsaPrivateKey,'RSA2');

$param['sign'] = $sign;
$str = $c->getSignContentUrlencode($param);//返回给客户端

print_r($str);die;