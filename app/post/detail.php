<?php

//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/post/detail.php", tags={"post"},
 *   summary="获取详情(ok)",
 *   description="",
 * @SWG\Parameter(name="typeCode", type="string", required=true, in="query",example = "OCCUP|ADWALL|BOXSHOP|PACKAGE|HOUSE_RENT"),
 * @SWG\Parameter(name="id", type="integer", required=true, in="query",example = ""),
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
//$a =tokenCreate(15);
//print_r($a);die;
//$bodyData = @file_get_contents('php://input');
//$bodyData = json_decode($bodyData,true);
$type = empty($_GET['typeCode'])? "":$_GET['typeCode'];
$id = empty($_GET['id'])? 0:$_GET['id'];
if($id>0 && in_array($type,array('OCCUP','ADWALL','PACKAGE','BOXSHOP','HOUSE_RENT'))){
    $list = getDetail($type,$id);
    header('HTTP/1.1 200 OK');
    echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
}else{
    header('HTTP/1.1 403 error');
    echo json_encode ( array('status'=>403, 'msg'=>'error') );exit();
}


/****************************************************FUNC*************************************************************/

function getDetail($type,$id){
    global $conn;
    $info = [];
    if($type == 'OCCUP'){
        $sql ="SELECT * from `snail_post_occup` WHERE id = $id limit 1;";
    }elseif($type == 'ADWALL'){
        $sql ="SELECT * from `snail_post_adwall` WHERE id = $id limit 1;";
    }elseif($type == 'PACKAGE'){
        $sql ="SELECT * from `snail_post_package` WHERE id = $id limit 1;";
    }elseif($type == 'BOXSHOP'){
        $sql ="SELECT * from `snail_post_boxshop` WHERE id = $id limit 1;";
    }elseif($type == 'HOUSE_RENT'){
        $sql ="SELECT * from `snail_post_house` WHERE id = $id limit 1;";
    }
    $result=$conn->query($sql);
    while ($row = mysqli_fetch_assoc($result))
    {
        
        $info[] = $row;
    }
   
    return $info;
}


