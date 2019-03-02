<?php

header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/package/package.php?ac=list", tags={"post"},
 *   summary="获取跨境包裹列表(OK)",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=true, in="query",example = "中文type类型"),
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
    $list = getPackageByType($type,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/package/package.php?ac=create", tags={"post"},
 *   summary="创建跨境包裹(OK)",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'token':'','uid':'','type':'','company':'','logo':'url','company_info':'','company_business':'','company_city':'','contacts_man':'','contacts_mobile':''}"
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
  $bodyData = @file_get_contents('php://input');
  snail_log($bodyData);
  $bodyData = json_decode($bodyData,true);
  $token = empty($bodyData['token'])? '':$bodyData['token'];
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['company'] = empty($bodyData['company'])? '':$bodyData['company'];
    $arr['logo'] = empty($bodyData['logo'])? '':$bodyData['logo'];
    $arr['company_info'] = empty($bodyData['company_info'])? '':$bodyData['company_info'];
    $arr['company_business'] = empty($bodyData['company_business'])? '':$bodyData['company_business'];
    $arr['company_city'] = empty($bodyData['company_city'])? '':$bodyData['company_city'];
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    
    if( $arr['uid'] == 0 || !$arr['type'] || !$arr['company'] || !$arr['contacts_man'] || !$arr['contacts_mobile'] || !$arr['logo'] || !$arr['company_info'] || !$arr['company_business'] || !$arr['company_city']){
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'请填写完整的信息') );exit();
    }else{
        $postId = createPackage($arr);
        if($postId){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200,'msg'=>'创建成功', 'postId'=>$postId,'amount'=>PRICE_200) );exit();
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


function createPackage($arr){
  global $conn;
  $time = time();
  $post_id = 0;
  $sql="INSERT INTO `snail_post_package` (uid,type,company,logo,company_info,company_business,company_city,contacts_man,contacts_mobile,status)
  VALUES (".$arr['uid'].",'".$arr['type']."','".$arr['company']."','".$arr['logo']."','".$arr['company_info']."','".$arr['company_business']."','".$arr['company_city']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."',0);";
 
  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        $sql="INSERT INTO `snail_post_log` (insert_id,post_type,amount,uid,dateline) VALUES (".$insert_id.",'PACKAGE',".PRICE_200.",".$arr['uid'].",$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  return $post_id;
}

function getPackageByType($type,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    
    $sqlStr = $type? " AND type = '$type'":"";
    $total = $conn->query("SELECT * from `snail_post_package` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_package` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr ORDER BY id DESC limit $offset,$pageCount;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['id']       = $row['id'];
      $row2['typeCode']     = "PACKAGE";  
      $row2['typeName'] = $row['type'];
      $row2['title']    = $row['company'];
      $row2['logo']     = $row['logo'];
      $row2['startDate']     = $row['start_date'];
      $list[] = $row2;
    }
   
    return array('total'=>$total,'list'=>$list);
}
/**************************************demo**********************************************/
/*
{
	"uid":1,
	"company":"公司名001",
	"type":"电商小包",
	"company_city":"公司名001",
	"company_info":"公司001介绍",
	"company_business":"公司业务描述。。。。",
	"contacts_man":"lianxiren",
	"contacts_mobile":"17621090121"
}
*/