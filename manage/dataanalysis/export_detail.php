<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'date'=>'日期',
                        'regionname' => '大区',
                        'incity' => '城市',
                        'goodsid' => '产品ID',
                        'goodsname' => '产品名称',
                        'type1' => '一级类目',
                        'firstcategoryname' => '二级类目',
                        'uv'=>'产品详情页访问数',
                        'ordernum' =>'下单数',
                        'orderproductnum' => '下单商品数量',
                        'offordernum' => '支付订单数',
                        'offorderproductnum' => '支付商品数量',
                        'offorderusernum' => '独立购买用户数',
                        'offordersale' => '销售金额',
                      );
//wait for data
$sql = "select goods_city_day.*,region.name as regionname,dim_goods.* from goods_city_day 
left join city on goods_city_day.incity = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = goods_city_day.firstcategoryid where 1=1  ";
$sql_summary = "select 
sum(goods_city_day.uv) as uv,
sum(goods_city_day.ordernum) as ordernum,
sum(goods_city_day.orderproductnum) as orderproductnum,
sum(goods_city_day.offordernum) as offordernum,
sum(goods_city_day.offorderproductnum) as offorderproductnum,
sum(goods_city_day.offorderusernum) as offorderusernum,
round(sum(goods_city_day.offordersale),2) as offordersale 
from goods_city_day 
left join city on  goods_city_day.incity = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = goods_city_day.firstcategoryid where 1=1  ";

$filter_goods = trim($_GET['filter_goods']);
if($filter_goods){
    $condition .= " and goods_city_day.goodsid = '$filter_goods'";
}

$filter_date_start = trim($_GET['stratdate']);
$filter_date_end = trim($_GET['enddate']);
if($filter_date_start && $filter_date_end){
	
    $condition .= " and goods_city_day.DATE between '$filter_date_start' and '$filter_date_end' ";
}elseif($filter_date_start){
    $condition .= " and goods_city_day.DATE = '$filter_date_start'  ";
}elseif($filter_date_end){
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

$sql .= $condition;
$sql .= " order by DATE,goodsid desc";
$sql_summary .= $condition;
$result = DB::Query($sql_summary);
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$summary_row[]= iconv('utf-8','gbk','汇总');
$summary_row[]= '';
$summary_row[]= '';
$summary_row[]= '';
$summary_row[]= '';
$summary_row[]= '';
$summary_row[]= '';
$summary_row[]= iconv('utf-8','gbk',$row['uv']);
$summary_row[]= iconv('utf-8','gbk',$row['ordernum']);
$summary_row[]= iconv('utf-8','gbk',$row['orderproductnum']);
$summary_row[]= iconv('utf-8','gbk',$row['offordernum']);
$summary_row[]= iconv('utf-8','gbk',$row['offorderproductnum']);
$summary_row[]= iconv('utf-8','gbk',$row['offorderusernum']);
$summary_row[]= iconv('utf-8','gbk',$row['offordersale']);

$excel_name = "流量详细".$date;
export_excel($sql,$sumary_table,$summary_row,$excel_name);

    