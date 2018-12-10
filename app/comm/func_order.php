<?php

/**
 * 改变订单状态
 * @params orderSn 订单号
 * @params status PAIDED|CANCEL
*/
function changeOrderStatus($orderSn,$status){
    global $conn;
    $time = time();
    if($orderSn && $status){
        if($status == "PAIDED"){
            $arr['pay_time'] = $time;
            $arr['status'] = "PAIDED";
        }elseif($status == "CANCEL"){
            $arr['status'] = "CANCEL";
        }
        snail_update('snail_order_info',$arr,"order_sn=$orderSn");
        
        //改变广告状态
        $order = $conn->query("SELECT B.insert_id,B.post_type from `snail_order_info` A LEFT JOIN `snail_post_log` B ON A.post_id = B.id WHERE A.order_sn = '$orderSn' LIMIT 1; ")->fetch_assoc();   
        if($arr['status'] == "PAIDED"){
            $post_insert_id = $order['insert_id'];
            $post_type      = $order['post_type'];
            $tableName = "";
            $end_date = $time + 86400*31;
            if($post_type == "ADWALL") $tableName = 'snail_post_adwall';
            if($post_type == "OCCUP" || $post_type == "FULLTIME" || $post_type == "PARTTIME" || $post_type == "FIND") $tableName = 'snail_post_occup';
            if($post_type == "BOXSHOP") $tableName = 'snail_post_boxshop';
            if($post_type == "HOUSE_RENT") $tableName = 'snail_post_house';
            if($post_type == "package") $tableName = 'snail_post_package';
            snail_update($tableName,array('status'=>1,'start_date'=>$time,'end_date'=>$end_date),"id=$post_insert_id");
        }
    }
    
}
?>
