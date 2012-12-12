<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
$action = '创建';
if ( $_POST ){
	$name = strval($_POST['name']);
	$area_id = trim(strval($_POST['area_id']));
	
	if (empty($name)) {
		Session::Set('error', '城市名称不能为空,请重新设置城市名称！');
		redirect( WEB_ROOT . "/manage/region/city_create.php");
	}
	$city = Table::Fetch('city', $name, 'name');
	if ($city && $city['id'] > 0 ) {
		Session::Set('error', '城市名称已经存在,请重新设置城市名称！');
		redirect( WEB_ROOT . "/manage/region/city_create.php");
	}

	$condition = array("name" => $name,"area_id"=>$area_id);
	if(DB::Insert("city",$condition)){
		Session::Set('notice', '城市创建成功');
		redirect( WEB_ROOT . "/manage/region/city_list.php");
	}else{
		Session::Set('error', '城市创建失败');
		redirect( WEB_ROOT . "/manage/region/city_create.php");
	}
}

echo template('manage_city_edit',array('action'=>$action));
?>
