<?php

//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
//$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/other/search.php", tags={"other"},
 *   summary="搜索协议(ok)",
 *   description="",
 * @SWG\Parameter(name="keywords", type="string", required=true, in="query",example = ""),
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
$keywords = empty($_GET['keywords'])? '':preg_replace('# #','',strip_tags(addslashes($_GET['keywords'])));
if($keywords){
    $list = getSearchList($keywords);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
}else{
    header('HTTP/1.1 400 error');
    echo json_encode ( array('status'=>400, 'msg'=>'error') );exit();
}











/****************************************************FUNC*************************************************************/

function getSearchList($keywords){
   $arr = [];
   $list1 = getAdWall($keywords);
   $list2 = getBoxShop($keywords);
   $list3 = getHouse($keywords);
   $list4 = getOccup($keywords);
   $list5 = getPackage($keywords);
   $arr = array_merge($list1,$list2);
   $arr = array_merge($arr,$list3);
   $arr = array_merge($arr,$list4);
   $arr = array_merge($arr,$list5);
    
   return $arr;
}

function getAdWall($keywords){
    global $conn;
    $time = time();
    $list = [];
    $res = $conn->query("SELECT id,type,title,content,start_date,end_date FROM `snail_post_adwall` WHERE status = 1 AND end_date>$time AND start_date<$time AND title LIKE '%$keywords%';");
    while ($row = mysqli_fetch_assoc($res))
    {
      $row2['id']       = $row['id'];
      $row2['type']     = "ADWALL";  
      $row2['typename'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['money']    = '';
      $row2['tag1']     = $row['type'];
      $row2['start_date']     = $row['start_date'];
      $list[] = $row2;
    }
    return $list;
}

function getBoxShop($keywords){
    global $conn;
    $time = time();
    $list = [];
    $res = $conn->query("SELECT id,type,title,region,marketing,money,area,start_date,end_date FROM `snail_post_boxshop` WHERE status = 1 AND end_date>$time AND start_date<$time AND title LIKE '%$keywords%';");
    while ($row = mysqli_fetch_assoc($res))
    {
      $row2['id']       = $row['id'];
      $row2['type']     = "BOXSHOP";  
      $row2['typename'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['money']    = $row['money'];
      $row2['tag1']     = $row['type'];
      $row2['tag2']     = "面积".$row['area'];
      $row2['start_date']     = $row['start_date'];
      $list[] = $row2;
    }
    return $list;
}

function getHouse($keywords){
    global $conn;
    $time = time();
    $list = [];
    $res = $conn->query("SELECT id,type,title,area,rent,start_date,end_date FROM `snail_post_house` WHERE status = 1 AND end_date>$time AND start_date<$time AND title LIKE '%$keywords%';");
    while ($row = mysqli_fetch_assoc($res))
    {
      $row2['id']       = $row['id'];
      $row2['type']     = "HOUSE_RENT";  
      $row2['typename'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['money']    = $row['rent'];
      $row2['tag1']     = $row['type'];
      $row2['tag2']     = "面积".$row['area'];
      $row2['start_date']     = $row['start_date'];
      $list[] = $row2;
    }
    return $list;
}

function getOccup($keywords){
    global $conn;
    $time = time();
    $list = [];
    $res = $conn->query("SELECT id,type,title,work_type,industry_type,salary,start_date,end_date FROM `snail_post_occup` WHERE status = 1 AND end_date>$time AND start_date<$time AND title LIKE '%$keywords%';");
    while ($row = mysqli_fetch_assoc($res))
    {
      $row2['id']       = $row['id'];
      $row2['type']     = "OCCUP";  
      $row2['typename'] = $row['type'];
      $row2['title']    = $row['title'];
      $row2['money']    = $row['salary'];
      $row2['tag1']     = $row['type'];
      $row2['tag2']     = $row['work_type'];
      $row2['tag3']     = $row['industry_type'];
      $row2['start_date']     = $row['start_date'];
      $list[] = $row2;
    }
    return $list;
}

function getPackage($keywords){
    global $conn;
    $time = time();
    $list = [];
    $res = $conn->query("SELECT id,type,company,company_info,company_city,start_date,end_date FROM `snail_post_package` WHERE status = 1 AND end_date>$time AND start_date<$time AND company LIKE '%$keywords%';");
    while ($row = mysqli_fetch_assoc($res))
    {
      $row2['id']       = $row['id'];
      $row2['type']     = "PACKAGE";  
      $row2['typename'] = $row['type'];
      $row2['title']    = $row['company'];
      $row2['money']    = '';
      $row2['tag1']     = $row['company_city'];
      $row2['start_date']     = $row['start_date'];
      $list[] = $row2;
    }
    return $list;
}