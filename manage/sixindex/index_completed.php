<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select s.*,sum(b.positive_profile) as actual_pos,sum(b.profile) as actual_pro, sum(b.lose_profile) as actual_lose,sum(b.suc_total_price) as actual_price,  
		sum(b.profile)/sum(b.suc_total_price) as actual_rate
        from six_indicators s 
        left join city on city.name = s.city
        left join buss_city b on s.city = b.city ";
$date_from = $_GET['date_from'];
$date_end = $_GET['date_end'];
if($date_from&&$date_end){
	if(date('m',strtotime($date_from))!=date('m',strtotime($date_end))||date('Y',strtotime($date_from))!=date('Y',strtotime($date_end))){
		Session::Set('error', '时间段不能跨月！');
	    redirect( WEB_ROOT . "/manage/sixindex/index_completed.php");
	}
	if( $date_from > $date_end ){
		Session::Set('error', '开始时间不能比结束时间大！');
	    redirect( WEB_ROOT . "/manage/sixindex/index_completed.php");
	}
	$month = date("Ym",strtotime($date_from));
	if($date_from==$date_end){
		$on_condition .= " and b.data_date = '$date_from'";
	}else{
	    $on_condition .= " and b.data_date between '$date_from' and '$date_end' ";
	}
}else if( $date_from && !$date_end ){
	$month = date("Ym",strtotime($date_from));
	$on_condition .= " and b.data_date = '$date_from'";
}else if( !$date_from && $date_end ){
	$month = date("Ym",strtotime($date_end));
	$on_condition .= " and b.data_date = '$date_end' ";
}else{
	$date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $month = date("Ym",time());
    $on_condition .= " and b.data_date between '$date_from' and '$date_end' ";
}
$sql .= $on_condition;
$condition = " where 1=1 ";
$condition .= " and s.indicator_date = {$month} ";
   

//权限处理
if($_SESSION['user_grade']>2){
	$filter_region = intval($_GET['region']);
	$filter_city = intval($_GET['city']);
	if($filter_city){
	    $condition .= " and city.id = $filter_city ";
	}else if($filter_region){
	    $cities = array();
	    $cities = get_cities_by_region_id($filter_region);
	    $cities = array_flip($cities);
	    $city_str = '('.implode(',',$cities).')';
	    $condition .= " and city.id in $city_str ";
	}
}else if($_SESSION['user_grade']==2){
	$filter_city = intval($_GET['city']);
	if($filter_city){
	    $condition .= " and city.id = $filter_city ";
	}else{
	    $cities = array();
	    $cities = get_cities_by_region_id($_SESSION['area_id']);
	    $cities = array_flip($cities);
	    $city_str = '('.implode(',',$cities).')';
	    $condition .= " and city.id in $city_str ";
	}
}else{
    $condition .= " and city.id = {$_SESSION['city_id']} ";
}
$condition .= " group by b.city ";
$sql .= $condition;
$datas = DB::GetQueryResult($sql,false);
echo template ( 'manage_index_completed',array('datas' => $datas, 'date_from'=>$date_from, 'date_end'=>$date_end, 'filter_region'=>$filter_region, 'filter_city'=>$filter_city)); 
?>