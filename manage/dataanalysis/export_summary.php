<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'regionname'=>'大区',
                        'city'=>'城市',
                        'uv'=>'uv',
                        'ip'=>'ip',
                        'pv'=>'pv',
                        'data_date'=>'日期',
                        'suc_total_price'=>'销售金额',
                        'price_uv'=>'销售额/uv'
                      );
//wait for data
$sql = "select buss_city.*,region.name as regionname from buss_city left join city on buss_city.city = city.name 
left join region on region.id = city.area_id where 1=1 ";
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
        $sql .= "and city.id = $filter_city ";
    }else if($filter_region){
        $cities = array();
        $cities = get_cities_by_region_id($filter_region);
        $cities = array_flip($cities);
        $city_str = '('.implode(',',$cities).')';
        $sql .= " and city.id in $city_str ";
    }
}else if($_SESSION['user_grade']==2){
    $filter_city = intval($_GET['city']);
    if($filter_city){
        $sql .= "and city.id = $filter_city ";
    }else{
        $cities = array();
        $cities = get_cities_by_region_id($_SESSION['area_id']);
        $cities = array_flip($cities);
        $city_str = '('.implode(',',$cities).')';
        $sql .= " and city.id in $city_str ";
    }
}else{
    $sql .= " and city.id = {$_SESSION['city_id']} ";
}
    
$datas = DB::GetQueryResult ( $sql, false ); 
foreach($datas as $key=>$data ){
	$datas[$key]['price_uv'] = round($data['suc_total_price']*100/$data['uv'])/100;
}
$result = mysql_query($sql." limit 1");
$fields = mysql_num_fields($result);
$data_field_array = array();
for ($i=0; $i < $fields; $i++) {
     $type  = mysql_field_type($result, $i);
     $name  = mysql_field_name($result, $i);
     $data_field_array[$name] = $type;
}
$data_field_array['price_uv'] = 'int';
$excel_name = "流量统计".$date;  
export_excel($datas,$sumary_table,$data_field_array,$excel_name);
?>
    