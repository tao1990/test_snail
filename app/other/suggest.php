<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);

/**
 * @SWG\Post(path="/app/other/suggest.php?ac=suggest", tags={"other"},
 *   summary="建议箱(ok)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'suggest':'',	'contact_man':'','contact_mobile':'','wechat':''}"
 *   ),
 * @SWG\Response(
 *   response=200,
 *   description="ok response",
 *   ),
 * @SWG\Response(
 *   response="default",
 *   description="unexpected error",
 *   )
 * )
 */
if($ac == 'suggest'){
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $suggest   = empty($bodyData['suggest'])? '':$bodyData['suggest'];
    $contact_man = empty($bodyData['contact_man'])? '':$bodyData['contact_man'];
    $contact_mobile = empty($bodyData['contact_mobile'])? '':$bodyData['contact_mobile'];
    $wechat = empty($bodyData['wechat'])? '':$bodyData['wechat'];
    if($suggest && $contact_man && $contact_mobile){
        $res = doSuggest($suggest,$contact_man,$contact_mobile,$wechat);
        if($res){
            header('HTTP/1.1 200 提交成功！');
            echo json_encode ( array('status'=>200, 'msg'=>'提交成功！') );exit();
        }else{
            header('HTTP/1.1 400 提交失败！');
            echo json_encode ( array('status'=>400, 'msg'=>'提交失败！') );exit();
        }
    }else{
        header('HTTP/1.1 400 请填写完整信息');
        echo json_encode ( array('status'=>400, 'msg'=>'请填写完整信息') );exit();
    }
    
}






/****************************************************FUNC*************************************************************/


function doSuggest($suggest,$contact_man,$contact_mobile,$wechat){
    global $conn;
    $conn->query("INSERT INTO `snail_suggest` (suggest,contact_man,contact_mobile,wechat) VALUES ('$suggest','$contact_man','$contact_mobile','$wechat');");
    return true;
}


