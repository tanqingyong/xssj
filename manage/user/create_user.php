<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
$action = '创建';
$current_menu = 'create_user';
if ( $_POST ){
	// unique username per user
	$str_username = strval($_POST['username']);
    if(strlen($str_username) < 4 || strlen($str_username) > 16){
        Session::Set('error', '用户名长度应为4-16个字符！');
        redirect( WEB_ROOT . "/manage/user/create_user.php");
        
    }
	$arr_eu = Table::Fetch('users', $str_username, 'username');
	if ($arr_eu && $arr_eu['id'] > 0 ) {
		Session::Set('notice', '用户名已经存在,请重设用户名！');
		redirect( WEB_ROOT . "/manage/user/create_user.php");
	}
	
	// validation password
	$str_password1 = strval($_POST['password1']);
	$str_password2 = strval($_POST['password2']);
	
	if(strlen(trim($str_password1))== 0){
		Session::Set('error', '密码不能为空');
		redirect( WEB_ROOT . "/manage/user/create_user.php");
	}
    if(strlen($str_password1)< 8){
        Session::Set('error', '密码最少设置为8个字符');
        redirect( WEB_ROOT . "/manage/user/create_user.php");
    }
    if(!preg_match('/\d/',$str_password1)||!preg_match('/[a-zA-Z]/',$str_password1)){
        Session::Set('error', '密码必须要含有一个数字和一个字母');
        redirect( WEB_ROOT . "/manage/user/create_user.php");
    }
	if($str_password1!=$str_password2 ){
		Session::Set('error', '密码不一致，请重设！');
		redirect( WEB_ROOT . "/manage/user/create_user.php");
	}
	// validation password end
	
	$grade = intval($_POST['grade']);
	
	//region and city
	$region = $_POST['region']?intval($_POST['region']):0;
	$city = $_POST['city']?intval($_POST['city']):0;
	
	$time = time();
	$arr_new_user = array("username" => $str_username,
						  "password" => Users::GenPassword($str_password1),
						  "create_by_id" => Session::Get('user_id'),
						  "grade" => $grade,
	                      "area_id" => $region,
	                      "city_id" => $city,
						  "create_time"	=> $time,
					      'update_time' => $time,
						  );
	if(DB::Insert("users",$arr_new_user)){
		redirect( WEB_ROOT . "/manage/user/manage.php");
	}else{
		Session::Set('notice', '用户创建失败');
		redirect( WEB_ROOT . "/manage/user/create_user.php");
	}
}

echo  template('manage_user_edit',array("action"=>$action,'current_menu'=>$current_menu));
?>
