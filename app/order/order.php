<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../../api/sms/signatureRequest.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Post(path="/app/order/order.php?ac=create", tags={"order"},
 *   summary="创建订单(调试中 未接通平台)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'uid':'',	'token':'','postId':'','bonusId':'','payMethod':'WECHAT|ALIPAY'}"
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
if($ac == 'create'){
    //phone check
    $resArr = array();
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $uid    = empty($bodyData['uid'])? 0 : intval($bodyData['uid']);
    $token  = empty($bodyData['token'])? '' : $bodyData['token'];
    $postId = empty($bodyData['postId'])? 0 : intval($bodyData['postId']);
    $bonusId = empty($bodyData['bonusId'])? 0 : intval($bodyData['bonusId']);
    $payMethod = empty($bodyData['payMethod'])? '' : $bodyData['payMethod'];
  
    if($uid > 0 && tokenVerify($token,$uid) && $postId > 0 && in_array($payMethod,array('WECHAT','ALIPAY'))){
        $postInfo = getPostInfo($postId,$uid);
        if($postInfo){
            $postInfo['payMethod'] = $payMethod;
            $bonusInfo = null;
            if($bonusId>0){
                $bonusInfo = checkBonus($bonusId,$uid,$postInfo['post_type']);
                if(!$bonusInfo){
                    header('HTTP/1.1 403 优惠券不存在');
                    echo json_encode ( array('status'=>403, 'msg'=>'优惠券不存在') );exit();
                }
            }
            $order = createOrder($postInfo,$bonusInfo);
            
            if($order){
                
                //扣优惠券
                //if($bonusId>0) useBonus($bonusId,$order['order_sn']);
                if($order['final_amount'] == 0){
                    //免支付
                    $arr['status'] = "PAIDED";
                    changeOrderStatus($order['order_sn'],$arr);
                    $needPay = false;
                }else{
                    //生成平台支付订单号
                    $needPay = true;
                    
                }
                
                
                //返回app
                $resArr['order_sn'] = $order['order_sn'];
                $resArr['needPay']  = $needPay;
                
                header('HTTP/1.1 200 ok');
                echo json_encode ( array('status'=>200, 'data'=>json_encode($resArr)) );exit();
            }
        }
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
    die;
    
  
   
    
}





/****************************************************FUNC*************************************************************/
//改变订单状态
function changeOrderStatus($orderSn,$arr){
    global $conn;
    $time = time();
    if($orderSn && $arr['status']){
        if($arr['status'] == "PAIDED"){
            if($arr['paid_amount']) $arr['paid_amount'] = $arr['paid_amount'];
            $arr['pay_time'] = $time;
            if($arr['platform_id']) $arr['platform_id'] = $arr['platform_id'];
            if($arr['callback']) $arr['callback'] = $arr['callback'];
            $arr['status'] = "PAIDED";
        }elseif($arr['status'] == "CANCEL"){
            $arr['status'] = "CANCEL";
        }
    }
    return snail_update('snail_order_info',$arr,"order_sn=$orderSn");
    //return $conn->query("UPDATE `snail_user_bonus` SET `used_time` = $time,`order_sn` = '$order_sn' WHERE `bonus_id` = $bonusId;");
    
}

function getOrderSn(){
    return date('YmdH') . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    
}
function createOrder($postInfo,$bonusInfo){
    global $conn;
    $arr = array();
    $arr['post_id']     = $postInfo['id'];
    $arr['order_sn']    = getOrderSn();
    $arr['uid']         = $postInfo['uid'];
    $arr['amount']      = $postInfo['amount'];
    $arr['final_amount']= $postInfo['amount'];
    $arr['create_time'] = time();
    $arr['pay_method']  = $postInfo['payMethod'];
    $arr['status']      = "CREATED";
    if($bonusInfo){
        $arr['bonus_id']      = $bonusInfo['bonus_id'];
        $arr['bonus_amount']  = $bonusInfo['type_money'];
        $arr['final_amount']  = $postInfo['amount']-$bonusInfo['type_money'];
    }
    $order_id = snail_insert('snail_order_info',$arr);
    return $conn->query("SELECT * from `snail_order_info` WHERE `order_id` = $order_id LIMIT 1; ")->fetch_assoc();    
}

function useBonus($bonusId,$order_sn){
    global $conn;
    $time = time();
    return $conn->query("UPDATE `snail_user_bonus` SET `used_time` = $time,`order_sn` = '$order_sn' WHERE `bonus_id` = $bonusId;");
}

function getPostInfo($postId,$uid){
    global $conn;
    return $conn->query("SELECT * from `snail_post_log` WHERE `id` = $postId AND `uid`=$uid; ")->fetch_assoc();
}

function checkBonus($bonusId,$uid,$post_type){
    global $conn;
    $res = false;
    $bonus = $conn->query("SELECT * from `snail_user_bonus` WHERE `bonus_id` = $bonusId AND `uid`=$uid AND used_time = 0; ")->fetch_assoc();
    if($bonus){
        $info = $conn->query("SELECT * from `snail_bonus_type` WHERE `type_id` = ".$bonus['bonus_type_id']."; ")->fetch_assoc();
        if($info){
            $typeArr = json_decode($info['post_type_json']);
            if(in_array('ALL',$typeArr) || in_array($post_type,$typeArr)){
               $info['bonus_id'] =  $bonusId;
              $res = $info;
            }
        }
    }
    return $res;
}





