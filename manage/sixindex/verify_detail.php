<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select 
gv.date,gv.goods_id,sum(gv.verify_num) as 'verify_num',sum(gv.verify_money) as 'verify_money',sum(gv.verify_profile) as 'verify_profile',
gv.city,gv.goods_name,gv.incity,gv.fromcity,gv.second_id,gv.second_name,gv.start_date,gv.end_date,
sum(gv.order_num) as 'order_num',sum(gv.order_goods_num) as 'order_goods_num',sum(gv.order_money) as 'order_money',sum(gv.order_user) as 'order_user',
sum(gv.suc_order) as 'suc_order',sum(gv.suc_goods_num) as 'suc_goods_num',
sum(gv.suc_money) as 'suc_money',sum(gv.suc_user) as 'suc_user',gv.sale_price,gv.cost_price,sum(gv.profile) as 'profile',
sum(gv.positive_profile) as 'positive_profile',sum(gv.lose_profile) as 'lose_profile',gv.zhekou,gv.jiezhi_time,gv.biz_id,gv.biz_name,gv.salesman,gv.salesman_id,

region.name as regionname,dim_goods.type1 from 
goods_verify gv left join city on gv.city = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gv.second_id where 1=1 ";
$sql_count = "select count(distinct gv.goods_id) from goods_verify gv left join city on gv.city = city.name 
left join region on region.id = city.area_id  where 1=1  ";
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
        $condition .= " and gv.date = '$date_from'";
    }else{
        $condition .= " and gv.date between '$date_from' and '$date_end' ";
    }
}else if( $date_from && !$date_end ){
    $condition .= " and gv.date = '$date_from'";
}else if( !$date_from && $date_end ){
    $condition .= " and gv.date = '$date_end'";
}else{
    $date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $condition .= " and gv.date between '$date_from' and '$date_end' ";
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
	$condition .= " and gv.goods_id = $goods_id";
}
$cate=$_GET['cate'];
if($cate){
	$condition .= " and dim_goods.type1 ='$cate' ";
}

$sql .= $condition;
$sql_count .= $condition;

$result = DB::query($sql_count);
 $count = mysql_fetch_row($result);
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count[0], $page_size);
$sql .="  group by gv.goods_id,gv.city  limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);

echo template('manage_verify_detail',
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