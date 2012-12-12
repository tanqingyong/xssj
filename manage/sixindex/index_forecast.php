<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
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
$data_summary = array("pre_sale_amount"=>0.00,
					  "pre_positive_profile"=>0.00,
					  "pre_lose_profile"=>0.00,
					  "pre_profile"=>0.00,
					  "pre_advance_payment"=>0.00,
					  "profile_rate"=>0.00);
foreach($datas as $key=>$data){
    $data_summary["pre_sale_amount"] += $data['pre_sale_amount'];
    $data_summary["pre_positive_profile"] += $data['pre_positive_profile'];
    $data_summary["pre_lose_profile"] += $data['pre_lose_profile'];
    $data_summary["pre_profile"] += $data['pre_profile'];
    $data_summary["pre_advance_payment"] += $data['pre_advance_payment'];
}
$data_summary["profile_rate"] = round(($data_summary["pre_profile"]/$data_summary["pre_sale_amount"])*10000)/100;
$sum_string = "销售额:<span>{$data_summary["pre_sale_amount"]}</span>,正毛利:<span>{$data_summary["pre_positive_profile"]}</span>,负毛利:<span>{$data_summary["pre_lose_profile"]}</span>,
                                        毛利:<span>{$data_summary["pre_profile"]}</span>,毛利率为:<span>{$data_summary["profile_rate"]}%</span>,预付款:<span>{$data_summary["pre_advance_payment"]}</span>。";
echo template ( 'manage_index_forecast', 
                array ( 'datas'=>$datas,'month'=>$month,'filter_region'=>$filter_region,'filter_city'=>$filter_city,'sum_string'=>$sum_string) );
?>