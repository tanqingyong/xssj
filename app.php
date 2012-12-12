<?php
require_once(dirname(__FILE__). '/include/application.php');
/* magic_quota_gpc */
$_GET = magic_gpc($_GET);
$_POST = magic_gpc($_POST);
$_COOKIE = magic_gpc($_COOKIE);

/* process currefer*/
$currefer = uencode(strval($_SERVER['REQUEST_URI']));

/* session,cache,configure,webroot register */
Session::Init();
$INI = ZSystem::GetINI();
/* end */

/* date_zone */
if(function_exists('date_default_timezone_set')) { 
	date_default_timezone_set($INI['system']['timezone']); 
}
/* end date_zone */


/* biz logic */
$currency = $INI['system']['currency'];

/**
 * 记录每次对的主体分析模块的二级菜单的访问情况
 * 获取主体分析模块的功能getAllURI()
 * 获取当前URI的访问路径 get_url()
 * 判断当前访问URI是否为所访问的主体分析模块的功能
 * 如果是，则保存此次访问如下信息：user_id，访问ip，菜单id，访问时间。
 */
$current_menu_url = get_url();
$all_menu_url = get_all_menu_url();
if(array_key_exists($current_menu_url,$all_menu_url)){
	$insert_Id = DB::Insert('menu_log',array('user_id'=>$_SESSION['user_id'],'ip'=>Utility::GetRemoteIp(),'menu_id'=>$all_menu_url[$current_menu_url],'action_time'=>time()));
}

/* not allow access app.php */
if($_SERVER['SCRIPT_FILENAME']==__FILE__){
	redirect( WEB_ROOT . '/index.php');
}
/* end */
$AJAX = ('XMLHttpRequest' == @$_SERVER['HTTP_X_REQUESTED_WITH']);
if (false==$AJAX) { 
	header('Content-Type: text/html; charset=UTF-8'); 
	run_cron();
} else {
	header("Cache-Control: no-store, no-cache, must-revalidate");
}
