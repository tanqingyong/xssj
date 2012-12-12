<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'indicator_date' => '月份',
                        'region' => '大区',
                        'city' => '城市',
                        'pre_sale_amount' => '预测销售额',
                        'pre_positive_profile' => '预测正毛利',
                        'pre_lose_profile' =>'预测负毛利',
                        'pre_profile' => '预测毛利',
                        'pre_profile_rate' => '预测毛利率%',
                        'pre_advance_payment' => '预测预付款',
                      );
$sql = "select * from six_indicators,city where city.name = six_indicators.city ";
$month = trim($_GET['month']);
if(!$month){
    if(date('d')>=25){
        $month = date('Y').date('m')+1;
    }else{
        $month = date('Y').date('m');
    }
}
$condition .= " and indicator_date = '$month' "; 
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
$sql .= $condition;
$datas = DB::GetQueryResult($sql,false);
foreach($datas as $key=>$value){
	$datas[$key]["indicator_date"] = get_six_indicator_month($datas[$key]["indicator_date"]);
}
//get data type
$result = mysql_query($sql." limit 1");
$fields = mysql_num_fields($result);
$data_field_array = array();
for ($i=0; $i < $fields; $i++) {
     $type  = mysql_field_type($result, $i);
     $name  = mysql_field_name($result, $i);
     if($name == "indicator_date"){
         $data_field_array[$name] = "text";
     }else{
         $data_field_array[$name] = $type;
     }
}
$excel_name = "六大指标预测报表".$month;
export_excel($datas,$sumary_table,$data_field_array,$excel_name);