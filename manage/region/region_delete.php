<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$id = $_GET['id'];
if (!$id) return false;

if(DB::Delete("region",array("id"=>$id))){
	Session::Set('notice', '大区删除成功');
}else{
	Session::Set('notice', '大区删除失败');
}

redirect(WEB_ROOT.'/manage/region/region_list.php');
?>