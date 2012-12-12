<?php
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/app.php');

need_login ();
$sql = "SELECT m.goods_id,m.goods_sname,m.sales_count,m.shop_price,m.order_id,m.city_name,m.first_type ,m.second_type,
       m.registered_time,m.consult_content,m.consult_count,CASE m.is_refund WHEN 0 THEN '是' ELSE '否' END is_refund,
       m.refund_type,m.refund_num
 FROM complaints_summary m 
 left join city n on n.name = m.city_name
WHERE 1 = 1  and  m.goods_id is not NULL  ";
$sql_count = "SELECT count(1) as count FROM  complaints_summary m 
left join city n on n.name = m.city_name
 WHERE 1 = 1
and  m.goods_id is not NULL  ";

$condition = "";
$filter_date_start = trim ( $_GET ['startdate'] );
$filter_date_end = trim ( $_GET ['enddate'] );
if ($filter_date_start && $filter_date_end) {
	$condition .= " and m.registered_time between '$filter_date_start' and '$filter_date_end' ";
} elseif ($filter_date_start) {
	$condition .= " and date(m.registered_time) = '$filter_date_start' ";
} elseif ($filter_date_end) {
	$condition .= " and date(m.registered_time) = '$filter_date_end' ";
} else {
	// 确认需求是否要求默认当月1号到当天的数据
	// $filter_date_start = date('Y-m-01',time());
	// $filter_date_end = date('Y-m-t',time());
	// $condition .= " and FROM_UNIXTIME(m.complaints_time) between
	// '$filter_date_start' and '$filter_date_end' ";
}
// 权限处理
if ($_SESSION ['user_grade'] > 2) {
	$filter_region = intval ( $_GET ['region'] );
	$filter_city = intval ( $_GET ['city'] );
	if ($filter_city) {
		$condition .= "and n.id = $filter_city  ";
	} else if ($filter_region) {
		$cities = array ();
		$cities = get_cities_by_region_id ( $filter_region );
		$cities = array_flip ( $cities );
		$city_str = '(' . implode ( ',', $cities ) . ')';
		$condition .= " and n.id in $city_str ";
	}
} else if ($_SESSION ['user_grade'] == 2) {
	$filter_city = intval ( $_GET ['city'] );
	if ($filter_city) {
		$condition .= "and n.id = $filter_city  ";
	} else {
		$cities = array ();
		$cities = get_cities_by_region_id ( $_SESSION ['area_id'] );
		$cities = array_flip ( $cities );
		$city_str = '(' . implode ( ',', $cities ) . ')';
		$condition .= " and n.id in $city_str  ";
	}
} else {
	$condition .= " and n.id = {$_SESSION['city_id']}  ";
}
$sql .= $condition;
$sql_count .= $condition;
$result = DB::GetQueryResult ( $sql_count, true );
$count = $result ['count'];
$page_size = $_GET ['pagesize'] ? intval ( $_GET ['pagesize'] ) : 10;
list ( $pagesize, $offset, $pagestring ) = pagestring ( $count, $page_size );

$sql .= "order by m.registered_time desc limit $offset,$pagesize";
$datas = DB::GetQueryResult ( $sql, false );
if (Session::Get ( 'surplus_time' ) <= 10 && Session::Get ( 'surplus_time' ) > 0) {
	Session::Set ( 'notice', "你账号还有" . Session::Get ( 'surplus_time' ) . "天将被系统自动停用,为了避免到时登录不了系统，建议你现在就去OA填写<font color=\"red\">数据系统权限审批单</font>提交申请" );
}
Session::Set ( 'surplus_time', '' );
echo template ( 'manage_complaint_details', array ('datas' => $datas, 'pagestring' => $pagestring, 'filter_date_start' => $filter_date_start, 'filter_date_end' => $filter_date_end, 'filter_region' => $filter_region, 'filter_city' => $filter_city, 'page_size' => $page_size ) );

?>