<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
//$appId = "20170925123213213"; //记得是appid 不是以前的pid
//
////私钥�?2048的那种私�?用户自己生成�?
//$rsaPrivateKey="MIIEugIBADANBgkqhkiG9w0BAQEFAASCBKQwggSgAgEAAoIBAQCCeAyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxNHXwZJYxCjy6QVgsw6uyz0IQx0Hq5NAn99jk5OfYVpfnyFOZwqF4xwG9LA9CAK/BYUSc/yGN2I9e/2hxkIcKpwBUmdecCQjW7L0hXXVoOP4d9I5EUNAvlwUYWkxZNlblZ3akir17JCcRyS99HpaU37RP+Tu+RqPk/ZtJh4KlT5uS0Y760bBUvSLMitFcVMJ+Gnt7GbLqRNAoGAO7NL+MUFu64Q+s+g1z9kwq5S1CbI3YbUfSDP1Uq3PJ4cEiwPB93fQew3hyQ9NKs+QkTNah90/n8zlhypIUsrUAsR/uBjbgs39jGGxfFwPWvjHyQiT6z2H9YCcgOHi2yWZchJIbTF0RP6qeJyZV02FOauvyFEcFxBA+NukI1wcCE=";
////支付宝公钥（非生成私钥对应的那个公钥，生成的公钥叫应用公钥。上传到支付宝后台后会生成支付公�?
//$alipayrsaPublicKey="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqx1N29eEAkv+InFjskPXo0CsJEidvm6dTLA13TXWfj8OvoMVag5Cim7byZCbI4JpGBRgNP7OnTeTsYesPx8QBBxxxxxxxxxxxxxfOW8i9RND48p0DxZ1SkGvQwnUQ8eJcy0CePH7SzQIDAQAB";
//
// //签名类型 
//$signType = "RSA2";
//

require_once "../../api/alipay/AopSdk.php";

        $c = new AopClient;    

        $request = new AlipayTradeAppPayRequest();
                print_r($c);    
die;
        /**业务参数**/
        // $content['body'] = "Iphone6 16G"; //对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。非必填参数
        $content['subject'] = "给用户充�?0000钻石";//商品的标�?交易标题/订单标题/订单关键字等�?
        $content['out_trade_no'] = $out_trade_no;//商户网站唯一订单�?
        // $content['timeout_express'] = "30m";//该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m�?5d。m-分钟，h-小时，d-天，1c-当天�?c-当天的情况下，无论交易何时创建，都在0点关闭）�?该参数数值不接受小数点， �?1.5h，可转换�?90m。注：若为空，则默认�?5d�?
        $content['total_amount'] =number_format($money,2,".","");//    订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]�?
        $content['product_code'] = "QUICK_MSECURITY_PAY";//    销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        // $content['goods_type'] = "0";//    商品主类型：0—虚拟类商品�?—实物类商品注：虚拟类商品不支持使用花呗渠道  非必填参�?
        $con = json_encode($content);//$content是biz_content的�?将之转化成字符串
        /**业务参数**/

        /**公共参数**/
        $param = array();
        $param['app_id'] = $c->appId;//支付宝分配给开发者的应用ID
        $param['method'] = 'alipay.trade.app.pay';//接口名称
        $param['charset'] = 'utf-8';//请求使用的编码格�?
        $param['sign_type'] = 'RSA2';//商户生成签名字符串所使用的签名算法类�?
        $param['timestamp'] = date("Y-m-d H:i:s");//发送请求的时间，格�?yyyy-MM-dd HH:mm:ss"
        $param['version'] = '1.0';//调用的接口版本，固定为：1.0
        $param['notify_url'] = WEBHOST.'/pay/alinotify';//支付宝服务器主动通知地址
        $param['biz_content'] = $con;//业务请求参数的集�?长度不限,json格式
        /**公共参数**/
        
         //生成签名
        $paramStr = $c->getSignContent($param);
        $sign = $c->alonersaSign($paramStr,$c->rsaPrivateKey,'RSA2');
        
        $param['sign'] = $sign;
        $str = $c->getSignContentUrlencode($param);//返回给客户端
        
        
        
        
        
        
        
        
        
require_once dirname(dirname(dirname(__FILE__)))."/core/public/plugin/alipay20171012/AopSdk.php";
$c = new AopClient;
        
$result = $c->rsaCheckV1($_POST,$c->alipayrsaPublicKey,$_POST['sign_type']);