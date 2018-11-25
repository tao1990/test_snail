<?php

//改变订单状态
function changeOrderStatus($orderSn,$arr){
    global $conn;
    $time = time();
    if($orderSn && $arr['status']){
        if($arr['status'] == "PAIDED"){
            $arr['pay_time'] = $time;
            $arr['status'] = "PAIDED";
            if($arr['paid_amount']) $arr['paid_amount'] = $arr['paid_amount'];
            if($arr['platform_id']) $arr['platform_id'] = $arr['platform_id'];
            if($arr['callback']) $arr['callback'] = $arr['callback'];
        }elseif($arr['status'] == "CANCEL"){
            $arr['status'] = "CANCEL";
        }
        snail_update('snail_order_info',$arr,"order_sn=$orderSn");
        
        //add edit order_adwall...
        $order = $conn->query("SELECT B.insert_id,B.post_type from `snail_order_info` A LEFT JOIN `snail_post_log` B ON A.post_id = B.id WHERE A.order_sn = '$orderSn' LIMIT 1; ")->fetch_assoc();   
        if($arr['status'] == "PAIDED"){
            $post_insert_id = $order['insert_id'];
            $post_type      = $order['post_type'];
            $tableName = "";
            if($post_type == "ADWALL") $tableName = 'snail_post_adwall';
            if($post_type == "OCCUP") $tableName = 'snail_post_occup';
            if($post_type == "BOXSHOP") $tableName = 'snail_post_boxshop';
            if($post_type == "HOUSE") $tableName = 'snail_post_house';
            if($post_type == "package") $tableName = 'snail_post_package';
            snail_update($tableName,array('status'=>2),"id=$post_insert_id");
        }
    }
    
}
?>
