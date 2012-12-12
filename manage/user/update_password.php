<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_login();

$int_user_id = Session::Get('user_id');
$arr_user = Table::FetchForce("users",$int_user_id);

if($_POST){
	// validation password
	$str_password1 = strval($_POST['password1']);
	$str_password2 = strval($_POST['password2']);
	if(strlen(trim($str_password1))==0){
		Session::Set('notice', '密码不能为空');
		redirect( WEB_ROOT . "/manage/user/update_password.php");
	}
    if(strlen($str_password1)< 8){
        Session::Set('error', '密码最少设置为8个字符');
        redirect( WEB_ROOT . "/manage/user/update_password.php");
    }
    if(!preg_match('/\d/',$str_password1)||!preg_match('/[a-zA-Z]/',$str_password1)){
        Session::Set('error', '密码必须要含有一个数字和一个字母');
        redirect( WEB_ROOT . "/manage/user/update_password.php");
    }
    if($str_password1!=$str_password2 ){
        Session::Set('error', '密码不一致，请重设！');
        redirect( WEB_ROOT . "/manage/user/update_password.php");
    }
	
	$arr_update = array("password" => Users::GenPassword($str_password1),'update_time' => time());
	
	if(DB::Update("users",$int_user_id,$arr_update)){
		Session::Set('notice', '重设密码成功！');
	}else{
		Session::Set('notice', '更新密码失败，请重试！');
	}
	redirect( WEB_ROOT . "/manage/user/update_password.php");
}

echo template('manage_user_update_password',array('user'=>$arr_user));
?>