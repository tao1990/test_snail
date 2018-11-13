<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/house/house.php?ac=list", tags={"post"},
 *   summary="房屋租借列表（未实现）",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=true, in="query"),
 *   @SWG\Parameter(name="page", type="integer", required=true, in="query",example = "1"),
 *   @SWG\Parameter(name="pageCount", type="integer", required=true, in="query",example = "10"),
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
    die;
  $type = empty($_GET['type'])? '':addslashes($_GET['type']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = isset($_GET['pageCount'])?$_GET['pageCount']:10;
  if(!$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
  }else{
    $list = getAdWallListByType($type,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/house/house.php?ac=create", tags={"post"},
 *   summary="创建房屋租借(OK)",
 *   description="",
 *   @SWG\Parameter(name="token", type="string", required=true, in="query",
 *     description="token"
 *   ),
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'uid':'','type':'宾馆','title':'出租宾馆啦~~~','tags':'{'冰箱':true,'空调':true}','traffic':'','space':'3|2|1','area':'35','rent':'','middle_man':1,'deposit_cash':1,'house_desc':'xxxx','imgs':'['/url1','/url2']','contacts_man':'','contacts_mobile':''}"
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
  $token = empty($_GET['token'])? '':$_GET['token'];
  $bodyData = @file_get_contents('php://input');
  $bodyData = json_decode($bodyData,true);
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['title'] = empty($bodyData['title'])? '':$bodyData['title'];
    $arr['tags'] = empty($bodyData['tags'])? '':$bodyData['tags'];
    $arr['traffic'] = empty($bodyData['traffic'])? '':$bodyData['traffic'];
    $arr['space'] = empty($bodyData['space'])? '':$bodyData['space'];
    $arr['area'] = empty($bodyData['area'])? 0:$bodyData['area'];
    $arr['rent'] = empty($bodyData['rent'])? 0:$bodyData['rent'];
    $arr['middle_man'] = empty($bodyData['middle_man'])? 0:$bodyData['middle_man'];
    $arr['deposit_cash'] = empty($bodyData['deposit_cash'])? 0:$bodyData['deposit_cash'];
    $arr['house_desc'] = empty($bodyData['house_desc'])? '':$bodyData['house_desc'];
    $arr['imgs'] = empty($bodyData['imgs'])? '':$bodyData['imgs'];
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    
    if( $arr['uid'] == 0 || !$arr['type'] || !$arr['title'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'params error') );exit();
    }else{
        $postId = createHouse($arr);
        if($postId){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200, 'postId'=>$postId) );exit();
        }else{
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode ( array('status'=>500, 'msg'=>'SERVER ERROR') );exit();
        }
    }
    
  }else{
    header('HTTP/1.1 403 ERROR');
    echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
  }
  
}




/*******************************************************************func***************************************************************************/


function createHouse($arr){
  global $conn;
  $time = time();
  $post_id = 0;
  $sql="INSERT INTO `snail_post_house` (uid,type,title,tags,traffic,space,area,rent,middle_man,deposit_cash,house_desc,imgs,contacts_man,contacts_mobile,status)
  VALUES (".$arr['uid'].",'".$arr['type']."','".$arr['title']."','".$arr['tags']."','".$arr['traffic']."','".$arr['space']."','".$arr['area']."','".$arr['rent']."','".$arr['middle_man']."','".$arr['deposit_cash']."','".$arr['house_desc']."','".$arr['imgs']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."',0);";
 
  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        $sql="INSERT INTO `snail_post_log` (post_id,post_type,uid,dateline) VALUES (".$insert_id.",'HOUSE_RENT','".$arr['uid']."',$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  return $post_id;
}

function getAdWallListByType($type,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    
    $sqlStr = $type? " AND type = '$type'":"";
    $total = $conn->query("SELECT * from `snail_post_adwall` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_adwall` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr limit $offset,$pageCount;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $list[] = $row;
    }
   
    return array('total'=>$total,'list'=>$list);
}
/**************************************demo**********************************************/
