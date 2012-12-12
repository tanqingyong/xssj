<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select 
gc.id,gc.DATE,gc.goodsid,gc.goodsname,gc.incity,gc.fromcity,gc.salesman,gc.salesman_id
,gc.firstcategoryid,gc.firstcategoryname,gc.begintime,gc.endtime,gc.offordernum
,gc.offaddupordersale,gc.offorderusernum,gc.PV,gc.UV,gc.IP,gc.sale_price
,gc.cost_price,gc.zhekou,gc.jiezhi_time,gc.biz_id,gc.biz_name
,sum(gc.offorderproductnum) as offorderproductnum
,round(sum(gc.offordersale),2) as offordersale
,round(sum(gc.positive_profile),2) as positive_profile
,round(sum(gc.lose_profile),2) as lose_profile
,round(sum(gc.profile),2) as profile,
gc.incity as incity, gc.fromcity  as fromcity,region.name as regionname,dim_goods.type1 from goods_city_day gc 
left join city on  gc.incity  = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gc.firstcategoryid where sale_price>0 ";
$sql_count = "select goodsid from goods_city_day gc 
left join city on  gc.incity  = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gc.firstcategoryid where sale_price>0 ";
$date_from = $_GET['date_from'];
$date_end = $_GET['date_end'];
$goods_id = intval($_GET['goods_id']);
// case when gc.incity='全国' then gc.fromcity else gc.incity end as actual_city,region.name as regionname,dim_goods.type1 from goods_city_day gc
if($date_from&&$date_end){
//    if(date('m',strtotime($date_from))!=date('m',strtotime($date_end))||date('Y',strtotime($date_from))!=date('Y',strtotime($date_end))){
//        Session::Set('notice', '时间段不能跨月！');
//        redirect( WEB_ROOT . "/manage/sixindex/sales_detail.php");
//    }
    if( $date_from > $date_end ){
        Session::Set('notice', '开始时间不能比结束时间大！');
        redirect( WEB_ROOT . "/manage/sixindex/sales_detail.php");
    }
    if($date_from==$date_end){
        $condition .= " and gc.DATE = '$date_from'";
    }else{
        $condition .= " and gc.DATE between '$date_from' and '$date_end' ";
    }
}else if( $date_from && !$date_end ){
    $condition .= " and gc.DATE = '$date_from'";
}else if( !$date_from && $date_end ){
    $condition .= " and gc.DATE = '$date_end'";
}else{
    $date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $condition .= " and gc.DATE between '$date_from' and '$date_end' ";
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

if($goods_id){
	$condition .= " and gc.goodsid = $goods_id";
}
$cate=$_GET['cate'];
if($cate){
	$condition .= " and dim_goods.type1 ='$cate' ";
}

$sql .= $condition;
$sql_count .= $condition;
$sql_count .= " group by gc.goodsid,gc.incity";
$result = DB::Query($sql_count);
$count = mysql_num_rows($result);
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);
$sql .=" group by gc.goodsid,gc.incity  limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);

echo template('manage_sales_detail',
			array('datas'=>$datas, 
					'pagestring'=>$pagestring, 
					'date_from'=>$date_from,
					'date_end'=>$date_end, 
					'filter_region'=>$filter_region,
					'filter_city'=>$filter_city,
					'goods_id'=>$goods_id,
					'cate'=>$cate,
					'page_size'=>$page_size));
?>