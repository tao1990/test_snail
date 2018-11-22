<?php

header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../../comm/comm.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
$token = empty($_GET['token'])? '':addslashes($_GET['token']);


/**
 * @SWG\Get(path="/app/post/house/house.php?ac=list", tags={"post"},
 *   summary="房屋租借列表（ok）",
 *   description="",
 *   @SWG\Parameter(name="type", type="string", required=false, in="query",example = "中文type类型"),
 *   @SWG\Parameter(name="money", type="integer", required=false, in="query",example = "租金"),
 *   @SWG\Parameter(name="space", type="string", required=false, in="query",example = "户型(3|2|0)"),
 *   @SWG\Parameter(name="order", type="string", required=false, in="query",example = "ASC|DESC"),
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
  $rent = empty($_GET['money'])? 0:addslashes($_GET['money']);
  $space = empty($_GET['space'])? '':addslashes($_GET['space']);
  $order = empty($_GET['order'])? 'DESC':addslashes($_GET['order']);
  $page = isset($_GET['page'])?$_GET['page']:1;
  $pageCount = isset($_GET['pageCount'])?$_GET['pageCount']:10;
  if(!$page || !$pageCount){
    header('HTTP/1.1 400 ERROR');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
  }else{
    $list = getHouseList($type,$rent,$space,$order,$page,$pageCount);
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

 *   @SWG\Parameter(name="body", type="string", required=true, in="formData",
 *     description="body" ,example = "{	'token':'','uid':'','type':'宾馆','title':'出租宾馆啦~~~','tags':'{'冰箱':true,'空调':true}','traffic':'','space':'3|2|1','area':'35','rent':'','middle_man':1,'deposit_cash':1,'house_desc':'xxxx','imgs':'['/url1','/url2']','contacts_man':'','contacts_mobile':''}"
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
  $logFile = fopen("./houselog.log", "w");
    $txt = "$bodyData -- ".date('Y-m-d H:i:s',time())."\n";
    fwrite($logFile, $txt);
    fclose($logFile); 
  $bodyData = json_decode($bodyData,true);
  $token = empty($bodyData['token'])? '':$bodyData['token'];
  
  if(tokenVerify($token)){
    $arr['uid'] = empty($bodyData['uid'])? 0:$bodyData['uid'];
    $arr['type']  = empty($bodyData['type'])? '':$bodyData['type'];
    $arr['title'] = empty($bodyData['title'])? '':$bodyData['title'];
    $arr['tags'] = empty($bodyData['tags'])? '':json_encode($bodyData['tags'],JSON_UNESCAPED_UNICODE);
    $arr['traffic'] = empty($bodyData['traffic'])? '':$bodyData['traffic'];
    $arr['space'] = empty($bodyData['space'])? '':$bodyData['space'];
    $arr['area'] = empty($bodyData['area'])? 0:$bodyData['area'];
    $arr['rent'] = empty($bodyData['rent'])? 0:$bodyData['rent'];
    $arr['middle_man'] = empty($bodyData['middle_man'])? 0:$bodyData['middle_man'];
    $arr['deposit_cash'] = empty($bodyData['deposit_cash'])? 0:$bodyData['deposit_cash'];
    $arr['house_desc'] = empty($bodyData['house_desc'])? '':$bodyData['house_desc'];
    $arr['imgs'] = empty($bodyData['imgs'])? '':json_encode($bodyData['imgs'],JSON_UNESCAPED_UNICODE);
    $arr['contacts_man'] = empty($bodyData['contacts_man'])? '':$bodyData['contacts_man'];
    $arr['contacts_mobile'] = empty($bodyData['contacts_mobile'])? '':$bodyData['contacts_mobile'];
    
    if( $arr['uid'] == 0 || !$arr['type'] || !$arr['title'] || !$arr['contacts_man'] || !$arr['contacts_mobile']){
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array('status'=>400, 'msg'=>'params error') );exit();
    }else{
        $postId = createHouse($arr);
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

function getHouseList($type,$rent,$space,$order="DESC",$page=1,$pageCount=10){
    global $conn;
    $list = array();
    $time = time();
    $offset=($page-1)*$pageCount;
    $sqlStr = "";
    $sqlStr.= $type? " AND type = '$type'":"";
    $sqlStr.= $space? " AND space = '$space'":"";
    if($rent != 0){
        $a = explode(',',$rent);
        if(count($a)>1){
            $sqlStr.=" AND rent BETWEEN ".$a[0]." AND ".$a[1];
        }else{
            $sqlStr.=" AND rent <= ".$rent;
        }
    }
    
    $total = $conn->query("SELECT * from `snail_post_house` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr;")->num_rows;
    $sql="SELECT * from `snail_post_house` WHERE `status` = 1 AND `start_date` < $time AND `end_date` > $time $sqlStr ORDER BY start_date $order limit $offset,$pageCount;";
    //print_r($sql);
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
      $row2['id']       = $row['id'];
      $row2['typeCode']     = "HOUSE_RENT";
      $row2['typeName'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['space']    = getAreaInfo($row['space']);
      $row2['area']    = $row['area'];
      $row2['money']    = $row['rent'];
      $row2['startDate']     = $row['start_date'];
      $list[] = $row2;
    }
   
    return array('total'=>$total,'list'=>$list);
}

function getAreaInfo($str){
    $a = explode('|',$str);
    return $a[0]."房".$a[1]."厨".$a[2]."卫";
}
/**************************************demo**********************************************/
