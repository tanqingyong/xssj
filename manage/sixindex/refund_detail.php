<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select
gr.date,gr.goods_id,gr.refund_bishu,sum(gr.refund_num) as 'refund_num',gr.refund_money as 'refund_money',gr.refund_profile as 'refund_profile',gr.refund_user,gr.city,gr.goods_name,gr.incity,gr.fromcity,gr.second_id,gr.second_name,gr.start_date,gr.end_date,gr.sale_price,gr.cost_price,gr.zhekou,gr.jiezhi_time,gr.biz_id,gr.biz_name,gr.salesman,gr.salesman_id,region.name as regionname,dim_goods.type1 from 
goods_refund gr left join city on gr.city = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gr.second_id where 1=1 ";
$sql_count = "select count(distinct concat(gr.goods_id,gr.city)) from goods_refund gr left join city on gr.city = city.name
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
		redirect( WEB_ROOT . "/manage/sixindex/refund_detail.php");
	}
	if($date_from==$date_end){
		$condition .= " and gr.date = '$date_from'";
	}else{
		$condition .= " and gr.date between '$date_from' and '$date_end' ";
	}
}else if( $date_from && !$date_end ){
	$condition .= " and gr.date = '$date_from'";
}else if( !$date_from && $date_end ){
	$condition .= " and gr.date = '$date_end'";
}else{
	$date_from = date('Y-m-01',time());
	$date_end = date('Y-m-t',time());
	$condition .= " and gr.date between '$date_from' and '$date_end' ";
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
	$condition .= " and gr.goods_id = $goods_id";
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
$sql .="  group by gr.goods_id,gr.city  limit $offset,$pagesize";
 $datas = DB::GetQueryResult($sql,false);
echo template('manage_refund_detail',
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