<?php
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
header("Access-Control-Allow-Origin:*"); 
header('Access-Control-Allow-Headers:x-requested-with,content-type'); 
header("Content-type: application/json; charset=UTF-8");
//header ( "Content-type: text/html; charset=UTF-8" );
require_once("conn_mysql.php");
define("IMG_SITE","http://img.neotv.cn");
define("ENCRY_KEY","snailkey2018");
//aliyun sms
define("SMS_ACCESS_KEY","LTAIMd1LXayHKDA6");
define("SMS_ACCESS_SECRET","3nYeOlhy5EuD7csW6H6PDtr1UxzhQI");
define("SMS_SIGN_NAME","略合科技");
define("SMS_REG_TEMPLATE_CN","SMS_145255795");//国内手机号短信模板id
define("SMS_REG_TEMPLATE_RU","SMS_145295382");//俄罗斯手机号短信模板id
define("SMS_PWD_TEMPLATE_CN","SMS_145255794");//国内手机号短信模板id（忘记密码）
define("SMS_PWD_TEMPLATE_RU","SMS_150744055");//俄罗斯手机号短信模板id（忘记密码）


//zfb
define("ZFB_APPID","2018110862076568");
define("ZFB_APP_PRIVATE_KEY","MIIEpAIBAAKCAQEAw9rqnSjZRdtmRISzRlhdTpc5hw6ppuQ0mf/U24n95x1sJZR7fFVCdrsbdKhome4MYYoc9M1BHlpPwasAuDBG2jTs8PiVaSQGOGuG/Mq1v3vrnQVZY6JV5NhS7t4wqpC3fKF4zVnScZ+KsnaNqjDWQ+MxSErj5UfMJ6G4SZ2DmGzYER+HRtVMwTWztvcmLuRd1CZQKe2pnYQFy5/RC2t3vGaKFNI5jnqmV8wpL0O9NDTPkn+oNYKuDoo9OrGWRL5Be50Fvmq+BRcsqzSIac3/kBhPNM9ZxR2Y5Rr5gw/gTaF8iHnXFdJwD/zLPJbbFEpZWGEH2UV4Gt0B3UB9W9bTbQIDAQABAoIBAC5Z4uhqwHDt6ZgRp7PgOcTduTmjWLcsjt5bU27Zi4NkzTFfoJHeQ6qBwY+sQ4Uad0emPhAZe23JhdKZu8PbeQkvOVwWGJYXdnlnyTyOZND6bNpuZ6dSxe0w3P+3CeFz+li+hi5jMZC+Zz6xG/nJMQxD+mfXgXPDuCcw+MJDSt+JiDkmibTEIKWpu4ZOuF9KP2OGtWNsxt6qn+0LbKtnZuJ+YLHmIuy9WYFN7nYPB1CIMPme3o5rkX9abVerAoH1P1hd3WE0MQ53O2zhD2gilIoG9Fes4d5ToDDvFtdw1cS3grg5Xq/gGPHmJk57tcyb1F3JeH4paVhotN+rJN1akWkCgYEA+oupeMmeR4+jm28yBPXh772hzSD5RdZY0ECr/r7nOmFl1z3o9ES041j6GURdCmj7qnMlUyl7vh9ViAtS4H3DOk7CFnZo4N2LtQkxRByXyy/KLgquNV4VILTMoKy3QSo2DZUN6tAgiWa+nyZWaaJIdF6CAl1V8+2G/VBXeHAAuU8CgYEAyB50U21wPOtqhzJPEkTATEFlspGNIGltDmWxmzCfAb1J3UEHGVYMTVVidLu0M7V6JteW7/qpSViLjFDeyil9RIyItWU6o3DCIqC3okwOezUrENjFLmKE2KMyMT+XKE5pbdU3Dq6fss0Kq/X/72B8Fb1U7pShH5h9HVXhc28IAIMCgYEA3aPfcDMsCjJPkZl0rl62WNdw435gBh/wwYn0nY9UDplK9naWNkDxpI158hBAHo1w1QC37DGufipKB+e8kUuwAUza9fQaI5LZnHVdV9vTjLPiL4jTQ/LOzfgjbaBdHRCycKGDhk5H+kUiLLhFiX0+i3Nvn5fiCb/+wujn+GtcuJECgYEAvuNd7t6re1DMMt4oUrLGA3c2PNleFxa5ckxK2E/OvOgEd0q6LM7JuVLDMbqCr/hh5n8reQpPRKlzo4rYmVpuJV8wYGeJQbIjXMiVofiOr7QNumor3I0ZT1SMYjHYTBhPtPb3J8gmiXXQwitL0NjmRA5v34xjDTJ11e+/uE6nAWUCgYBgrnz1cuOSumQBOjoszv5Re/CgQcOS4cEgnIHoKFark379Jjw6plo7NIVARLtTrVg5ut6nLnThbIq4lO45C2icChtb09o5V7gkm7/pRFWsYV9mzOrQeVA/fqHLWqx2FzF5MdKsTHuXUkmZ7rdRs/y4dIxkfCgyRoUFdlU4k9FFOQ==");
define("ZFB_PUBLIC_KEY","MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2lggOh2XIpcEjKl7Ya6UCQPDtTA1qgMgRSW5sDShHK7KDxWImG7kFd3ny7O6xvz6FD1zJCshMNK5kuvu8YacPQbZuFNI3kDMB/hfmULaO7wPsXan6eiXerzp5/sy4BqG0GU53andoA+50fHq9IM4Pi4Le4frLm77Xspuqq91g4V3hjJ7PgJzcWICPMsB1vgDIkt2wpJjaex/Itf8e9/OtuFhLWr7KmG9ObH03+Cb6QAAa9QrztHIMWR88NS5U3/uPDyDqvIH2+AwV8oynZI9vSlirO5OHXWp1cOXZxEbxGPIvGOAXl5eTDrkuiAtNfmlcTb5uhj7R/WNpnpgaf/diQIDAQAB");

//bonus
define("SEND_BONUS_IDS","1,2,3,4,5");

function tokenCreate($uid){
    //return md5(md5($uid."#".ENCRY_KEY));
    $str = $uid."#".time();
    return snailEncrypt($str,ENCRY_KEY);
}
/**
 * token 验证
 */
function tokenVerify($token,$uid){
  //$token = "Yltqmm2VY2lsZ56a1";
  $decryToken = snailDecrypt($token,ENCRY_KEY);
  $check = explode('#',$decryToken);

  if($uid){
    if($check[0] == $uid && is_numeric($check[0])&& strlen($check[1])==10){
        return true;
    }else{
        return false;
    }
  }else{
    if(is_numeric($check[0])&& strlen($check[1])==10){
        return true;
    }else{
        return false;
    }
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
