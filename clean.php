<?php
/**
 * clean` api
 *
 */
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
require_once("./comm.php");
require_once("./conn_memcache.php");

if($_GET['type'] == 'all'){
	$mem->delete('neso2017_against');
    $mem->delete('neso2017_area_list');
    $mem->delete('neso2017_imglist');
    $mem->delete('neso2017_zone_intro');
    $mem->delete('neso2017_map');
    $mem->delete('neso2017_newslist_rec');
    $mem->delete('neso2017_schedule');
	$mem->delete('neso2017_videolist');
    
    $i=1;
    foreach($zone_array as $k=>$v){
        $zone_md5 = md5($v);
        
        $mem->delete('neso2017_flink_'.$zone_md5);
	    $mem->delete('neso2017_zone_'.$zone_md5);
        $mem->delete('neso2017_newslist_'.NEWS_ID.'_'.$i);
        $i++;
    }
    
}
