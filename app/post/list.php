<?php

//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);

/**
 * @SWG\Get(path="/app/post/list.php", tags={"post"},
 *   summary="我的发布(ok)",
 *   description="",
 * @SWG\Parameter(name="uid", type="string", required=true, in="query",example = ""),
 * @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
 * @SWG\Parameter(name="status", type="string", required=true, in="query",example = "1:已审核,2:待审核,3:审核未通过"),
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
    $uid    = empty($_GET['uid'])? 0 : intval($_GET['uid']);
    $token  = empty($_GET['token'])? 0 : $_GET['token'];
    $status = empty($_GET['status'])? 0 : intval($_GET['status']);
    if($uid > 0 && tokenVerify($token,$uid)){//
        $list = array();
        $list = getReleaseList($uid,$status);
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }


/****************************************************FUNC*************************************************************/

function getReleaseList($uid,$status){
    global $conn;
    $list = [];
    $sql ="SELECT insert_id,post_type from `snail_order_info` A LEFT JOIN `snail_post_log` B ON A.post_id = B.id WHERE A.uid = $uid AND status = 'PAIDED';";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $info = getPostInfo($row['insert_id'],$row['post_type'],$status);
        if($info) $list[] = $info;
        
    }
    return $list;

}

function getPostInfo($id,$type,$status){
    global $conn;
    $resList = null;
    if($type == 'OCCUP' || $type == 'PARTTIME' || $type == 'FULLTIME' || $type == 'FIND'){
        $sql ="SELECT * from `snail_post_occup` WHERE id = $id AND status = $status limit 1;";
        $list = $conn->query($sql)->fetch_assoc();
        if($list){
          $resList['id']       = $list['id'];
          $resList['type']     = "OCCUP";  
          $resList['typename'] = $list['type'];
          $resList['title']    = $list['title'];
          $resList['money']    = $list['salary'];
          $resList['money_type']    = $list['salary_type'];
          $resList['tag1']     = $list['type'];
          $resList['tag2']     = $list['work_type'];
          $resList['tag3']     = $list['industry_type'];
          $resList['start_date']     = $list['start_date'];
        }
    }elseif($type == 'ADWALL'){
        $sql ="SELECT * from `snail_post_adwall` WHERE id = $id AND status = $status limit 1;";
        $list = $conn->query($sql)->fetch_assoc();
        if($list){
          $resList['id']       = $list['id'];
          $resList['type']     = "ADWALL";  
          $resList['typename'] = $list['type'];
          $resList['title']    = $list['title'];
          $resList['money_type']    = "RUB";
          $resList['tag1']     = $list['type'];
          $resList['start_date']     = $list['start_date'];
        }
    }elseif($type == 'PACKAGE'){
        $sql ="SELECT * from `snail_post_package` WHERE id = $id AND status = $status limit 1;";
        $list = $conn->query($sql)->fetch_assoc();
        if($list){
          $resList['id']       = $list['id'];
          $resList['type']     = "PACKAGE";  
          $resList['typename'] = $list['type'];
          $resList['title']    = $list['company'];
          $resList['tag1']     = $list['company_city'];
          $resList['start_date']     = $list['start_date'];
        }
    }elseif($type == 'BOXSHOP'){
        $sql ="SELECT * from `snail_post_boxshop` WHERE id = $id AND status = $status limit 1;";
        $list = $conn->query($sql)->fetch_assoc();
        if($list){
          $resList['id']       = $list['id'];
          $resList['type']     = "BOXSHOP";  
          $resList['typename'] = $list['type'];
          $resList['title']    = $list['title'];
          $resList['money']    = $list['money'];
          $resList['money_type']    = "RUB";
          $resList['tag1']     = $list['type'];
          $resList['tag2']     = "面积".$list['area'];
          $resList['start_date']     = $list['start_date'];
        }
    }elseif($type == 'HOUSE_RENT'){
        $sql ="SELECT * from `snail_post_house` WHERE id = $id AND status = $status limit 1;";
        $list = $conn->query($sql)->fetch_assoc();
        if($list){
          $resList['type']     = "HOUSE_RENT";  
          $resList['typename'] = $list['type'];
          $resList['title']    = $list['title'];
          $resList['money']    = $list['rent'];
          $resList['money_type']    = "RUB";
          $resList['tag1']     = $list['type'];
          $resList['tag2']     = "面积".$list['area'];
          $resList['start_date']     = $list['start_date'];
        }
    }
    return $resList;
    
}


function getAreaInfo($str){
    $a = explode('|',$str);
    return $a[0]."房".$a[1]."厨".$a[2]."卫";
}
function getOccupCode($str){
    if($str == "全职招聘") return "FULLTIME";
    if($str == "兼职招聘") return "PARTTIME";
    if($str == "我要求职") return "PARTTIME";
}
function getOccupTag($str){
    if($str == "全职招聘") return "全职";
    if($str == "兼职招聘") return "兼职";
    if($str == "我要求职") return "求职";
}
