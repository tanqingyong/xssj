<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$indicator_date = date('Y').date('m')+1;
$action = 'add';
if( $_SESSION['grade']!=1 ){
	Session::Set('notice', '您没有权限添加数据');
    redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
}
if(date("d")<25){
	Session::Set('notice', '现在不能添加数据');
	redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
}
if ( $_POST ) {
	$city = trim($_POST['city']);
	$region = trim($_POST['region']);
	$pre_sale_amount = trim($_POST['pre_sale_amount']);
	$pre_positive_profile = trim($_POST['pre_positive_profile']);
	$pre_lose_profile = trim($_POST['pre_lose_profile']);
	$pre_profile = trim($_POST['pre_profile']);
	$pre_profile_rate = trim($_POST['pre_profile_rate']);
	$pre_advance_payment = trim($_POST['pre_advance_payment']);
	if( !is_numeric($pre_sale_amount) || intval($pre_sale_amount)<=0){
		Session::Set('notice', '请输入正确的预测销售额！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_add.php");
	}
	if( !is_numeric($pre_positive_profile) || intval($pre_positive_profile)<0){
		Session::Set('notice', '请输入正确的预测正毛利！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_add.php");
	}
	if( !is_numeric($pre_lose_profile) || intval($pre_lose_profile)<0){
		Session::Set('notice', '请输入正确的预测负毛利！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_add.php");
	}
	if( !is_numeric($pre_advance_payment) || intval($pre_advance_payment)<0){
		Session::Set('notice', '请输入正确的预测预付款！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_add.php");
	}
  
	$arr_new_data = array( "indicator_date" => $indicator_date, 
						   "city" => $city,
						   "region" => $region,
						   "pre_sale_amount" => $pre_sale_amount,
						   "pre_positive_profile" => $pre_positive_profile,
						   "pre_lose_profile" => $pre_lose_profile,
						   "pre_profile" => $pre_profile,
						   "pre_profile_rate" => round((($pre_positive_profile-$pre_lose_profile)/$pre_sale_amount)*10000)/100,
						   "pre_advance_payment" => $pre_advance_payment
						 );
	if(DB::Insert("six_indicators",$arr_new_data)){
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
    }else{
	 	Session::Set('notice', '数据添加失败');
	 	$params = "?";
	 	foreach( $arr_new_data as $key=>$data ){
	 	    $params.="$key=$data&";
	 	}
	 	$params = substr($params, 0, strlen($params)-1);
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_add.php{$params}");
	}
}else if($_GET){
	$indicator_date = trim($_GET['indicator_date']);
	$city = trim($_GET['city']);
    $region = trim($_GET['region']);
    $pre_sale_amount = trim($_GET['pre_sale_amount']);
    $pre_positive_profile = trim($_GET['pre_positive_profile']);
    $pre_lose_profile = trim($_GET['pre_lose_profile']);
    $pre_profile = trim($_GET['pre_profile']);
    $pre_profile_rate = trim($_GET['pre_profile_rate']);
    $pre_advance_payment = trim($_GET['pre_advance_payment']);
    if(!$indicator_date || !$city || !$region || $pre_sale_amount<=0 || $pre_positive_profile<0 || $pre_lose_profile<0 || $pre_advance_payment < 0){
    	Session::Set('notice', '数据信息错误');
    	redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
    }
    $sql = "select count(1) as count from six_indicators where indicator_date = '{$indicator_date}' and city = '{$city}'";
    $results = DB::GetQueryResult($sql, true);
    if($results['count']){
        Session::Set('notice', '数据已存在请选择编辑');
        redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php"); 
    }
    $datas = array( "indicator_date" => $indicator_date, 
                    "city" => $city,
                    "region" => $region,
                    "pre_sale_amount" => $pre_sale_amount,
                    "pre_positive_profile" => $pre_positive_profile,
                    "pre_lose_profile" => $pre_lose_profile,
                    "pre_profile" => $pre_profile,
                    "pre_profile_rate" => round((($pre_positive_profile-$pre_lose_profile)/$pre_sale_amount)*10000)/100,
                    "pre_advance_payment" => $pre_advance_payment
                   );
    echo template( 'manage_index_forecast_edit', array('datas' => $datas, 'action' => $action));
}else{
	$city = get_city_name_by_id($_SESSION['city_id']);
	$region = get_region_name_by_id($_SESSION['area_id']);
    $sql = "select count(1) as count from six_indicators where indicator_date = '{$indicator_date}' and city = '{$city}'";
    $results = DB::GetQueryResult($sql, true);
    if($results['count']){
        Session::Set('notice', '数据已存在请选择编辑');
        redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php"); 
    }
    $datas = array('city' => $city, 'region' => $region, 'indicator_date' => $indicator_date);
	echo template( 'manage_index_forecast_edit', array('datas' => $datas, 'action' => $action)); 
}