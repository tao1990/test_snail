<?php
//助手方法
/**
 * token 创建
 */
function tokenCreate($uid){
    //return md5(md5($uid."#".ENCRY_KEY));
    $str = $uid."#".time();
    return snailEncrypt($str,ENCRY_KEY);
}

/**
 * token 验证
 */
function tokenVerify($token,$uid=0){
  //$token = "Yltqmm2VY2lsZ56a1";
  //Ym1clm6YZ2RxZJeXZg==
  $decryToken = snailDecrypt($token,ENCRY_KEY);
  $check = explode('#',$decryToken);

  if($uid>0){
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
/**
 * 加密
 */
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

/**
 * 解密
 */
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

function snail_log($bodyData,$name="log"){
    if($name){
        $logName = "/$name.log";
    }
    $logFile = fopen($_SERVER['DOCUMENT_ROOT'].$logName, "a+");
    $txt = date('Y-m-d H:i:s',time())."--- $bodyData\r\n";
    fwrite($logFile, $txt);
    fclose($logFile); 
}
?>
