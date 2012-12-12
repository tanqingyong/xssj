<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
//权限处理
if($_SESSION['user_grade']>2){
	redirect( WEB_ROOT . '/manage/moneydetail/nationwide_day.php');
}else if($_SESSION['user_grade']==2){
	redirect( WEB_ROOT . '/manage/moneydetail/region_day.php');
}else{
    redirect( WEB_ROOT . '/manage/moneydetail/city_day.php');
}

?>