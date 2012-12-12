<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
$action = '编辑';
$id = abs(intval($_REQUEST['id']));
$region = Table::Fetch('region', $id);

if ( $_POST ) {
	$old_name = trim(strval($_POST['old_area']));
	$name = trim(strval($_POST['name']));
	if (empty($name)) {
		Session::Set('error', '大区名称不能为空,请重新设置大区名称！');
		redirect( WEB_ROOT . "/manage/region/region_create.php");
	}
	
	$region = Table::Fetch('region', $name, 'name');
	if ($region && $region['id'] != $_POST['id'] ) {
		Session::Set('error', '大区名称已经存在,修改失败');
		redirect( WEB_ROOT . "/manage/region/region_edit.php?id=$id");
	}
	$condition = array("name"=>$name);

	if ( DB::Update("region",$_POST['id'],$condition) ) {
		if( $old_name != $name ){
			$sql = "update six_indicators set region = '$name' where region = '$old_name'";
            DB::Query($sql);
		}
		Session::Set('notice', '修改大区信息成功');
		redirect( WEB_ROOT . "/manage/region/region_list.php");
	}else{
		Session::Set('error', '修改大区信息失败');
		redirect( WEB_ROOT . "/manage/region/region_edit.php?id=$id");
	}
	
}
echo  template('manage_region_edit',array("id"=>$id,"region"=>$region,'action'=>$action));