<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/other/collect.php?ac=list", tags={"other"},
 *   summary="获取收藏列表",
 *   description="",
 * @SWG\Parameter(name="uid", type="string", required=true, in="query",example = "1"),
 * @SWG\Parameter(name="type", type="string", required=true, in="query",example = ""),
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
if($ac == 'list'){
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $uid = $_GET['uid'];
    $type = $_GET['type'];
    if(!$uid || !$type){
        header('HTTP/1.1 400 error');
        echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
    }else{
        $list = getCollectList($uid,$type);
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }
}

/**
 * @SWG\Get(path="/app/other/collect.php?ac=collect", tags={"other"},
 *   summary="收藏",
 *   description="",
 * @SWG\Parameter(name="uid", type="integer", required=true, in="query"),
 * @SWG\Parameter(name="type", type="string", required=true, in="query"),
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
if($ac == 'collect'){
    //$bodyData = @file_get_contents('php://input');
    //$bodyData = json_decode($bodyData,true);
    $uid    = empty($_GET['uid'])? 0:$_GET['uid'];
    $type   = empty($_GET['type'])? '':$_GET['type'];
    $id = empty($_GET['id'])? '':$_GET['id'];
    if($uid >0 && $type && $id){
        $res = doCollect($uid,$type,$id);
        if($res){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200, 'msg'=>'ok') );exit();
        }else{
            header('HTTP/1.1 400 error');
            echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
        }
    }else{
        header('HTTP/1.1 400 error');
        echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
    }
    
}






/****************************************************FUNC*************************************************************/

function getCollectList($uid,$type){
    global $conn;
    $resList = [];
    $sqlStr = "";
    $sqlStr.=" AND type = '".$type."'";
    $collectRes = $conn->query("SELECT * FROM `snail_collect` WHERE uid = $uid $sqlStr;");
    while ($row = mysqli_fetch_assoc($collectRes))
    {
      $list[] = $row;
    }
    if($list){
        $resList = getMoreInfo($list,$type);
    }
    return $resList;
}

function doCollect($uid,$type,$id){
    global $conn;
    $have = $conn->query("SELECT * FROM `snail_collect` WHERE uid = $uid AND type ='$type' AND insert_id = $id;")->fetch_row();
    if($have){
       $do = $conn->query("DELETE FROM `snail_collect` WHERE uid = $uid AND type ='$type' AND insert_id = $id;");
    }else{
       $do = $conn->query("INSERT INTO `snail_collect` (uid,type,insert_id) VALUES ($uid,'$type',$id);");
    }
    return $do;
}

function getMoreInfo($list,$type){
    global $conn;
    $resList = array();
    if(count($list)>0){
        $str = "";
        foreach($list as $v){
            $str.=$v['insert_id'].",";
        }
        $newstr = substr($str,0,strlen($str)-1); 
    
        //if($type == 'OCCUP' ||$type == 'FULLTIME' || $type == 'PARTTIME' || $type == 'FIND'){
        if($type == 'OCCUP'){
            $sql ="SELECT * from `snail_post_occup` WHERE id IN ($newstr);";
        }elseif($type == 'ADWALL'){
            $sql ="SELECT * from `snail_post_adwall` WHERE id IN ($newstr) ;";
        }elseif($type == 'PACKAGE'){
            $sql ="SELECT * from `snail_post_package` WHERE id IN ($newstr) ;";
        }elseif($type == 'BOXSHOP'){
            $sql ="SELECT * from `snail_post_boxshop` WHERE id IN ($newstr) ;";
        }elseif($type == 'HOUSE_RENT'){
            $sql ="SELECT * from `snail_post_house` WHERE id IN ($newstr) ;";
        }
        
        $res = $conn->query($sql);
        while ($row = mysqli_fetch_assoc($res))
        {
            if($type == 'OCCUP'){
                $row2['id']       = $row['id'];
                  $row2['typeCode'] = getOccupCode($row['type']);
                  $row2['typeName'] = $row['type'];
                  $row2['title']    = $row['title'];
                  $row2['tag1']    = getOccupTag($row['type']);
                  $row2['tag2']    = $row['work_type'];
                  $row2['tag3']    = $row['industry_type'];
                  $row2['salary']    = $row['salary'];
                  $row2['salaryType']    = $row['salary_type'];
                  $row2['startDate']     = $row['start_date'];
                  $resList[] = $row2;
            }elseif($type == 'ADWALL'){
                  $row2['id']       = $row['id'];
                  $row2['typeCode']     = "ADWALL";  
                  $row2['typeName'] = $row['type'];
                  $row2['title']    = $row['title'];
                  $row2['startDate']     = $row['start_date'];
                  $resList[] = $row2;
            }elseif($type == 'PACKAGE'){
                $row2['id']       = $row['id'];
                  $row2['typeCode']     = "PACKAGE";  
                  $row2['typeName'] = $row['type'];
                  $row2['title']    = $row['company'];
                  $row2['logo']     = $row['logo'];
                  $row2['startDate']     = $row['start_date'];
                  $resList[] = $row2;
            }elseif($type == 'BOXSHOP'){
                $row2['id']       = $row['id'];
              $row2['typeCode']     = "BOXSHOP";  
              $row2['typeName'] = $row['type'];
              $row2['title']    = $row['title'];
              $row2['region']    = $row['region'];
              $row2['money']    = $row['money'];
              $row2['marketing']    = $row['marketing'];
              $row2['startDate']     = $row['start_date'];
              $resList[] = $row2;
            }elseif($type == 'HOUSE_RENT'){
                $row2['id']       = $row['id'];
                  $row2['typeCode']     = "HOUSE_RENT";
                  $row2['typeName'] = $row['type'];
                  $row2['title']    = $row['title'];
                  $row2['space']    = getAreaInfo($row['space']);
                  $row2['area']    = $row['area'];
                  $row2['money']    = $row['rent'];
                  $row2['img']      = json_decode($row['imgs'])[0];
                  $row2['startDate']     = $row['start_date'];
                  $resList[] = $row2;
            }
          
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