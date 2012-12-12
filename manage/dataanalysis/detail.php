<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select goods_city_day.*,region.name as regionname,dim_goods.* from goods_city_day 
left join city on  goods_city_day.incity = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = goods_city_day.firstcategoryid where 1=1 ";
$sql_count = "select count(1) as count from goods_city_day 
left join city on  goods_city_day.incity = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = goods_city_day.firstcategoryid where 1=1 ";

$condition = '';
$filter_goods = trim($_GET['filter_goods']);
if($filter_goods){
    $condition .= " and goods_city_day.goodsid = '$filter_goods'";
}

$filter_date_start = trim($_GET['stratdate']);
$filter_date_end = trim($_GET['enddate']);
if($filter_date_start && $filter_date_end){
    $condition .= " and goods_city_day.DATE between '$filter_date_start' and '$filter_date_end' ";
}else if($filter_date_start){
    $condition .= " and goods_city_day.DATE = '$filter_date_start'  ";
}else if($filter_date_end){
    $condition .= " and goods_city_day.DATE ='$filter_date_end' ";
}else{
    $filter_date_start = date('Y-m-01',time());
    $filter_date_end = date('Y-m-t',time());
    $condition .= " and goods_city_day.DATE between '$filter_date_start' and '$filter_date_end' ";
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

$cate=$_GET['cate'];
if($cate){
	$condition .= " and dim_goods.type1 ='$cate' ";
}
$result = DB::GetQueryResult($sql_count . $condition,true);;
$count = $result['count'];
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);
$sql .= $condition;
$sql .=" order by DATE,goodsid desc limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);

echo template ( 'manage_dataanalysis_detail', array ( 'datas'=>$datas,'pagestring'=>$pagestring,'filter_goods' => $filter_goods, 'filter_date_start'=>$filter_date_start,'filter_date_end'=>$filter_date_end,'filter_region'=>$filter_region,'filter_city'=>$filter_city,'cate'=>$cate,'page_size'=>$page_size) );

?>