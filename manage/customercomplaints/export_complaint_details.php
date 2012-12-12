<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'goods_id'=>'商品ID',
                        'goods_sname' => '商品名称',
                        'sales_count' => '销售数量',
                        'shop_price' => '商品单价',
                        'order_id' => '订单号',
                        'city_name' => '城市',
                        'first_type'=>'一级分类',
                        'second_type' =>'二级分类',
                        'registered_time' => '投诉时间',
                        'consult_content' => '投诉内容',
                        'consult_count' => '产品总投诉量',
                        'is_refund' => '是否退款',
                        'refund_type' => '退款类型',
                        'refund_num' => '退款数量',
                      );

$sql = "SELECT m.goods_id,m.goods_sname,m.sales_count,m.shop_price,m.order_id,m.city_name,m.first_type ,m.second_type,
       m.registered_time,m.consult_content,m.consult_count,CASE m.is_refund WHEN 0 THEN '是' ELSE '否' END is_refund,
       m.refund_type,m.refund_num
 FROM complaints_summary m  
 left join city n on n.name = m.city_name
WHERE 1 = 1  and  m.goods_id is not NULL  ";

$condition = "";
$filter_date_start = trim($_GET['startdate']);
$filter_date_end = trim($_GET['enddate']);
if($filter_date_start && $filter_date_end){
    $condition .= " and m.registered_time between '$filter_date_start' and '$filter_date_end' ";
}elseif($filter_date_start){
   $condition .= " and date(m.registered_time) = '$filter_date_start' ";
}elseif($filter_date_end){
   $condition .= " and date(m.registered_time) = '$filter_date_end' ";
}else{
	//确认需求是否要求默认当月1号到当天的数据
// 	$filter_date_start = date('Y-m-01',time());
// 	$filter_date_end = date('Y-m-t',time());
// 	$condition .= " and FROM_UNIXTIME(m.complaints_time) between '$filter_date_start' and '$filter_date_end' ";
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
$sql .= " order by m.registered_time";
error_log($sql);
$excel_name = "客服投诉报表".$date;  
export_excel($sql,$sumary_table,"",$excel_name);
?>
    