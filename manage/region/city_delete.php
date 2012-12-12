<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$id = $_GET['id'];
$area_id = intval($_GET['area_id']);
if(!is_null($area_id))
	$area_para = "?id=$area_id";

if (!$id) return false;

if(DB::Delete("city",array("id"=>$id))){
	Session::Set('notice', '城市删除成功');
}else{
	Session::Set('error', '城市删除失败');
}

redirect(WEB_ROOT.'/manage/region/city_list.php'.$area_para);
?>