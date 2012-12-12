<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$city = $_GET['city'];
$city_name = urldecode($city);
$indicator_date = $_GET['indicator_date'];
if($_SESSION['grade'] != 3){
    if($indicator_date != date('Y').date('m')+1 && $indicator_date){
        Session::Set('notice', '现在不能修改数据');
        redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
    }
}
$action = "edit";
if(date("d")<25 && $_SESSION['grade'] != 3){
	Session::Set('notice', '现在不能修改数据');
	redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
}
if ( $_POST ) {
	$city = trim($_POST['city']);
	$pre_sale_amount = trim($_POST['pre_sale_amount']);
	$pre_positive_profile = trim($_POST['pre_positive_profile']);
	$pre_lose_profile = trim($_POST['pre_lose_profile']);
	$pre_advance_payment = trim($_POST['pre_advance_payment']);
	if( !is_numeric($pre_sale_amount) || floatval($pre_sale_amount)<=0){
		Session::Set('notice', '请输入正确的预测销售额！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_edit.php?city={$city}&indicator_date={$indicator_date}");
	}
	if( !is_numeric($pre_positive_profile) || floatval($pre_positive_profile)<0){
		Session::Set('notice', '请输入正确的预测正毛利！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_edit.php?city={$city}&indicator_date={$indicator_date}");
	}
	if( !is_numeric($pre_lose_profile) || floatval($pre_lose_profile)<0){
		Session::Set('notice', '请输入正确的预测负毛利！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_edit.php?city={$city}&indicator_date={$indicator_date}");
	}
	if( !is_numeric($pre_advance_payment) || floatval($pre_advance_payment)<0){
		Session::Set('notice', '请输入正确的预测预付款！');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_edit.php?city={$city}&indicator_date={$indicator_date}");
	}
	$arr_update = array( "pre_sale_amount" => $pre_sale_amount,
						 "pre_positive_profile" => $pre_positive_profile,
						 "pre_lose_profile" => $pre_lose_profile,
						 "pre_profile" => $pre_positive_profile-$pre_lose_profile,
						 "pre_profile_rate" => round((($pre_positive_profile-$pre_lose_profile)/$pre_sale_amount)*10000)/100,
						 "pre_advance_payment" => $pre_advance_payment);
	if($_SESSION["grade"]==2){
	    $arr_update["is_modified_by_regioner"] = 1;
	}
	$condition = array( "city" => $city,
						"indicator_date" => $indicator_date);
	if ( DB::Update("six_indicators",$condition,$arr_update) ) {
		Session::Set('notice', '修改预测信息成功');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
	}else{
		Session::Set('notice', '修改预测信息失败');
		redirect( WEB_ROOT . "/manage/sixindex/index_forecast_edit.php?city={$city}&indicator_date={$indicator_date}");
	}
}else{
	if( $city_name && $indicator_date){
		if( $_SESSION['grade'] == 4 ){
		    Session::Set('notice', '您没有权限修改该城市的数据!');
            redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
		}
		if( $_SESSION['grade'] == 1 ){
		    $city_belong = get_city_name_by_id($_SESSION['city_id']);
            if($city_name!=$city_belong){
            	Session::Set('notice', '您没有权限修改该城市的数据!');
                redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
            }
		}
	    if( $_SESSION['grade'] == 2 ){
            $cities = get_cities_by_region_id($_SESSION['area_id']);
            if(!in_array($city_name,$cities)){
                Session::Set('notice', '您没有权限修改该城市的数据!');
                redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
            }
        }
		$sql = "select s.* from six_indicators s, city where city.name = s.city and city.name = '{$city_name}' and s.indicator_date = '{$indicator_date}'";
		$datas = DB::GetQueryResult($sql,true);
		if($datas["is_modified_by_regioner"]&&$_SESSION['grade']!=3){
		    Session::Set('notice', '数据处于锁定状态，不能修改!');
            redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
		}
		echo template('manage_index_forecast_edit',array('datas'=>$datas, 'action' => $action));
	}else{
		Session::Set('notice', '非法的月份和城市!');
        redirect( WEB_ROOT . "/manage/sixindex/index_forecast.php");
	}
}