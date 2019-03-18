<?php

header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/touristdest/touristdest.php?ac=list", tags={"post"},
 *   summary="获取旅游地接列表(OK)",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=false, in="query",example = "类型（可选）"),
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
  $type = empty($_GET['type'])? '':addslashes($_GET['type']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = isset($_GET['pageCount'])?$_GET['pageCount']:10;
  if(!$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
  }else{
    $list = getListByType($type,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/touristdest/touristdest.php?ac=create", tags={"post"},
 *   summary="创建旅游地接(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{'token':'',	'uid':'','type':'非必传',	'tags':'','content':'','logo':'','contacts_man':'','contacts_mobile':'','contacts_wechat':''}"
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
    
  //$token = empty($_GET['token'])? '':$_GET['token'];
  $bodyData = @file_get_contents('php://input');
  //snail_log($bodyData);
  $bodyData = json_decode($bodyData,true);
  
  $token  = empty($bodyData['token'])? '':$bodyData['token'];
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['title'] = empty($bodyData['title'])? '':$bodyData['title'];
    $arr['tags'] = empty($bodyData['tags'])? '':json_encode($bodyData['tags'],JSON_UNESCAPED_UNICODE);
    $arr['content'] = empty($bodyData['content'])? '':$bodyData['content'];
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    $arr['contacts_wechat'] = empty($bodyData['contacts_wechat'])? '':$bodyData['contacts_wechat'];
    
    $arr['filter_tag'] = '';
    foreach($bodyData['tags'] as $v){
        foreach($v as $k=>$j){
            if($j){
                $arr['filter_tag'].=$k."|";
            }
        }
    }
    if($arr['uid'] == 0 || !$arr['title'] || !$arr['tags'] || !$arr['content'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
        header('HTTP/1.1 400 请填写完整的信息');
        echo json_encode ( array('status'=>400, 'msg'=>'请填写完整的信息') );exit();
    }else{
        $postId = createAd($arr);
        if($postId){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200,'msg'=>'创建成功', 'postId'=>$postId,'amount'=>PRICE_200) );exit();
        }else{
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode ( array('status'=>500, 'msg'=>'SERVER ERROR') );exit();
        }
    }
    
  }else{
    header('HTTP/1.1 400 提交失败');
    echo json_encode ( array('status'=>400, 'msg'=>'提交失败') );exit();
  }
  
}




/*******************************************************************func***************************************************************************/



function createAd($arr){
  global $conn;
  $time = time();
  $post_id = 0;
  $sql="INSERT INTO `snail_post_touristdest` (uid,type,title,tags,filter_tag,content,logo,contacts_man,contacts_mobile,contacts_wechat,status)
  VALUES (".$arr['uid'].",'".$arr['type']."','".$arr['title']."','".$arr['tags']."','".$arr['filter_tag']."','".$arr['content']."','".$arr['logo']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."','".$arr['contacts_wechat']."',0);";

  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        $sql="INSERT INTO `snail_post_log` (insert_id,post_type,amount,uid,dateline) VALUES (".$insert_id.",'TOURISTDEST',".PRICE_200.",".$arr['uid'].",$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  
  return $post_id;
}

function getListByType($type,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    
    //$sqlStr = $type? " AND type = '$type'":"";
    $sqlStr = $type? " AND filter_tag LIKE '%$type%'":"";
    $total = $conn->query("SELECT * from `snail_post_touristdest` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_touristdest` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr ORDER BY id DESC limit $offset,$pageCount;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['id']       = $row['id'];
      $row2['typeCode']     = "TOURISTDEST";  
      $row2['title'] = $row['title'];
      $row2['logo'] = $row['logo'];
      $row2['typeName'] = $row['type'];
      $row2['tags'] = json_decode($row['tags']);
      $row2['filterTag'] = $row['filter_tag'];
      $row2['startDate']     = $row['start_date'];
      $list[] = $row2;
    }
   
    return array('total'=>$total,'list'=>$list);
}
