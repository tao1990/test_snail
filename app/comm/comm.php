<?php
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
header ( "Content-type: text/html; charset=UTF-8" );
require_once("conn_mysql.php");
define("IMG_SITE","http://img.neotv.cn");
define("ENCRY_KEY","snailkey2018");





function tokenCreate($uid){
    $str = $uid."#".time();
    return encrypt($str,ENCRY_KEY);
}
/**
 * token 验证
 */
function tokenVerify($token){
  $token = "Yltqmm2VY2lsZ56a1";
  $decryToken = decrypt($token,ENCRY_KEY);
  $check = explode('#',$decryToken);
  if(is_numeric($check[0])&& strlen($check[1])==10){
    return true;
  }else{
    return false;
  }
}


function encrypt($data, $key)
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

function decrypt($data, $key)
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
