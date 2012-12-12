<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
//权限处理
if($_SESSION['user_grade']<=2){
	echo template('manage_no_right');die();
}
$table_array =  array(  'data_date' => '日期',
                        'goods_day' => '销量',
                        'sales_day' => '销售额',
                        'profile_day' => '毛利',
                        'profile_rate' => '毛利率',
                        'goods_sum' => '累计销量',
                        'sales_sum' => '累计销售额',
                        'profile_sum' =>'累计毛利',
						'verify_num'=>'验证量',
						'verify_sum'=>'验证额',
						'verify_profile'=>'验证毛利',
						'verify_num_sum'=>'累计验证量',
						'verify_sum_sum'=>'累计验证额',
						'verify_profile_sum'=>'累计验证毛利'
                      );
                      
$sql = "select data_date,round(sum(suc_total_price),2) as sales_day,
round(sum(suc_goods_num),2) as goods_day,round(sum(profile),2) as profile_day,
 round(sum(profile)*100/sum(suc_total_price),2) as profile_rate ,
 sum(verify_num) as verify_num,sum(verify_sum) as verify_sum,sum(verify_profile) as verify_profile
from buss_city 
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
$total_datas = DB::GetQueryResult($sql." and data_date between '$date_start' and '$date_end' group by data_date order by data_date ",false);
$sql .= $condition." group by data_date order by data_date";
$query_datas = DB::GetQueryResult($sql,false);
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
foreach( $query_datas as $key => $value ){
	$query_datas[$key]['goods_sum']=$sum_datas[$value['data_date'].'goods_sum'];
	$query_datas[$key]['sales_sum']=$sum_datas[$value['data_date'].'sales_sum'];
	$query_datas[$key]['profile_sum']=$sum_datas[$value['data_date'].'profile_sum'];
	$query_datas[$key]['verify_num_sum']=$sum_datas[$value['data_date'].'verify_num_sum'];
	$query_datas[$key]['verify_sum_sum']=$sum_datas[$value['data_date'].'verify_sum_sum'];
	$query_datas[$key]['verify_profile_sum']=$sum_datas[$value['data_date'].'verify_profile_sum'];
}
$excel_name = "全国日累计".$date_from."-".$date_end;

export_excel($query_datas,$table_array,$data_field_array,$excel_name);