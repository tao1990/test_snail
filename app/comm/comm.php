<?php
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
header ( "Content-type: application/json; charset=UTF-8" );
require_once("conn_mysql.php");
define("IMG_SITE","http://img.neotv.cn");
define("ENCRY_KEY","snailkey2018");
//aliyun sms
define("SMS_ACCESS_KEY","LTAIMd1LXayHKDA6");
define("SMS_ACCESS_SECRET","3nYeOlhy5EuD7csW6H6PDtr1UxzhQI");
define("SMS_SIGN_NAME","略合科技");
define("SMS_REG_TEMPLATE_CN","SMS_145255795");//国内手机号短信模板id
define("SMS_REG_TEMPLATE_RU","SMS_145295382");//俄罗斯手机号短信模板id
define("SMS_PW_TEMPLATE_CN","SMS_145255794");//国内手机号短信模板id（忘记密码）
define("SMS_PW_TEMPLATE_RU","SMS_150744055");//俄罗斯手机号短信模板id（忘记密码）

//bonus
define("SEND_BONUS_IDS","1,2,3,4,5");

function tokenCreate($uid){
    $str = $uid."#".time();
    return snailEncrypt($str,ENCRY_KEY);
}
/**
 * token 验证
 */
function tokenVerify($token){
  //$token = "Yltqmm2VY2lsZ56a1";
  $decryToken = snailDecrypt($token,ENCRY_KEY);
  $check = explode('#',$decryToken);
  if(is_numeric($check[0])&& strlen($check[1])==10){
    return true;
  }else{
    return false;
  }
}


function snailEncrypt($data, $key)
{
	$key	=	md5(md5($key));
    $x		=	0;
    $len	=	strlen($data);
    $l		=	strlen($key);
    $char   =   "";
    $str    =   "";
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l) 
        {
        	$x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++)
    {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}

function snailDecrypt($data, $key)
{
	$key = md5(md5($key));
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char   =   "";
    $str    =   "";
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l) 
        {
        	$x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++)
    {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
        {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }
        else
        {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}
?>
