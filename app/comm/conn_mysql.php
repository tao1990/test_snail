<?php

//数据库连接信息
$cfg_dbtype = 'mysql';
$cfg_dbhost = '10.10.20.43';
//$cfg_dbname = 'dedecmsv57utf8sp2';
$cfg_dbname = 'snail';
$cfg_dbuser = 'root';
$cfg_dbpwd = 'root';
$cfg_dbprefix = 'snail_';
$cfg_db_language = 'utf8';

$conn=new mysqli($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd,$cfg_dbname,3306);

if(!$conn){
    header('HTTP/1.1 500 DBERROR');
    exit('{}');
}


function snail_insert($table,$data){
    global $conn;
     //遍历数组，得到每一个字段和字段的值
     $key_str='';
     $v_str='';
     foreach($data as $key=>$v){
     
        //$key的值是每一个字段s一个字段所对应的值
        $key_str.=$key.',';
        $v_str.="'$v',";
     }
     $key_str=trim($key_str,',');
     $v_str=trim($v_str,',');
     //判断数据是否为空
     $sql="insert into $table ($key_str) values ($v_str)";
     $conn->query($sql);
    //返回上一次增加操做产生ID值
     return $conn->insert_id;
}

//$up=$db->update("users",$_POST,"id=27");
function snail_update($table,$data,$where){
    global $conn;
     //遍历数组，得到每一个字段和字段的值
     $str='';
    foreach($data as $key=>$v){
     $str.="$key='$v',";
    }
    $str=rtrim($str,',');
    //修改SQL语句
    $sql="update $table set $str where $where";
    return $conn->query($sql);
   }
?>
