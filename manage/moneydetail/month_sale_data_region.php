<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select 
month,city,sum(month_goodsnum) as month_goodsnum,sum(month_money) as month_money,
sum(month_profile) as month_profile,sum(early_goodsnum) as early_goodsnum,sum(early_money) as early_money,
sum(early_profile) as early_profile,sum(mid_goodsnum) as mid_goodsnum,sum(mid_money) as mid_money,
sum(mid_profile) as mid_profile,sum(end_goodsnum) as end_goodsnum,sum(end_money) as end_money,
sum(end_profile) as end_profile,region.name as regionname
from month_sale_data msd left join city on msd.city= city.name left join region on region.id = city.area_id 
 where 1=1 ";
$sql_count = "select city 
from month_sale_data msd left join city on msd.city= city.name left join region on region.id = city.area_id 
 where 1=1 ";
$monthstart = $_GET['monthstart'];
$monthend = $_GET['monthend'];
$filter_region = intval($_GET['region']);
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
	 if($filter_region){
	    $cities = array();
	    $cities = get_cities_by_region_id($filter_region);
	    $cities = array_flip($cities);
	    $city_str = '('.implode(',',$cities).')';
	    $condition .= " and city.id in $city_str ";
	}
}else if($_SESSION['user_grade']==2){
	$filter_region = intval($_GET['region']);
	if($filter_region){
	    $cities = array();
	    $cities = get_cities_by_region_id($_SESSION['area_id']);
	    $cities = array_flip($cities);
	    $city_str = '('.implode(',',$cities).')';
	    $condition .= " and city.id in $city_str ";
	}
}

if($filter_region){
	$condition .= " and region.id = $filter_region";
}
$sql .= $condition;
$sql_count .= $condition;
$sql_count .= " group by msd.month,region.id";
$result = DB::Query($sql_count);
$count = mysql_num_rows($result);
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);
$sql .=" group by msd.month,region.id order by msd.month,region.id desc limit $offset,$pagesize";

$datas = DB::GetQueryResult($sql,false);

echo template('manage_month_region',array('datas'=>$datas, 'pagestring'=>$pagestring, 'monthstart'=>$monthstart,'monthend'=>$monthend, 'filter_region'=>$filter_region,'page_size'=>$page_size));
?>