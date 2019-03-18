<?php

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$ac = empty($_GET['ac'])? '':addslashes($_GET['ac']);
//$m = empty($_GET['m'])? '':addslashes($_GET['m']);


/**
 * @SWG\Get(path="/app/other/bonus.php?ac=userBonusList", tags={"other"},
 *   summary="获取用户所拥有的优惠券列表",
 *   @SWG\Parameter(name="uid", type="integer", required=true, in="query",example = ""),
 *   @SWG\Parameter(name="token", type="string", required=true, in="query",example = ""),
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
if($ac == 'userBonusList'){
    
    $uid    = empty($_GET['uid'])? 0 : intval($_GET['uid']);
    $token  = empty($_GET['token'])? 0 : $_GET['token'];
    
    
    if($uid > 0 && tokenVerify($token,$uid)){
        $list = array();
        $list = getUserBonusInfo($uid);
        header('HTTP/1.1 200 ok');
        echo json_encode ( array('status'=>200, 'data'=>$list) );exit();
    }else{
        header('HTTP/1.1 400 参数错误');
        echo json_encode ( array('status'=>400, 'msg'=>'参数错误') );exit();
    }

}



/****************************************************FUNC*************************************************************/

//获取用户红包信息
function getUserBonusInfo($uid){
  global $conn;
  $list = array();
  $result = $conn->query("SELECT * from `snail_user_bonus` A LEFT JOIN `snail_bonus_type` B  ON A.bonus_type_id = B.type_id WHERE A.uid = $uid AND used_time = 0;");
  while ($row = mysqli_fetch_assoc($result))
  {
      if($row['expiry_time'] != 0){
        $row['overdue'] =  $row['get_time'] + (86400*$row['use_term']) > $row['expiry_time'] ? 1:0;  
      }else{
        $row['overdue'] = 0;
      }
      $list[] = $row;
  }
  return $list;
}
