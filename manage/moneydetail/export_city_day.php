<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$table_array =  array(  'data_date' => '日期',
						'region' => '大区',
						'city' => '城市',
                        'goods_day' => '销量',
                        'sales_day' => '销售额',
                        'profile_day' => '毛利',
                        'profile_rate' => '毛利率',
                        'goods_sum' => '累计销量',
                        'sales_sum'=>'累计销售额',
                        'profile_sum' =>'累计毛利',
						'verify_num'=>'验证量',
						'verify_sum'=>'验证额',
						'verify_profile'=>'验证毛利',
						'verify_num_sum'=>'累计验证量',
						'verify_sum_sum'=>'累计验证额',
						'verify_profile_sum'=>'累计验证毛利'
                      );
$sql = "select region.name as region,data_date,city,
round(sum(suc_total_price),2) as sales_day,round(sum(suc_goods_num),2) as goods_day,
round(sum(profile),2) as profile_day, round(sum(profile)*100/sum(suc_total_price),2) as profile_rate,
verify_num,verify_sum,verify_profile 
from buss_city left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1  ";
$sql_count = "select count(1) as count from (select data_date from buss_city 
left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1 ";

$date_from = trim($_GET['date_from']);
$date_end = trim($_GET['date_end']);
$condition = "";

if($date_from&&$date_end){
    if(date('m',strtotime($date_from))!=date('m',strtotime($date_end))||date('Y',strtotime($date_from))!=date('Y',strtotime($date_end))){
        Session::Set('notice', '时间段不能跨月！');
        redirect( WEB_ROOT . "/manage/moneydetail/nationwide_day.php");
    }
    if( $date_from > $date_end ){
        Session::Set('notice', '开始时间不能比结束时间大！');
        redirect( WEB_ROOT . "/manage/moneydetail/nationwide_day_noun.php");
    }
    if($date_from==$date_end){
        $condition .= " and data_date = '$date_from'";
    }else{
        $condition .= " and data_date between '$date_from' and '$date_end' ";
    }
}else if( $date_from && !$date_end ){
    $date_end = $date_from;
    $condition .= " and data_date = '$date_from'";
}else if( !$date_from && $date_end ){
    $date_from = $date_end;
    $condition .= " and data_date = '$date_end'";
}else{
    $date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $condition .= " and data_date between '$date_from' and '$date_end' ";
}

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
$date_start = date('Y-m-01',strtotime($date_from));
$total_datas = DB::GetQueryResult($sql." and data_date between '$date_start' and '$date_end' group by data_date,city order by data_date",false);
$sql .= $condition." group by data_date,city order by data_date";

$sum_datas = array();
foreach( $total_datas as $key => $value ){
	$goods_sum[$value['city']] += $value['goods_day'];
    $sales_sum[$value['city']] += $value['sales_day'];
    $profile_sum[$value['city']] += $value['profile_day'];
    $verify_num_sum[$value['city']] += $value['verify_num'];//累计验证量
	$verify_sum_sum[$value['city']] += $value['verify_sum'];//累计验证额
	$verify_profile_sum[$value['city']] += $value['verify_profile'];//累计验证毛利
	
    $sum_datas[$value['data_date'].$value['city']."goods_sum"] += $goods_sum[$value['city']];
    $sum_datas[$value['data_date'].$value['city']."sales_sum"] += $sales_sum[$value['city']];
    $sum_datas[$value['data_date'].$value['city']."profile_sum"] += $profile_sum[$value['city']];
    $sum_datas[$value['data_date'].$value['city']."verify_num_sum"] += $verify_num_sum[$value['city']];
	$sum_datas[$value['data_date'].$value['city']."verify_sum_sum"] += $verify_sum_sum[$value['city']];
	$sum_datas[$value['data_date'].$value['city']."verify_profile_sum"] += $verify_profile_sum[$value['city']];
	
}
$query_datas = DB::GetQueryResult($sql,false);
foreach( $query_datas as $key => $value ){
	$query_datas[$key]['goods_sum']=$sum_datas[$value['data_date'].$value['city']."goods_sum"];
	$query_datas[$key]['sales_sum']=$sum_datas[$value['data_date'].$value['city'].'sales_sum'];
	$query_datas[$key]['profile_sum']=$sum_datas[$value['data_date'].$value['city'].'profile_sum'];
	$query_datas[$key]['verify_num_sum']=$sum_datas[$value['data_date'].$value['city'].'verify_num_sum'];
	$query_datas[$key]['verify_sum_sum']=$sum_datas[$value['data_date'].$value['city'].'verify_sum_sum'];
	$query_datas[$key]['verify_profile_sum']=$sum_datas[$value['data_date'].$value['city'].'verify_profile_sum'];
}

$excel_name = "城市日累计".$date_from."-".$date_end;
export_excel($query_datas,$table_array,$data_field_array,$excel_name);