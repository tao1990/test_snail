<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$ac = empty($_GET['ac'])? '':$_GET['ac'];


/**
 * @SWG\Post(path="/app/other/file.php?ac=upload", tags={"other"},
 *   summary="图片上传(ok)",
 *   description="",
 *   @SWG\Parameter(name="img", type="file", required=true, in="formData",
 *     description="file" 
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
if($ac == 'upload'){
  
  $type     = $_FILES['img']['name'];//文件名
  //print_r($_FILES['img']['name']);die;
  $logFile = fopen("./img.log", "w");
  $txt = json_encode($_FILES)." -- ".date('Y-m-d H:i:s',time())."\n";
  fwrite($logFile, $txt);
  fclose($logFile); 
  if($type){
    $type     = explode('.',$type);
    $type     = $type[1];
    $filetype = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];
    if (! in_array($type, $filetype))
    {
        header('HTTP/1.1 500 ERROR');
      	echo json_encode ( array('status'=>400, 'msg'=>'不是图片类型') );exit();
    }
    $base_path = "../../upload/img/".date('Ymd',time())."/"; //存放目录
    if(!is_dir($base_path)){
      mkdir($base_path,0777,true);
    }
    $fileName = md5(basename ( $_FILES ['img'] ['name'] ).time()).".".$type;
    $target_path = $base_path . $fileName;
    
    if (move_uploaded_file ( $_FILES ['img'] ['tmp_name'], $target_path )) {
        $array = array (
        		"status" => 200,
        		"data" => "/upload/img/".date('Ymd',time())."/".$fileName
        );
        header('HTTP/1.1 200 OK');
        echo json_encode ( $array );exit();
    } else {
        $array = array (
        		"status" => 500,
        		"msg" => "There was an error uploading the file, please try again!" . $_FILES ['img'] ['error']
        );
        header('HTTP/1.1 500 ERROR');
        echo json_encode ( $array );exit();
    }
  }else{
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array("status"=> 400,"msg"=>"ERROR") );exit();
  }
  
}

/**
 * @SWG\Post(path="/app/other/file.php?ac=avatarUpload", tags={"other"},
 *   summary="头像上传(ok)",
 *   description="",
 *   @SWG\Parameter(name="uid", type="file", required=true, in="query",
 *     description="query" 
 *   ),
 *   @SWG\Parameter(name="img", type="file", required=true, in="formData",
 *     description="file" 
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
if($ac == 'avatarUpload'){
  $uid      = empty($_GET['uid'])? 0 : intval($_GET['uid']);
  $type     = $_FILES['img']['name'];//文件名
  $logFile = fopen("./avatarlog.log", "w");
  $txt = json_encode($_FILES)." -- ".date('Y-m-d H:i:s',time())."\n";
  fwrite($logFile, $txt);
  fclose($logFile); 
  //print_r($_FILES['img']['name']);die;
  if($type && $uid>0){
    $type     = explode('.',$type);
    $type     = $type[1];
    $filetype = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];
    if (! in_array($type, $filetype))
    {
        header('HTTP/1.1 500 ERROR');
      	echo json_encode ( array('status'=>400, 'msg'=>'不是图片类型') );exit();
    }
    $base_path = "../../upload/avatar/"; //存放目录
    if(!is_dir($base_path)){
      mkdir($base_path,0777,true);
    }
    $fileName = $uid.".".$type;
    $target_path = $base_path . $fileName;
    
    if (move_uploaded_file ( $_FILES ['img'] ['tmp_name'], $target_path )) {
        $array = array (
        		"status" => 200,
        		"data" => "/upload/avatar/".$fileName
        );
        header('HTTP/1.1 200 OK');
        echo json_encode ( $array );exit();
    } else {
        $array = array (
        		"status" => 500,
        		"msg" => "There was an error uploading the file, please try again!" . $_FILES ['img'] ['error']
        );
        header('HTTP/1.1 500 ERROR');
        echo json_encode ( $array );exit();
    }
  }else{
        header('HTTP/1.1 400 ERROR');
        echo json_encode ( array("status"=> 400,"msg"=>"ERROR") );exit();
  }
  
}
