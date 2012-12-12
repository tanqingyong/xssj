<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

DB::Update ( 'users', $_SESSION['user_id'], array ('online'=>0) ); //注销账户，修改用户的在线状态为离线
unset($_SESSION['user_grade']);
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_type']);
unset($_SESSION['area_id']);
unset($_SESSION['city_id']);
unset($_SESSION['session_time']);		
redirect( WEB_ROOT . '/manage/login.php');
?>