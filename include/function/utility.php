<?php
function get_city($ip = null) {
	$cities = option_category ( 'city', false, true );
	$ip = ($ip) ? $ip : Utility::GetRemoteIP ();
	$location = ip_location_youdao ( $ip );
	if ($location) {
		foreach ( $cities as $one ) {
			if (FALSE !== strpos ( $location, $one ['name'] )) {
				return $one;
			}
		}
	}
	return array ();
}

function ip_location_baidu($ip) {
	$u = "http://open.baidu.com/ipsearch/s?wd={$ip}&tn=baiduip";
	$r = mb_convert_encoding ( Utility::HttpRequest ( $u ), 'UTF-8', 'GBK' );
	preg_match ( '#来自：<b>(.+)</b>#Ui', $r, $m );
	return strval ( $m [1] );
}

function ip_location_youdao($ip) {
	$u = "http://www.youdao.com/smartresult-xml/search.s?type=ip&q={$ip}";
	$r = mb_convert_encoding ( Utility::HttpRequest ( $u ), 'UTF-8', 'GBK' );
	preg_match ( "#<location>(.+)</location>#Ui", $r, $m );
	return strval ( $m [1] );
}

/**
 * 取得订单相关状态的显示
 * @param string $type 状态类型,order_status,payment_status,shipping_status
 * @param int $value
 * @return string
 */
function get_status_display($type, $value) {
	$status_arr = array ();
	$status_arr ['order_status'] = array (0 => '未确认', 1 => '已确认', 2 => '已取消', 3 => '无效', 4 => '退货', 5 => '已分单', 6 => '部分分单' );
	$status_arr ['payment_status'] = array (0 => '未付款', 1 => '付款中', 2 => '已付款', 3 => '已退款', 4 => '已冻结(退款中)' );
	$status_arr ['shipping_status'] = array (0 => '未发货', 1 => '已发货', 2 => '已收货', 3 => '备货中', 4 => '已发货(部分商品)', 4 => '发货中(处理分单)' );
	return $status_arr [$type] [$value];
}

/**
 * 取得差异类型的显示
 * @param int $value
 * @return string
 */
function get_diff_type_display($value) {
	if ($_SESSION ["diff_type"]) {
		$diff_type = $_SESSION ["diff_type"];
	} else {
		$diff_type = data_row_to_array ( DB::GetTableRows ( "DIFFERENCE_TYPE" ), "type_id", "type_name" );
		Session::Set ( "diff_type", $diff_type );
	}
	return $diff_type [$value];
}
/**
 * 取得是否为实物的显示
 * @param int $value
 * @return string
 */
function get_real_display($value) {
	$is_real_arr = array (0 => '非实物', 1 => '实物' );
	return $is_real_arr [$value];
}

/**
 * 取得日志类型的显示
 * @param int $value
 * @return string
 */
function get_log_type_display($value) {
	$is_real_arr = array (1 => '新建', 2 => '修改', 3 => '删除' );
	return $is_real_arr [$value];
}

function get_diff_mark_display($value) {
	$is_real_arr = array (1 => '有差异');
	if($is_real_arr [$value]){
		return $is_real_arr [$value];
	}
	return "无差异";
}

/**
 * 取得配送显示
 * @param int $value
 * @return string
 */
function get_delive_type_display($value) {
	$delive_type = data_row_to_array ( DB::GetTableRows ( "SHIPPING" ), "shipping_id", "shipping_name" );
	return $delive_type [$value];
}

/**
 * 取得支付方式显示
 * @param int $value
 * @return string
 */
function get_pay_type_display($value) {
	$pay_type = data_row_to_array ( DB::GetTableRows ( "PAYMENT" ), "pay_id", "pay_desc" );
	return $pay_type [$value];
}

function mail_zd($email) {
	global $option_mail;
	if (! Utility::ValidEmail ( $email ))
		return false;
	preg_match ( '#@(.+)$#', $email, $m );
	$suffix = strtolower ( $m [1] );
	return $option_mail [$suffix];
}

function nanooption($string) {
	if (preg_match_all ( '#{(.+)}#U', $string, $m )) {
		return $m [1];
	}
	return array ();
}

global $option_year;
$option_year = array ('2010' => '2010', '2011' => '2011', '2012' => '2012', '2013' => '2013', '2014' => '2014', '2015' => '2015' );

global $option_month;
$option_month = array ('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12' );

global $option_year_month;
$option_year_month = array ('2010-01' => '2010-01','2011-01' => '2011-01','2011-02' => '2011-02','2011-03' => '2011-03', '2011-04' => '2011-04', 
'2011-05' => '2011-05', '2011-06' => '2011-06', '2011-07' => '2011-07', '2011-08' => '2011-08', '2011-09' => '2011-09',
 '2011-10' => '2011-10', '2011-11' => '2011-11', '2011-12' => '2011-12', '2012-01' => '2012-01' , '2012-02' => '2012-02' , 
 '2012-03' => '2012-03' , '2012-04' => '2012-04' , '2012-05' => '2012-05' , '2012-06' => '2012-06' , '2012-07' => '2012-07' , 
 '2012-08' => '2012-08' , '2012-09' => '2012-09' , '2012-10' => '2012-10', '2012-11' => '2012-11', '2012-12' => '2012-12' ,
 '2013-01' => '2013-01' , '2013-02' => '2013-02' , '2013-03' => '2013-03', '2013-04' => '2013-04', '2013-05' => '2013-05' ,
'2013-06' => '2013-06' , '2013-07' => '2013-07' , '2013-08' => '2013-08', '2013-09' => '2013-09', '2013-10' => '2013-10' 
 );


function get_last_month_and_year() {
	$current_month = idate ( 'm' );
	$current_year = idate ( 'Y' );
	$last_month = $current_month - 1;
	$last_month_of_year = $current_year;
	if ($last_month == 0) {
		$last_month = 12;
		$last_month_of_year = $current_year - 1;
	}
	return array ('year' => $last_month_of_year, 'month' => $last_month );
}

function generate_year_selector($element_name = 'select_year', $year = 2011) {
	global $option_year;
	$result = "<select name='" . $element_name . "' class='f-city'> ";
	$result .= Utility::Option ( $option_year, $year );
	$result .= "</select>";
	
	return $result;
}

function generate_month_selector($element_name = 'select_month', $month = 1) {
	global $option_month;
	$result = "<select name='" . $element_name . "' class='f-city'> ";
	$result .= Utility::Option ( $option_month, $month );
	$result .= "</select>";
	
	return $result;
}

function get_alipay_data_table_name($timestamp) {
	$year = idate ( "Y", $timestamp );
	$month = idate ( "m", $timestamp );
	$month = $month < 10 ? "0" . $month : $month;
	return "alipay_data_" . $year . $month;
}

function get_field_display($name, $value) {
	$field_array = array ("city_id" => get_city_display ( $value ), "pay_time" => date ( 'Y-m-d H:i:s', $value ), "difference_mark" => get_diff_mark_display ( $value ), "difference_type" => get_diff_type_display ( $value ) );

	if (array_key_exists ( $name, $field_array )) {
		return $field_array [$name];
	}
	return $value;
}
/**
 * 取得城市名称的显示
 * @param int $value
 * @return string
 */
function get_city_display($value) {
	if ($_SESSION ["city"]) {
		$city = $_SESSION ["city"];
	} else {
		$city = data_row_to_array ( DB::GetTableRows ( "CITY" ), "city_id", "city_name" );
		Session::Set ( "city", $city );
	}
	return $city [$value];
	return "";
}

/**
 * 取得属性名显示
 * @param int $name
 * @return string
 */
function get_name_display($name) {
	$attr_name = array ("goods_number" => "商品数量", "goods_price" => "商品价格", "goods_amount" => "销售金额", "zhifubao_fee" => "支付宝付款", "yibao_fee" => "易宝付款", "caifutong_fee" => "财付通付款", "wangyinzaixian_fee" => "网银在线付款", "kuaiqian_fee" => "快钱付款", "shoujizhifu_fee" => "手机支付付款", "yue_fee" => "余额支付", "shipping_fee" => "运费", "cash_fee" => "现金支付", "difference_fee" => "差异金额", "difference_mark" => "差异标识", "difference_type" => "差异类型", "mark" => "备注" );
	if (array_key_exists ( $name, $attr_name )) {
		return $attr_name [$name];
	}
	return $name;
}

function data_row_to_array($array = array(), $c1, $c2) {
	$result = array ();
	foreach ( $array as $key => $row ) {
		$result [$row [$c1]] = $row [$c2];
	}
	return $result;
}


function get_six_indicator_month($string_date){
	return substr($string_date,0,4)."-".substr($string_date,4,2);
}

function get_filename_for_http_header($var){
    if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")){
        return header('Content-Disposition: attachment; filename="'.urlencode($var).'"');
    }else{
        return header('Content-Disposition: attachment; filename="'.$var.'"');
    }
}


/*
 * 根据一级菜单获取二级菜单下拉选项
 * 
 */
function get_second_menu_by_parent_id($parent_id) {
	$menu_array = array();
	$sql_menu = "SELECT id,menu_name FROM menu WHERE menu_grade = 2 AND parent_id = $parent_id";
	$menus = DB::GetQueryResult($sql_menu,false);
	foreach($menus as $menu){
		$menu_array[$menu['id']] = $menu['menu_name'];
	}
	return $menu_array;
}

/**
 * 取得在线状态
 * @param int $value
 * @return string
 */
function get_online_status($value) {
	$is_real_arr = array (0 => '离线', 1 => '在线' );
	return $is_real_arr [$value];
}

/**
 * 取得在线状态
 * @param int $value
 * @return string
 */
function get_all_online_status() {
	$is_real_arr = array (2 => '离线', 1 => '在线' );
	return $is_real_arr;
}