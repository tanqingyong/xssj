<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
//权限处理
if($_SESSION['user_grade']<=2){
	echo template('manage_no_right');die();
}

$sql = "select data_date,round(sum(suc_total_price),2) as sales_day,
round(sum(suc_goods_num),2) as goods_day,round(sum(profile),2) as profile_day, 
round(sum(profile)*100/sum(suc_total_price),2) as profile_rate ,
sum(verify_num) as verify_num,sum(verify_sum) as verify_sum,sum(verify_profile) as verify_profile
from buss_city 
left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1 ";
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
        redirect( WEB_ROOT . "/manage/moneydetail/nationwide_day.php");
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
$date_start = date('Y-m-01',strtotime($date_from));
$total_datas = DB::GetQueryResult($sql." and data_date between '$date_start' and '$date_end' group by data_date order by data_date",false);
$sql .= $condition." group by data_date order by data_date";
$sql_count .= $condition." group by data_date ) as buss_day";;
$result = DB::GetQueryResult($sql_count,true);
$count = $result['count'];
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):50;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);
$sum_datas = array();
foreach( $total_datas as $key => $value ){
    $sales_goods += $value['goods_day']; 
	$sales_sum += $value['sales_day'];
	$profile_sum += $value['profile_day'];
	$verify_num_sum += $value['verify_num'];//累计验证量
	$verify_sum_sum += $value['verify_sum'];//累计验证额
	$verify_profile_sum += $value['verify_profile'];//累计验证毛利
	
	$sum_datas[$value['data_date']."goods_sum"] += $sales_goods;
	$sum_datas[$value['data_date']."sales_sum"] += $sales_sum;
	$sum_datas[$value['data_date']."profile_sum"] += $profile_sum;
	$sum_datas[$value['data_date']."verify_num_sum"] += $verify_num_sum;
	$sum_datas[$value['data_date']."verify_sum_sum"] += $verify_sum_sum;
	$sum_datas[$value['data_date']."verify_profile_sum"] += $verify_profile_sum;
}
$sql .="  asc limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);

echo template ( 'manage_moneydetail_nationwide_day', 
                array ( 'datas'=>$datas,'pagestring'=>$pagestring,'date_from'=>$date_from, 'date_end'=>$date_end, 'page_size'=>$page_size, 'sum_datas' => $sum_datas) );
