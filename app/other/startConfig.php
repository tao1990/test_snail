<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/other/startConfig.php?ac=typelist", tags={"other"},
 *   summary="获取所有广告的type总类(OK)",
 *   description="",
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
if($ac == 'typelist'){
    
    $adList = getAdType();
    $list = getTypeList();
    
    $resArr = array(
        'openSwitch'=>false,
        'ad'=> $adList,
        'post'=>$list
    );
    if($list){
        header('HTTP/1.1 200 OK');
        echo json_encode ( array('status'=>200, 'data'=>$resArr) );exit();
    }
}







/****************************************************FUNC*************************************************************/

function getAdType(){
    global $conn;
    $list = array();
    $sql ="SELECT * from `snail_ad_type` ;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $list[] = $row;
    }
    //echo json_encode ( array('status'=>200, 'data'=>$tree) );exit();
    return $list;
}

function getTypeList(){
    global $conn;
    $list = array();
    $sql ="SELECT * from `snail_post_type` ;";
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        $list[] = $row;
    }
    $arr = array();

    $tree = getTree($list, 0);
    
    //echo json_encode ( array('status'=>200, 'data'=>$tree) );exit();
    return $tree;
}



function getTree($data, $pId)
{
    $tree = '';
    foreach($data as $k => $v)
    {
      if($v['parent_id'] == $pId)
      {
        if(getTree($data, $v['id'])) $v['son'] = getTree($data, $v['id']);
       
       $tree[] = $v;
       //unset($data[$k]);
      }
    }
    return $tree;
}
