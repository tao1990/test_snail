<?php

header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/occup/occup.php?ac=list", tags={"post"},
 *   summary="求职招聘列表(OK)",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=true, in="query",example = "全职招聘|兼职招聘|我要求职"),
 *   @SWG\Parameter(name="workType", type="string", required=true, in="query",example = "工种"),
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
  $workType = empty($_GET['workType'])? '':addslashes($_GET['workType']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = isset($_GET['pageCount'])?$_GET['pageCount']:10;
  
  if(!$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
  }else{
    $list = getOccupByType($type,$workType,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/occup/occup.php?ac=create", tags={"post"},
 *   summary="创建求职招聘(OK)",
 *   description="",

 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'token':'','uid':1,	'type':'全职招聘',	'title':'招聘001',work_type':'翻译',	'industry_type':'餐饮',	'salary':'5000',	'salary_type':'RMB',	'sex':'0',	'age':'10',	'content':'jian简述。。。。',	'contacts_man':'lianxiren',	'contacts_man':'lianxiren',	'contacts_mobile':'17621090121'}"
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
  snail_log($bodyData);
  $bodyData = json_decode($bodyData,true);
  $token = empty($bodyData['token'])? '':$bodyData['token'];
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['title'] = empty($bodyData['title'])? '':$bodyData['title'];
    $arr['work_type'] = empty($bodyData['work_type'])? '':$bodyData['work_type'];
    $arr['industry_type'] = empty($bodyData['industry_type'])? '':$bodyData['industry_type'];
    $arr['salary'] = empty($bodyData['salary'])? '':$bodyData['salary'];
    $arr['salary_type'] = empty($bodyData['salary_type'])? 'RUB':$bodyData['salary_type'];
    $arr['sex'] = empty($bodyData['sex'])? '男':$bodyData['sex'];
    $arr['age'] = empty($bodyData['age'])? '':$bodyData['age'];
    $arr['content'] = empty($bodyData['content'])? '':$bodyData['content'];
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    $amount = $arr['type']=="我要求职"? PRICE_100:PRICE_200;
    if( $arr['uid'] == 0 || !$arr['type'] || !$arr['title'] || !$arr['contacts_man'] || !$arr['contacts_mobile'] || !$arr['work_type'] || !$arr['industry_type'] || !$arr['age'] || !$arr['content']){
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'请填写完整的信息') );exit();
    }else{
        $postId = createOccup($arr,$amount);
        if($postId){
            header('HTTP/1.1 200 ok');
            echo json_encode ( array('status'=>200,'msg'=>'创建成功', 'postId'=>$postId,'amount'=>$amount) );exit();
        }else{
            header('HTTP/1.1 500 SERVER ERROR');
            echo json_encode ( array('status'=>500, 'msg'=>'SERVER ERROR') );exit();
        }
    }
    
  }else{
    header('HTTP/1.1 403 提交失败');
    echo json_encode ( array('status'=>403, 'msg'=>'提交失败') );exit();
  }
  
}




/*******************************************************************func***************************************************************************/


function createOccup($arr,$amount){
  global $conn;
  $time = time();
  $post_id = 0;
  $sql="INSERT INTO `snail_post_occup` (uid,type,title,work_type,industry_type,salary,salary_type,sex,age,content,contacts_man,contacts_mobile,status) VALUES (".$arr['uid'].",'".$arr['type']."','".$arr['title']."','".$arr['work_type']."','".$arr['industry_type']."','".$arr['salary']."','".$arr['salary_type']."','".$arr['sex']."','".$arr['age']."','".$arr['content']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."',0);";

  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        
        $post_type = "OCCUP";
        if($arr['type'] == "全职招聘") $post_type = "FULLTIME";
        if($arr['type'] == "兼职招聘") $post_type = "PARTTIME";
        if($arr['type'] == "我要求职") $post_type = "FIND";
        $sql="INSERT INTO `snail_post_log` (insert_id,post_type,amount,uid,dateline) VALUES (".$insert_id.",'$post_type',$amount,".$arr['uid'].",$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  return $post_id;
}

function getOccupByType($type,$workType,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    $sqlStr = "";
    $sqlStr.= $type? " AND type = '$type'":"";
    $sqlStr.= $workType? " AND work_type = '$workType'":"";
    $total = $conn->query("SELECT * from `snail_post_occup` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_occup` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr ORDER BY id DESC limit $offset,$pageCount;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['id']       = $row['id'];
      $row2['typeCode'] = getOccupCode($row['type']);
      $row2['typeName'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['tag1']    = getOccupTag($row['type']);
      $row2['tag2']    = $row['work_type'];
      $row2['tag3']    = $row['industry_type'];
      $row2['salary']    = $row['salary'] == 0? '面议':ceil($row['salary']);
      $row2['salaryType']    = $row['salary_type'];
      $row2['startDate']     = $row['start_date'];
      $list[] = $row2;
    }
   
    return array('total'=>$total,'list'=>$list);
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
/**************************************demo**********************************************/
/*
{
	"uid":1,
	"type":"全职招聘",
	"title":"全职招聘001",
	"work_type":"翻译",
	"industry_type":"餐饮",
	"salary":"5000",
	"salary_type":"RMB",
	"sex":"0",
	"age":"10",
	"content":"jian简述。。。。',
	"contacts_man":"lianxiren",
	"contacts_man":"lianxiren",
	"contacts_mobile":"17621090121"
}
*/