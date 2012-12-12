<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'month'=>'月份',
						'regionname'=>'大区',
						'city'=>'城市',
                        'month_goodsnum'=>'月销售量',
                        'month_money'=>'月销售额',
                        'month_profile'=>'月毛利',
                        'maoli_rate'=>'毛利率',
                        'early_goodsnum'=>'上旬销售量',
                        'early_money'=>'上旬销售额',
                        'early_profile'=>'上旬毛利',
						'mid_goodsnum'=>'中旬销售量',
                        'mid_money'=>'中旬销售额',
                        'mid_profile'=>'中旬毛利',
						'end_goodsnum'=>'下旬销售量',
                        'end_money'=>'下旬销售额',
                        'end_profile'=>'下旬毛利'
                      );
$sql = "select 
month,city,sum(month_goodsnum) as month_goodsnum,sum(month_money) as month_money,
sum(month_profile) as month_profile,sum(early_goodsnum) as early_goodsnum,sum(early_money) as early_money,
sum(early_profile) as early_profile,sum(mid_goodsnum) as mid_goodsnum,sum(mid_money) as mid_money,
sum(mid_profile) as mid_profile,sum(end_goodsnum) as end_goodsnum,sum(end_money) as end_money,
sum(end_profile) as end_profile,region.name as regionname
from month_sale_data msd left join city on msd.city= city.name left join region on region.id = city.area_id 
 where 1=1 ";

$monthstart = $_GET['monthstart'];
$monthend = $_GET['monthend'];
$filter_region = intval($_GET['region']);
$filter_city = intval($_GET['city']);

if($monthstart&&$monthend){

    if($monthstart==$monthend){
        $condition .= " and msd.month = '$monthstart'";
    }else{
        $condition .= " and msd.month between '$monthstart' and '$monthend' ";
    }
}else if( $monthstart && !$monthend ){
    $condition .= " and msd.month = '$monthstart'";
}else if( !$monthstart && $monthend ){
    $condition .= " and msd.month = '$monthend'";
}else{
    $monthstart = date('Y-01',time());
    $monthend = date('Y-m',time());
    $condition .= " and msd.month between '$monthstart' and '$monthend' ";
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

//$total_datas = DB::GetQueryResult($sql." group by msd.month,region.id,city.id",false);
$sql .= $condition." group by msd.month,region.id,city.id";


$datas = DB::GetQueryResult($sql,false);

foreach($datas as $key=>$data ){
	$datas[$key]['maoli_rate'] =  round($data['month_profile']/$data['month_money'],2);
}

foreach($datas as $key=>$data ){
	$mg += $datas[$key]['month_goodsnum'];
	$mm += $datas[$key]['month_money'];
	$mp += $datas[$key]['month_profile'];
	$eg += $datas[$key]['early_goodsnum'];
	$em += $datas[$key]['early_money'];
	$ep += $datas[$key]['early_profile'];
	$midg += $datas[$key]['mid_goodsnum'];
	$midm += $datas[$key]['mid_money'];
	$midp += $datas[$key]['mid_profile'];
	$endg += $datas[$key]['end_goodsnum'];
	$endm += $datas[$key]['end_money'];
	$endp += $datas[$key]['end_profile'];
}

$mr = round($mp/$mg,2)."%";
$test = array(
	'month'=>"合计",
	'regionname'=>"大区",
	'city'=>"城市",
	'month_goodsnum'=>"$mg",
	'month_money'=>"$mm",
	'month_profile'=>"$mp",
	'maoli_rate'=>"$mr",
	'early_goodsnum'=>"$eg",
	'early_money'=>"$em",
	'early_profile'=>"$ep",
	'mid_goodsnum'=>"$midg",
	'mid_money'=>"$midm",
	'mid_profile'=>"$midp",
	'end_goodsnum'=>"$endg",
	'end_money'=>"$endm",
	'end_profile'=>"$endp"
);
array_push($datas, $test);

$result = mysql_query($sql." limit 1");
$fields = mysql_num_fields($result);
$data_field_array = array();
for ($i=0; $i < $fields; $i++) {
     $type  = mysql_field_type($result, $i);
     $name  = mysql_field_name($result, $i);
     $data_field_array[$name] = $type;
}
$data_field_array['maoli_rate'] = 'string';
$excel_name = "按城市月销售数据".$monthstart."-".$monthend;  
export_excel($datas,$sumary_table,$data_field_array,$excel_name);

?>
    