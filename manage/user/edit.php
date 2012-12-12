<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$id = abs(intval($_GET['user_id']));
$arr_edited_user = Table::Fetch('users', $id);
$action = '编辑';
$current_menu = 'manage';

if ( $_POST ) {
	
	// unique username per user
	$username = trim(strval($_POST['username']));
	if(strlen($username) < 4 || strlen($username) > 16){
		Session::Set('error', '用户名长度应为4-16个字符！');
		redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id");
		
	}
	
	$eu = Table::Fetch('users', $username, 'username');
	if ($eu && $eu['id'] != $_POST['id'] ) {
		Session::Set('notice', '用户名已经存在,不能修改！');
		redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id");
	}
	// end
	$arr_update = array("username"=>$username);
	
	// validation password
	$str_password1 = trim(strval($_POST['password1']));
	$str_password2 = trim(strval($_POST['password2']));
	
	if(strlen($str_password1)> 0){
		if(strlen($str_password1)< 8){
			Session::Set('error', '密码最少设置为8个字符');
			redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id");
		}
	    if(!preg_match('/\d/',$str_password1)||!preg_match('/[a-zA-Z]/',$str_password1)){
	    	Session::Set('error', '密码必须要含有一个数字和一个字母');
            redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id");
	    }
		if($str_password1!=$str_password2 ){
			Session::Set('error', '密码不一致，请重设！');
			redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id");
		}
		$arr_update['password'] = Users::GenPassword($str_password1);
	}
	// validation password end
	
	
	$arr_update['grade'] =  intval($_POST['grade']);

	$arr_update['area_id'] =  $_POST['region']?intval($_POST['region']):0;
	$arr_update['city_id'] =  $_POST['city']?intval($_POST['city']):0;
	
	$arr_update['update_time'] = time();
	//exit;
	if ( DB::Update("users",$_POST['id'],$arr_update) ) {
		Session::Set('notice', '修改用户信息成功');
		redirect( WEB_ROOT . "/manage/user/manage.php");
	}else{
		Session::Set('error', '修改用户信息失败');
		redirect( WEB_ROOT . "/manage/user/edit.php?user_id=$id?id={$id}");
	}
	
}

echo  template('manage_user_edit',array("id"=>$id,"arr_edited_user"=>$arr_edited_user,"action"=>$action,'current_menu'=>$current_menu));
?>
