<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../../api/sms/signatureRequest.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


//require_once("../pay/pay.php");
//$orderSn = '2018120800998811';
//$amount = 100;
//$subject = "蜗牛时代广告费";
//$body = "一笔广告费";
//$mess = snail_alipay_create_order($orderSn,$amount,$subject,$body);
//
//print_r($mess);
//die;
/**
 * @SWG\Post(path="/app/order/order.php?ac=create", tags={"order"},
 *   summary="创建订单(调试中 未接通平台2)",
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
    snail_log($bodyData,'order');
    $bodyData = json_decode($bodyData,true);
    $uid    = empty($bodyData['uid'])? 0 : intval($bodyData['uid']);
    $token  = empty($bodyData['token'])? '' : $bodyData['token'];
    $postId = empty($bodyData['postId'])? 0 : intval($bodyData['postId']);
    $bonusId = empty($bodyData['bonusId'])? 0 : intval($bodyData['bonusId']);
    $payMethod = empty($bodyData['payMethod'])? '' : $bodyData['payMethod'];
  
    if($uid > 0 && tokenVerify($token,$uid) && $postId > 0 && in_array($payMethod,array('WECHAT','ALIPAY'))){
        
        //postid if in order_info 
        $order = $conn->query("SELECT * from `snail_order_info` WHERE `post_id` = ".$postId." limit 1; ")->fetch_assoc();
        //no 
        if(!$order){
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
               
            }else{
                header('HTTP/1.1 400 参数错误');
                echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
            }
        }
        if($order){
            
            //扣优惠券
            //if($bonusId>0) useBonus($bonusId,$order['order_sn']);
            if($order['final_amount'] == 0){
                //免支付
                $order['status'] = "PAIDED";
                changeOrderStatus($order['order_sn'],"PAIDED");
                $needPay = false;
            }else{
                //生成平台支付订单号
                $needPay = true;
                if($order['pay_method'] == "ALIPAY"){
                    require_once("../pay/pay.php");
                    $orderSn = $order['order_sn'];
                    $amount = $order['final_amount'];
                    $subject = "蜗牛时代广告费".$order['post_id'];
                    $body = "一笔广告费";
                    $payCode = snail_alipay_create_order($orderSn,$amount,$subject,$body);
                    //$resArr['payCode'] = $payCode;
                    $resArr['payInfo'] = $payCode;
                }
                if($order['pay_method'] == "WECHAT"){
                    require_once("../pay/pay.php");
                    $orderSn = $order['order_sn'];
                    $amount = $order['final_amount'];
                    $pay = snail_wxpay_create_order($orderSn,$amount);
                    //$resArr['payCode'] = $pay['payCode'];
//                    $resArr['sgin'] = $pay['sgin'];
                    $resArr['payInfo'] = $pay;
                }
                
                
            }
            //返回app
            $resArr['orderSn'] = $order['order_sn'];
            $resArr['needPay']  = $needPay;
            
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200, 'data'=>$resArr) );exit();
        }
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
    
    
}

/**
 * @SWG\Get(path="/app/order/order.php?ac=list", tags={"order"},
 *   summary="订单列表",
 *   description="",
 *   @SWG\Parameter(name="uid", type="integer", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="status", type="string", required=false, in="query",example = "CREATED|PAIDED|CANCEL"),
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
if($ac == "list"){
    $uid    = empty($_GET['uid'])? 0 : intval($_GET['uid']);
    $token  = empty($_GET['token'])? 0 : $_GET['token'];
    $status = empty($_GET['status'])? '' : $_GET['status'];

    if($uid > 0 && tokenVerify($token,$uid) && in_array($status,array('CREATED','PAIDED','CANNCEL',''))){
        $list = array();
        $list = getOrderList($uid,$status);
        
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
}

/**
 * @SWG\Post(path="/app/order/order.php?ac=cancel", tags={"order"},
 *   summary="订单取消",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'uid':'',	'token':'','orderSn':''"
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
if($ac == "cancel"){
    $bodyData = @file_get_contents('php://input');
    $bodyData = json_decode($bodyData,true);
    $uid    = empty($bodyData['uid'])? 0 : intval($bodyData['uid']);
    $token  = empty($bodyData['token'])? 0 : $bodyData['token'];
    $orderSn = empty($bodyData['orderSn'])? '' : $bodyData['orderSn'];
    if($uid > 0 && tokenVerify($token,$uid) && $orderSn){
        snail_update("snail_order_info",array('status'=>'CANCEL'),"order_sn=$orderSn AND uid=$uid");
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'msg'=>'取消成功') );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
}

/**
 * @SWG\Get(path="/app/order/order.php?ac=query", tags={"order"},
 *   summary="订单状态查询",
 *   description="",
 *   @SWG\Parameter(name="uid", type="integer", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="orderSn", type="string", required=true, in="query",example = ""),
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
if($ac == "query"){
    $uid    = empty($_GET['uid'])? 0 : intval($_GET['uid']);
    $token  = empty($_GET['token'])? 0 : $_GET['token'];
    $orderSn = empty($_GET['orderSn'])? '' : $_GET['orderSn'];
    if($uid > 0 && tokenVerify($token,$uid) && $orderSn){
       $orderInfo = $conn->query("SELECT status from `snail_order_info` WHERE `order_sn` = '$orderSn' LIMIT 1; ")->fetch_assoc();
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'data'=>$orderInfo['status']) );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
}


/****************************************************FUNC*************************************************************/




function getOrderList($uid,$status){
    global $conn;
    $list = [];
    $sqlStr = $status? " AND A.status = '$status'":"";
    $sql="SELECT * from `snail_order_info` A LEFT JOIN `snail_post_log` B ON A.post_id = B.id WHERE A.uid = $uid $sqlStr;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['order_sn'] = $row['order_sn'];  
      $row2['amount'] = $row['amount'];  
      $row2['final_amount'] = $row['final_amount'];  
      $row2['status'] = $row['status'];  
      $row2['type'] = $row['post_type']; 
      $row2['create_time'] = $row['create_time'];  
      $row2['create_time'] = $row['create_time']; 
      $row2['postId'] = $row['post_id'];
      $list[] = $row2;
    }
    return $list;
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
    $arr['term']        = DEFAULT_TERM;
    $arr['status']      = "CREATED";
    if($bonusInfo){
        $arr['bonus_id']      = $bonusInfo['bonus_id'];
        $arr['bonus_amount']  = $bonusInfo['type_money'];
        $arr['final_amount']  = $postInfo['amount']-$bonusInfo['type_money'];
        if($arr['final_amount'] <0) $arr['final_amount'] = 0;
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





