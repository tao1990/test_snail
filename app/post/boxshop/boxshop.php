<?php

header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/boxshop/boxshop.php?ac=list", tags={"post"},
 *   summary="获取箱铺列表（ok）",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=false, in="query",example = "中文type类型"),
 *   @SWG\Parameter(name="region", type="string", required=false, in="query",example = "中文region"),
 *   @SWG\Parameter(name="marketing", type="string", required=false, in="query",example = "中文marketing"),
 *   @SWG\Parameter(name="money", type="string", required=false, in="query",example = "101,200(101到200之间)|0（无限制）|100（100以下）"),
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
  $region = empty($_GET['region'])? '':addslashes($_GET['region']);
  $marketing = empty($_GET['marketing'])? '':addslashes($_GET['marketing']);
  $money = empty($_GET['money'])? 0:addslashes($_GET['money']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = isset($_GET['pageCount'])?$_GET['pageCount']:10;
  if(!$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
  }else{
    $list = getBoxListByType($type,$region,$marketing,$money,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/boxshop/boxshop.php?ac=create", tags={"post"},
 *   summary="创建箱铺出租(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'token':'','uid':'','type':'','title':'','region':'','marketing':'','tags':'{'电梯':true,'无线网络':true}','money':'','area':'','content':'','imgs':'['/url1','/url2']','contacts_man':'','contacts_mobile':''}"
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
  $logFile = fopen("./boxlog.log", "w");
    $txt = "$bodyData -- ".date('Y-m-d H:i:s',time())."\n";
    fwrite($logFile, $txt);
    fclose($logFile); 
  $bodyData = json_decode($bodyData,true);
  $token  = empty($bodyData['token'])? '':$bodyData['token'];
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['title'] = empty($bodyData['title'])? '':$bodyData['title'];
    $arr['region'] = empty($bodyData['region'])? '':$bodyData['region'];
    $arr['marketing'] = empty($bodyData['marketing'])? '':$bodyData['marketing'];
    $arr['tags'] = empty($bodyData['tags'])? '':json_encode($bodyData['tags'],JSON_UNESCAPED_UNICODE);
    $arr['money'] = empty($bodyData['money'])? 0:$bodyData['money'];
    $arr['area'] = empty($bodyData['area'])? '':$bodyData['area'];
    $arr['content'] = empty($bodyData['content'])? '':$bodyData['content'];
    $arr['imgs'] = empty($bodyData['imgs'])? '':json_encode($bodyData['imgs'],JSON_UNESCAPED_UNICODE);
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    
    if( $arr['uid'] == 0 || !$arr['type'] || !$arr['title'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
    }else{
        $postId = createBoxShop($arr);
        if($postId){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200,'msg'=>'创建成功', 'postId'=>$postId,'amount'=>200) );exit();
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



function createBoxShop($arr){
  global $conn;
  $time = time();
  $post_id = 0;
  
  $sql="INSERT INTO `snail_post_boxshop` (uid,type,title,region,marketing,tags,money,area,content,imgs,contacts_man,contacts_mobile,status)
  VALUES (".$arr['uid'].",'".$arr['type']."','".$arr['title']."','".$arr['region']."','".$arr['marketing']."','".$arr['tags']."','".$arr['money']."','".$arr['area']."','".$arr['content']."','".$arr['imgs']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."',0);";
 
  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        $sql="INSERT INTO `snail_post_log` (insert_id,post_type,amount,uid,dateline) VALUES (".$insert_id.",'BOXSHOP',200,".$arr['uid'].",$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  
  return $post_id;
}

function getBoxListByType($type,$region,$marketing,$money,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    $sqlStr = "";
    $sqlStr.= $type? " AND type = '$type'":"";
    $sqlStr.= $region? " AND region = '$region'":"";
    $sqlStr.= $marketing? " AND marketing = '$marketing'":"";
    if($money != 0){
        $a = explode(',',$money);
        if(count($a)>1){
            $sqlStr.=" AND money BETWEEN ".$a[0]." AND ".$a[1];
        }else{
            $sqlStr.=" AND money <= ".$money;
        }
    }
    
    $total = $conn->query("SELECT * from `snail_post_boxshop` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_boxshop` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr limit $offset,$pageCount;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['id']       = $row['id'];
      $row2['typeCode']     = "BOXSHOP";  
      $row2['typeName'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['region']    = $row['region'];
      $row2['money']    = $row['money'];
      $row2['marketing']    = $row['marketing'];
      $row2['startDate']     = $row['start_date'];
      $list[] = $row2;
    }
   
    return array('total'=>$total,'list'=>$list);
}
