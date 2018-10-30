<?php
/**
 * map api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("../comm/comm.php");
require_once("../comm/conn_mysql.php");

$action = empty($_GET['action'])? '':$_GET['action'];

if($action == 'upload'){

  $type     = $_FILES['img1']['name'];//文件名
  $type     = explode('.',$type);
  $type     = $type[1];
  $filetype = ['jpg', 'jpeg', 'gif', 'bmp', 'png'];
  if (! in_array($type, $filetype))
  {
        header('HTTP/1.1 500 ERROR');
      	echo json_encode ( array('status'=>400, 'msg'=>'不是图片类型') );exit();
  }
  $base_path = "../upload/".date('Ymd',time())."/"; //存放目录
  if(!is_dir($base_path)){
      mkdir($base_path,0777,true);
  }
  $target_path = $base_path . basename ( $_FILES ['img1'] ['name'] );
  if (move_uploaded_file ( $_FILES ['img1'] ['tmp_name'], $target_path )) {
  	$array = array (
  			"status" => true,
  			"msg" => $_FILES ['img1'] ['name']
  	);
    header('HTTP/1.1 200 OK');
  	echo json_encode ( $array );exit();
  } else {
  	$array = array (
  			"status" => false,
  			"msg" => "There was an error uploading the file, please try again!" . $_FILES ['img1'] ['error']
  	);
    header('HTTP/1.1 500 ERROR');
  	echo json_encode ( $array );exit();
  }

}
