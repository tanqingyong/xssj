<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
$action = '创建';
if ( $_POST ){
	$name = strval($_POST['name']);
	if (empty($name)) {
		Session::Set('error', '大区名称不能为空,请重新设置大区名称！');
		redirect( WEB_ROOT . "/manage/region/region_create.php");
	}
	$region = Table::Fetch('region', $name, 'name');
	if ($region && $region['id'] > 0 ) {
		Session::Set('error', '大区名称已经存在,请重新设置大区名称！');
		redirect( WEB_ROOT . "/manage/region/region_create.php");
	}

	$condition = array("name" => $name);
	if(DB::Insert("region",$condition)){
		Session::Set('notice', '大区创建成功');
		redirect( WEB_ROOT . "/manage/region/region_list.php");
	}else{
		Session::Set('error', '大区创建失败');
		redirect( WEB_ROOT . "/manage/region/region_create.php");
	}
}

echo template('manage_region_edit',array('action'=>$action));
?>
