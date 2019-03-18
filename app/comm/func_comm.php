<?php

/**
 * @name 发送系统消息
 * @params @name uid
 * @params @name type
 * @params @name title
 * @params @name msg
*/
function sendMessage($uid,$type,$title,$msg){
    global $conn;
    $time = time();
    if($uid && $type && $title && $msg){
        $arr['uid']     = $uid;
        $arr['type']    = $type;
        $arr['title']   = $title;
        $arr['content'] = $msg;
        $arr['unread']  = 1;
        $arr['dateline']  = time();
        snail_insert('snail_message',$arr);
    }
}


function getOrderSnByTypeId($type,$id){
    global $conn;
  
    $postid = $conn->query("SELECT id FROM `snail_post_log` WHERE `post_type` = '$type' AND `insert_id`=$id limit 1; ")->fetch_assoc();
    $postid = $postid['id'];
    $orderSn = $conn->query("SELECT order_sn FROM `snail_order_info` WHERE `post_id` = '$postid' limit 1; ")->fetch_assoc();
    return $orderSn['order_sn'];
}
?>
