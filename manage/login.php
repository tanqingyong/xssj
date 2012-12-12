<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

if ( $_POST ) {
	if(strtolower($_POST['captcha'])==$_SESSION['randcode']){
		$login_admin = Users::GetLogin($_POST['username'], $_POST['password']);
		
		if($login_admin){
			$cha = round((time()-$login_admin['create_time'])/24/3600);
			$state=$login_admin['state'];
			$yue=60-$cha;
			
			if($cha<60 && $state==1){
			Session::Set('user_grade', $login_admin['grade']);
			Session::Set('user_id', $login_admin['id']);
			Session::Set('user_name', $login_admin['username']);
			Session::Set('grade', $login_admin['grade']);
			Session::Set('area_id', $login_admin['area_id']);
			Session::Set('city_id', $login_admin['city_id']);
			Session::Set('session_time', time());
			Session::Set('surplus_time', $yue);
	//		error_log("-------------++++++++++++-----".var_export(Session,true));
	//		error_log("-------------++++++++++++-----".var_export($login_admin,true));
			DB::Update('users',$login_admin['id'],array('login_time'=>time(),'online'=>1 ));
			redirect( WEB_ROOT . '/manage/dataanalysis/summary.php');
			}else{
					Session::Set ( 'user_id', $login_admin ['id'] );
					redirect ( WEB_ROOT .'/manage/user/guoqi.php');					
				}
		}else{
			Session::Set('error', '用户名密码不匹配！');
		}
	}else{
		Session::Set('error', '验证码输入错误！');
	}
}

if(Session::Get('user_id')&&Session::Get('user_name')){
	redirect( WEB_ROOT . '/manage/dataanalysis/summary.php');
}

echo template('manage_login');

?>
