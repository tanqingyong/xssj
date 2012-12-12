<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select buss_city.*,region.name as regionname from buss_city 
left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1 ";
$sql_count = "select count(1) as count from buss_city 
left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1 ";
$sql_sum = "SELECT sum(buss_city.suc_total_price) AS regionprice, region.name
FROM buss_city
left join city ON buss_city.city = city.name
left join region ON region.id = city.area_id
WHERE 1 =1 ";

$condition = "";
$filter_date_start = trim($_GET['stratdate']);
$filter_date_end = trim($_GET['enddate']);
if($filter_date_start && $filter_date_end){
    $condition .= " and buss_city.data_date between '$filter_date_start' and '$filter_date_end' ";
}elseif($filter_date_start){
   $condition .= " and buss_city.data_date = '$filter_date_start' ";
}elseif($filter_date_end){
   $condition .= " and buss_city.data_date = '$filter_date_end' ";
}else{
	$filter_date_start = date('Y-m-01',time());
	$filter_date_end = date('Y-m-t',time());
	$condition .= " and buss_city.data_date between '$filter_date_start' and '$filter_date_end' ";
}
//权限处理
if($_SESSION['user_grade']>2){
	$filter_region = intval($_GET['region']);
	$filter_city = intval($_GET['city']);
	if($filter_city){
	    $condition .= "and city.id = $filter_city ";
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
	    $condition .= "and city.id = $filter_city ";
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
$sql .= $condition;
$sql_count .= $condition;
$sql_sum .= $condition." group by region.id";
$sum_array = DB::GetQueryResult($sql_sum,false);
$sum_string = "";
$sum_money=0;
foreach($sum_array as $sum){
	$money = round($sum['regionprice']*10)/10;
	$sum_string .= ",{$sum['name']}<span>$money</span>元"; 
	$sum_money += $sum['regionprice'];
}
$sum_money = round($sum_money*10)/10;
$sum_string = "销售总金额<span>$sum_money</span>元".$sum_string.'.'; 
$result = DB::GetQueryResult($sql_count,true);
$count = $result['count'];
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);

$sql .="order by data_date desc limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);
if(Session::Get('surplus_time')<=10 && Session::Get('surplus_time')>0){
Session::Set('notice', "你账号还有".Session::Get('surplus_time')."天将被系统自动停用,为了避免到时登录不了系统，建议你现在就去OA填写<font color=\"red\">数据系统权限审批单</font>提交申请");	
}
	Session::Set('surplus_time','');
echo template ( 'manage_dataanalysis_summary', 
                array ( 'datas'=>$datas,'pagestring'=>$pagestring,'filter_date_start'=>$filter_date_start,'filter_date_end'=>$filter_date_end,'filter_region'=>$filter_region,'filter_city'=>$filter_city,'page_size'=>$page_size,'sum_string'=>$sum_string) );

?>