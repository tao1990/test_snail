<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/occup/occup.php?ac=list", tags={"occup"},
 *   summary="获取招聘求职列表",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=true, in="query",example = "FULLTIME|PARTTIME|FIND"),
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
  $pageCount = $_GET['pageCount'];
  if(!in_array($type,array('FIND','FULLTIME','PARTTIME')) || !$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
  }else{
    $list = getOccupListByType($type,$page,$pageCount);
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>array('total'=>$list['total'],'list'=>$list['list'])) );exit();
    }
  }
}


/**
 * @SWG\Post(path="/app/post/occup/occup.php?ac=create", tags={"occup"},
 *   summary="创建求职招聘",
 *   description="",
 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'ad_name':'首页广告02',	'ad_img':'/upload/20181031/33d2360b6fb024e170425f9ce57a14c1.jpg',	'ad_remark':'test',	'ad_type':'INDEX',	'ad_show':1}"
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
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    
    if($arr['type'] == "FIND"){
        $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
        $arr['real_name'] = empty($bodyData['real_name'])? '':$bodyData['real_name'];
        $arr['sex'] = empty($bodyData['sex'])? "男":$bodyData['sex'];
        $arr['mobile'] = empty($bodyData['mobile'])? '':$bodyData['mobile'];
        $arr['birthday'] = empty($bodyData['birthday'])? '':$bodyData['birthday'];
        $arr['city'] = empty($bodyData['city'])? '':$bodyData['city'];
        $arr['now_state'] = empty($bodyData['now_state'])? '':$bodyData['now_state'];
        $arr['now_ident'] = empty($bodyData['now_ident'])? '':$bodyData['now_ident'];
        $arr['highest_degree'] = empty($bodyData['highest_degree'])? '':$bodyData['highest_degree'];
        $arr['job_experience'] = empty($bodyData['job_experience'])? '':$bodyData['job_experience'];
        $arr['job_desc'] = empty($bodyData['job_desc'])? '':$bodyData['job_desc'];
        
        if($arr['uid'] == 0 || !$arr['real_name'] || !$arr['mobile'] || !$arr['job_desc']){
            header('HTTP/1.1 400 ERROR');
            echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
        }
    }elseif($arr['type'] == "FULLTIME"){
        $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
        $arr['job_title'] = empty($bodyData['job_title'])? '':$bodyData['job_title'];
        $arr['company_industry'] = empty($bodyData['company_industry'])? "":$bodyData['company_industry'];
        $arr['pay'] = empty($bodyData['pay'])? '':$bodyData['pay'];
        $arr['welfare'] = empty($bodyData['welfare'])? '':$bodyData['welfare'];
        $arr['job_demand'] = empty($bodyData['job_demand'])? '':$bodyData['job_demand'];
        $arr['ed_demand'] = empty($bodyData['ed_demand'])? '':$bodyData['ed_demand'];
        $arr['year_demand'] = empty($bodyData['year_demand'])? '':$bodyData['year_demand'];
        $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
        $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
        
        if($arr['uid'] == 0 || !$arr['job_title'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
            header('HTTP/1.1 400 ERROR');
            echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
        }
    }elseif($arr['type'] == "PARTTIME"){
        $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
        $arr['job_title'] = empty($bodyData['job_title'])? '':$bodyData['job_title'];
        $arr['company_industry'] = empty($bodyData['company_industry'])? "":$bodyData['company_industry'];
        $arr['part_term'] = empty($bodyData['part_term'])? '':$bodyData['part_term'];
        $arr['part_interval_1'] = empty($bodyData['part_interval_1'])? '':$bodyData['part_interval_1'];
        $arr['part_interval_2'] = empty($bodyData['part_interval_2'])? '':$bodyData['part_interval_2'];
        $arr['part_payment'] = empty($bodyData['part_payment'])? '':$bodyData['part_payment'];
        $arr['part_address'] = empty($bodyData['part_address'])? '':$bodyData['part_address'];
        $arr['part_content'] = empty($bodyData['part_content'])? '':$bodyData['part_content'];
        $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
        $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
        
        if($arr['uid'] == 0 || !$arr['job_title'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
            header('HTTP/1.1 400 ERROR');
            echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
        }
    }else{
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }
    
    $postId = createOccup($arr);
    if($postId){
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'postId'=>$postId) );exit();
    }else{
        header('HTTP/1.1 500 SERVER ERROR');
        echo json_encode ( array('status'=>500, 'msg'=>'SERVER ERROR') );exit();
    }
      
  }else{
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
  }
  
}








/*******************************************************************func***************************************************************************/







function createOccup($arr){
  global $conn;
  $time = time();
  $post_id = 0;
  if($arr['type'] == "FIND"){
    
    $sql="INSERT INTO `snail_job_find` (uid,real_name,sex,mobile,birthday,city,now_state,now_ident,highest_degree,job_experience,job_desc)
  VALUES (".$arr['uid'].",'".$arr['real_name']."','".$arr['sex']."','".$arr['mobile']."','".$arr['birthday']."','".$arr['city']."','".$arr['now_state']."','".$arr['now_ident']."','".$arr['highest_degree']."','".$arr['job_experience']."','".$arr['job_desc']."');";
      
  }elseif($arr['type'] == "FULLTIME"){
    
    $sql="INSERT INTO `snail_job_release` (type,uid,job_title,company_industry,pay,welfare,job_demand,ed_demand,year_demand,contacts_man,contacts_mobile)
  VALUES ('FULLTIME',".$arr['uid'].",'".$arr['job_title']."','".$arr['company_industry']."','".$arr['pay']."','".$arr['welfare']."','".$arr['job_demand']."','".$arr['ed_demand']."','".$arr['year_demand']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."');";

  }elseif($arr['type'] == "PARTTIME"){
    $sql="INSERT INTO `snail_job_release` (type,uid,job_title,company_industry,part_term,part_interval_1,part_interval_2,part_payment,part_address,part_content,contacts_man,contacts_mobile)
  VALUES ('FULLTIME',".$arr['uid'].",'".$arr['job_title']."','".$arr['company_industry']."','".$arr['part_term']."','".$arr['part_interval_1']."','".$arr['part_interval_2']."','".$arr['part_payment']."','".$arr['part_address']."','".$arr['part_content']."','".$arr['contacts_man']."','".$arr['contacts_mobile']."');";
  }
  $conn->query($sql);
  $insert_id = $conn->insert_id;
  if($insert_id){
        $sql="INSERT INTO `snail_post_log` (post_id,post_type,uid,dateline) VALUES (".$insert_id.",'".$arr['type']."','".$arr['uid']."',$time)";
        $conn->query($sql);
        $post_id = $conn->insert_id;
  }
  
  return $post_id;
}

function getOccupListByType($type,$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    if($type == "FIND"){//找工作

      $total = $conn->query("SELECT * from `snail_job_find` WHERE `show` = 1 AND `start_date` < $time AND `end_date` > $time;")->num_rows;
      $sql="SELECT * from `snail_job_find` WHERE `show` = 1 AND `start_date` < $time AND `end_date` > $time limit $offset,$pageCount;";
      $result=$conn->query($sql);
      while ($row = mysqli_fetch_assoc($result))
      {
          $list[] = $row;
      }
        
    }elseif($type == "FULLTIME" || $type == "PARTTIME"){ //全职兼职
      $total = $conn->query("SELECT * from `snail_job_release` WHERE `show` = 1 AND `type` = '$type' AND `start_date` < $time AND `end_date` > $time;")->num_rows;
      $sql="SELECT * from `snail_job_release` WHERE `show` = 1 AND `type` = '$type' AND `start_date` < $time AND `end_date` > $time limit $offset,$pageCount;";
      $result=$conn->query($sql);
      while ($row = mysqli_fetch_assoc($result))
      {
          $list[] = $row;
      }
    }

    return array('total'=>$total,'list'=>$list);
}
