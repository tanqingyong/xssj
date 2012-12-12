<?php
//  2011-8-11  pm 02:40:23  writen by sun-zhenchao
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$user_id = $_GET['user_id'];
if (!$user_id) return false;

if(DB::Delete("users",array("id"=>$user_id))){
	Session::Set('notice', '用户删除成功');
}else{
	Session::Set('notice', '用户删除失败');
}

redirect(WEB_ROOT.'/manage/user/manage.php');
?>