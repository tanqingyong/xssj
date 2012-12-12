<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
$action = '编辑';
$area_id = intval($_GET['area_id']);
if(!is_null($area_id))
	$area_para = "?id=$area_id";
	
$id = abs(intval($_REQUEST['id']));
$city = Table::Fetch('city', $id);

if ( $_POST ) {
	$name = trim(strval($_POST['name']));
	$area_id = trim(strval($_POST['area_id']));
	$old_area = trim(strval($_POST['old_area']));
	$old_city = trim(strval($_POST['old_city']));
	if (empty($name)) {
		Session::Set('error', '城市名称不能为空,请重新设置城市名称！');
		redirect( WEB_ROOT . "/manage/region/city_create.php");
	}
	$city = Table::Fetch('city', $name, 'name');
	if ($city && $city['id'] != $_POST['id'] ) {
		Session::Set('error', '城市名称已经存在,修改失败');
		redirect( WEB_ROOT . "/manage/region/city_edit.php?id=$id");
	}
	$condition = array("name"=>$name,"area_id"=>$area_id);

	if ( DB::Update("city",$_POST['id'],$condition) ) {
		if( $old_area != $area_id ){
			$area_name = get_region_name_by_id($area_id);
			$old_name = get_region_name_by_id($old_area);
		  	$sql = "update six_indicators set region = '$area_name' where region = '$old_name' and city = '$old_city' ";
		  	DB::Query($sql);
		}
		if( $old_city != $name ){
		    $sql = "update six_indicators set city = '$name' where city = '$old_city'";
            DB::Query($sql);
		}
		Session::Set('notice', '修改城市信息成功');
		redirect( WEB_ROOT . "/manage/region/city_list.php$area_para");
	}else{
		Session::Set('error', '修改城市信息失败');
		redirect( WEB_ROOT . "/manage/region/city_edit.php?id=$id");
	}
	
}
echo  template('manage_city_edit',array("id"=>$id,"city"=>$city,'action'=>$action));